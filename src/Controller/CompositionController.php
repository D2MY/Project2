<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Composition;
use App\Repository\ChapterRepository;
use App\Repository\CommentRepository;
use App\Repository\CompositionRepository;
use App\Repository\FavouriteRepository;
use App\Repository\RatesRepository;
use App\Repository\UserRepository;
use App\Security\Voter\CustomVoter;
use App\Service\CommentService;
use App\Service\CompositionService;
use App\Entity\Favourite;
use App\Entity\Rates;
use App\Form\CommentType;
use App\Form\CreateCompositionType;
use App\Service\RatesService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class CompositionController extends AbstractController
{
    protected CompositionService $compositionService;
    protected CompositionRepository $compositionRepository;
    protected RatesRepository $ratesRepository;
    protected UserRepository $userRepository;
    protected CommentRepository $commentRepository;
    protected FavouriteRepository $favouriteRepository;
    protected ChapterRepository $chapterRepository;
    protected RatesService $ratesService;
    protected CommentService $commentService;
    protected EntityManagerInterface $em;

    public function __construct(CompositionService $compositionService, CompositionRepository $compositionRepository, RatesRepository $ratesRepository,
                                UserRepository $userRepository, CommentRepository $commentRepository, FavouriteRepository $favouriteRepository,
                                RatesService $ratesService, CommentService $commentService, EntityManagerInterface $em, ChapterRepository $chapterRepository)
    {
        $this->em = $em;
        $this->compositionService = $compositionService;
        $this->compositionRepository = $compositionRepository;
        $this->ratesRepository = $ratesRepository;
        $this->userRepository = $userRepository;
        $this->commentRepository = $commentRepository;
        $this->favouriteRepository = $favouriteRepository;
        $this->chapterRepository = $chapterRepository;
        $this->ratesService = $ratesService;
        $this->commentService = $commentService;
    }

    #[Route('/composition/{id<\d+>?1}', name: 'composition')]
    public function composition(int $id, Request $request, PaginatorInterface $paginator) :Response
    {
        $composition = $this->compositionRepository->find($id);
        if (!$composition) {
            throw new NotFoundHttpException('Composition not found');
        }
        $user = $this->getUser();
        $query = $this->chapterRepository->getChaptersForComposition($composition, $user);
        $chapters = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 1),
        );
        $user? $isFavourite = $this->favouriteRepository->isFavouriteCompositionForUser($user, $composition):$isFavourite = false;
        $rate = $this->ratesService->getUserRateForComposition($user, $composition);
        $compositionAverageRate = $this->compositionService->compositionAverageRate($composition);
        $comments = $this->commentRepository->getCommentsForComposition($id);
        $author = $this->userRepository->find($composition->getUser());

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->commentService->commentCreate($form->getData(), $user, $composition);
            return $this->redirectToRoute('composition', ['id' => $id]);
        }

        return $this->render('composition/index.html.twig', [
            'composition' => $composition,
            'chapters' => $chapters,
            'comments' => $comments,
            'author' => $author,
            'form' => $form->createView(),
            'rate' => $rate,
            'composition_average_rate' => $compositionAverageRate,
            'is_favourite' => $isFavourite
        ]);
    }

    #[Route('/create/composition', name: 'composition_create')]
    public function createComposition(Request $request) :Response
    {
        $composition = new Composition();
        $form = $this->createForm(CreateCompositionType::class, $composition);
        $form->handleRequest($request);
        $user = $this->getUser();

        if ($form->isSubmitted() && $form->isValid()) {
            $this->compositionService->compositionCreate($form->getData(), $user);
            $session = new Session();
            $session->getFlashBag()->add('success', 'Composition created successfully');
            return $this->redirectToRoute('profile');
        }

        return $this->render('composition/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/edit/composition/{id<\d+>?1}', name: 'composition_edit')]
    public function editComposition(int $id, Request $request) :Response
    {
        $composition = $this->compositionRepository->find($id);
        if (!$composition) {
            throw new NotFoundHttpException('Composition not found');
        }
        $chapters = $this->chapterRepository->findByComposition($composition);
        $this->denyAccessUnlessGranted(CustomVoter::EDIT, $composition->getUser());
        $form = $this->createForm(CreateCompositionType::class, $composition);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->compositionService->compositionEdit($composition);
            return $this->redirectToRoute('composition', ['id' => $composition->getId()]);
        }

        return $this->render('composition/edit.html.twig', [
            'composition' => $composition,
            'chapters' => $chapters,
            'form' => $form->createView()
        ]);
    }

    #[Route('/delete/composition/{id<\d+>?}', name: 'composition_delete')]
    public function deleteComposition(int $id) :Response
    {
        $composition = $this->compositionRepository->find($id);
        if (!$composition) {
            throw new NotFoundHttpException('Composition not found');
        }
        $this->denyAccessUnlessGranted(CustomVoter::DELETE, $composition->getUser());
        $this->em->remove($composition);
        $this->em->flush();
        $session = new Session();
        $session->getFlashBag()->add('success', 'Composition deleted successfully');

        return $this->redirectToRoute('profile');
    }

    #[Route('/add/favourite/{id<\d+>?}', name: 'composition_add_to_favourite')]
    public function addToFavourite(int $id) :Response
    {
        $composition = $this->compositionRepository->find($id);
        $user = $this->getUser();
        $checkFavourite = $this->favouriteRepository->isFavouriteCompositionForUser($user, $composition);

        if (!$checkFavourite) {
            $favourite = new Favourite();
            $favourite->setUser($user);
            $favourite->setComposition($composition);
            $this->em->persist($favourite);
            $this->em->flush();
        }

        return $this->redirectToRoute('favourites');
    }

    #[Route('/delete/favourite/{id<\d+>?}', name: 'composition_delete_from_favourite')]
    public function deleteFromFavourite(int $id) :Response
    {
        $composition = $this->compositionRepository->find($id);
        $user = $this->getUser();
        $checkFavourite = $this->favouriteRepository->isFavouriteCompositionForUser($user, $composition);

        if ($checkFavourite) {
            $this->em->remove($checkFavourite);
            $this->em->flush();
        }

        return $this->redirectToRoute('favourites');
    }

    #[Route('/rate/composition/{id<\d+>?}/{rate<\d+>?}', name: 'composition_rate')]
    public function rate(int $id, int $rate) :Response
    {
        $composition = $this->compositionRepository->find($id);
        $user = $this->getUser();
        $checkRate = $this->ratesRepository->checkRateByUserForComposition($user, $composition);
        if (!$checkRate) {
            $checkRate = new Rates();
            $checkRate->setUser($user);
            $checkRate->setComposition($composition);
        }
        $checkRate->setRate($rate);
        $this->em->persist($checkRate);
        $this->em->flush();

        return $this->redirectToRoute('composition', ['id' => $id]);
    }
}
