<?php

namespace App\Controller;

use App\Entity\Composition;
use App\Entity\Favourite;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\SwitchUserToken;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        $lastUpdatedCompositions = $this->getDoctrine()->getRepository(Composition::class)->lastUpdated(3);
        $moreRated = $this->getDoctrine()->getRepository(Composition::class)->lastUpdated(3);

        return $this->render('home/index.html.twig', [
            'last_updated_compositions' => $lastUpdatedCompositions,
            'more_rated' => $moreRated
        ]);
    }

    #[Route('/profile', name: 'profile')]
    public function profile(): Response
    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        $id = $user->getId();
        $compositions = $this->getDoctrine()->getRepository(Composition::class)->getUserCompositionsWithFandoms($id);

        $token = $this->get('security.token_storage')->getToken();
        if ($token instanceof SwitchUserToken) {
            $isSwitch = 1;
        } else {
            $isSwitch = 0;
        }

        return $this->render('home/profile.html.twig', [
            'compositions' => $compositions,
            'roles' => $roles,
            'is_switch' => $isSwitch
        ]);
    }

    #[Route('/favourites', name: 'favourites')]
    public function favourites(): Response
    {
        $user = $this->getUser();
        $favourites = $this->getDoctrine()->getRepository(Favourite::class)->favouritesForUser($user);

        return $this->render('home/favourites.html.twig', [
            'favourites' => $favourites
        ]);
    }
}