<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\CompositionRepository;
use App\Repository\FavouriteRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\SwitchUserToken;

class HomeController extends AbstractController
{
    protected CompositionRepository $compositionRepository;
    protected FavouriteRepository $favouriteRepository;

    public function __construct(CompositionRepository $compositionRepository, FavouriteRepository $favouriteRepository)
    {
        $this->compositionRepository = $compositionRepository;
        $this->favouriteRepository = $favouriteRepository;
    }

    #[Route('/', name: 'home')]
    public function index(): Response
    {
        $lastUpdatedCompositions = $this->compositionRepository->lastUpdated(3);
        $moreRated = $this->compositionRepository->moreRated(3);

        return $this->render('home/index.html.twig', [
            'last_updated_compositions' => $lastUpdatedCompositions,
            'more_rated' => $moreRated
        ]);
    }

    #[Route('/profile', name: 'profile')]
    public function profile(PaginatorInterface $paginator, Request $request): Response
    {
        $query = $this->compositionRepository->getUserCompositionsWithFandoms($this->getUser()->getId());
        $compositions = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 5),
        );

        $token = $this->get('security.token_storage')->getToken();
        $isSwitch = $token instanceof SwitchUserToken;

        return $this->render('home/profile.html.twig', [
            'compositions' => $compositions,
            'is_switch' => $isSwitch
        ]);
    }

    #[Route('/favourites', name: 'favourites')]
    public function favourites(PaginatorInterface $paginator, Request $request): Response
    {
        $user = $this->getUser();
        $query = $this->favouriteRepository->favouritesForUser($user);
        $favourites = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 5),
        );

        return $this->render('home/favourites.html.twig', [
            'favourites' => $favourites
        ]);
    }

    #[Route('/theme', name: 'change_theme')]
    public function changeTheme(Request $request): Response
    {
        $response = new RedirectResponse($request->headers->get('referer'));
        $cookie = $request->cookies->get('theme');
        if ($cookie) {
            $cookie = new Cookie('theme', '');
        } else {
            $cookie = new Cookie('theme', 'dark');
        }
        $response->headers->setCookie($cookie);
        return $response;
    }
}