<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Composition;
use App\Entity\Favourite;
use App\Entity\Rates;
use App\Entity\User;
use App\Form\CommentType;
use App\Form\CreateCompositionType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class CompositionController extends AbstractController
{
    #[Route('/composition/{id<\d+>?1}', name: 'composition')]
    public function composition(int $id, Request $request) :Response
    {
        $user = $this->getUser();
        $composition = $this->getDoctrine()->getRepository(Composition::class)->find($id);

        if (!$composition) {
            $session = new Session();
            $session->getFlashBag()->add('error', 'Composition not found');
            return $this->redirectToRoute('profile');
        }

        $rates = $this->getDoctrine()->getRepository(Rates::class)->getRatesForComposition($composition);

        $compositionAverageRate = 0;

        if ($rates) {
            foreach ($rates as $item) {
                $compositionAverageRate += $item['rate'];
            }
            $countRates = count($rates);
            $compositionAverageRate /= $countRates;
        }

        $rate = $this->getDoctrine()->getRepository(Rates::class)->getUserRateForComposition($user, $composition);
        is_null($rate)?$rate['rate'] = 0:'';

        $comments = $this->getDoctrine()->getRepository(Comment::class)->getCommentsForComposition($id);

        $author = $this->getDoctrine()->getRepository(User::class)->find($composition->getUser());

        $isFavourite = $this->getDoctrine()->getRepository(Favourite::class)->isFavouriteCompositionForUser($user, $composition);

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment = $form->getData();
            $comment->setCreatedAt(new \DateTime());
            $comment->setUser($user);
            $comment->setComposition($composition);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('composition', ['id' => $id]);
        }

        return $this->render('composition/index.html.twig', [
            'composition' => $composition,
            'comments' => $comments,
            'author' => $author,
            'form' => $form->createView(),
            'rate' => $rate,
            'composition_average_rate' => $compositionAverageRate,
            'is_favourite' => $isFavourite
        ]);
    }

    #[Route('/composition/create', name: 'composition_create')]
    public function createComposition(Request $request) :Response
    {
        $composition = new Composition();
        $form = $this->createForm(CreateCompositionType::class, $composition);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $composition->setUser($this->getUser());
            $composition = $form->getData();
            $composition->setUpdatedAt(new \DateTime());

            $em = $this->getDoctrine()->getManager();
            $em->persist($composition);
            $em->flush();

            $session = new Session();
            $session->getFlashBag()->add('success', 'Composition created successfully');

            return $this->redirectToRoute('profile');
        }

        return $this->render('composition/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/composition/add-to-favourite/{id<\d+>?}', name: 'composition_add_to_favourite')]
    public function addToFavourite(int $id) :Response
    {
        $composition = $this->getDoctrine()->getRepository(Composition::class)->find($id);

        if (!$composition) {
            $session = new Session();
            $session->getFlashBag()->add('success', 'Composition not found');
            return $this->redirectToRoute('profile');
        }

        $user = $this->getUser();

        $checkFavourite = $this->getDoctrine()->getRepository(Favourite::class)->isFavouriteCompositionForUser($user, $composition);

        if ($checkFavourite) {
            return $this->redirectToRoute('favourites');
        }

        $favourite = new Favourite();
        $favourite->setUser($user);
        $favourite->setComposition($composition);
        $em = $this->getDoctrine()->getManager();
        $em->persist($favourite);
        $em->flush();

        return $this->redirectToRoute('favourites');
    }

    #[Route('/composition/delete-from-favourite/{id<\d+>?}', name: 'composition_delete_from_favourite')]
    public function deleteFromFavourite(int $id) :Response
    {
        $composition = $this->getDoctrine()->getRepository(Composition::class)->find($id);

        if (!$composition) {
            $session = new Session();
            $session->getFlashBag()->add('success', 'Composition not found');
            return $this->redirectToRoute('profile');
        }

        $user = $this->getUser();

        $checkFavourite = $this->getDoctrine()->getRepository(Favourite::class)->isFavouriteCompositionForUser($user, $composition);

        if (!$checkFavourite) {
            return $this->redirectToRoute('favourites');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($checkFavourite);
        $em->flush();

        return $this->redirectToRoute('favourites');
    }

    #[Route('/composition/delete/{id<\d+>?}', name: 'composition_delete')]
    public function deleteComposition(int $id) :Response
    {
        $composition = $this->getDoctrine()->getRepository(Composition::class)->find($id);

        if (!$composition) {
            $session = new Session();
            $session->getFlashBag()->add('error', 'Composition not found');
            return $this->redirectToRoute('profile');
        }

        $user = $this->getUser();

        if ($composition->getUser() !== $user) {
            return $this->redirectToRoute('profile');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($composition);
        $em->flush();
        $session = new Session();
        $session->getFlashBag()->add('success', 'Composition deleted successfully');

        return $this->redirectToRoute('profile');
    }

    #[Route('/composition/edit/{id<\d+>?1}', name: 'composition_edit')]
    public function editComposition(int $id, Request $request) :Response
    {
        $composition = $this->getDoctrine()->getRepository(Composition::class)->find($id);

        if (!$composition) {
            $session = new Session();
            $session->getFlashBag()->add('error', 'Composition not found');
            return $this->redirectToRoute('profile');
        }

        $user = $this->getUser();

        if ($composition->getUser() !== $user) {
            return $this->redirectToRoute('profile');
        }

        $form = $this->createForm(CreateCompositionType::class, $composition);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $composition = $form->getData();
            $composition->setUpdatedAt(new \DateTime());

            $em = $this->getDoctrine()->getManager();
            $em->persist($composition);
            $em->flush();

            $session = new Session();
            $session->getFlashBag()->add('success', 'Composition edited successfully');

            return $this->redirectToRoute('composition', ['id' => $composition->getId()]);
        }

        return $this->render('composition/edit.html.twig', [
            'composition' => $composition,
            'form' => $form->createView()
        ]);
    }

    #[Route('composition/rate/{id<\d+>?}/{rate<\d+>?}', name: 'composition_rate')]
    public function rate(int $id, int $rate) :Response
    {
        $em = $this->getDoctrine()->getManager();
        $composition = $em->getRepository(Composition::class)->find($id);
        $user = $this->getUser();
        $checkRate = $this->getDoctrine()->getRepository(Rates::class)->checkRateByUserForComposition($user, $composition);
        if (!$checkRate) {
            $checkRate = new Rates();
            $checkRate->setUser($user);
            $checkRate->setComposition($composition);
        }
        $checkRate->setRate($rate);
        $em->persist($checkRate);
        $em->flush();

        return $this->redirectToRoute('composition', ['id' => $id]);
    }
}
