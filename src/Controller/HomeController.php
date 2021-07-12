<?php

namespace App\Controller;

use App\Entity\Composition;
use App\Entity\Favourite;
use App\Repository\CommentRepository;
use App\Repository\CompositionRepository;
use App\Repository\FavouriteRepository;
use App\Repository\RatesRepository;
use App\Repository\UserRepository;
use App\Service\CommentService;
use App\Service\CompositionService;
use App\Service\RatesService;
use App\Service\Security;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;

class HomeController extends AbstractController
{
    protected TokenStorageInterface $tokenStorage;
    protected Security $security;
    protected CompositionRepository $compositionRepository;
    protected FavouriteRepository $favouriteRepository;

    public function __construct(TokenStorageInterface $tokenStorage, Security $security,
                                CompositionRepository $compositionRepository, FavouriteRepository $favouriteRepository)
    {
        $this->tokenStorage = $tokenStorage;
        $this->security = $security;
        $this->compositionRepository = $compositionRepository;
        $this->favouriteRepository = $favouriteRepository;
    }

    #[Route('/', name: 'home')]
    public function index(): Response
    {
        $lastUpdatedCompositions = $this->compositionRepository->lastUpdated(3);
        $moreRated = $lastUpdatedCompositions;

        return $this->render('home/index.html.twig', [
            'last_updated_compositions' => $lastUpdatedCompositions,
            'more_rated' => $moreRated
        ]);
    }

    #[Route('/profile', name: 'profile')]
    public function profile(PaginatorInterface $paginator, Request $request): Response
    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        $query = $this->compositionRepository->getUserCompositionsWithFandoms($user->getId());
        $compositions = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 5),
        );

        $token = $this->get('security.token_storage')->getToken();
        $isSwitch = $this->security->isSwitch($this->tokenStorage, $token);

        return $this->render('home/profile.html.twig', [
            'compositions' => $compositions,
            'roles' => $roles,
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
            $cookie = new Cookie('theme', 0);
        } else {
            $cookie = new Cookie('theme', 1);
        }
        $response->headers->setCookie($cookie);
        return $response;
    }
}