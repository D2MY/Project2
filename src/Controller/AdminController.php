<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\Security;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;

class AdminController extends AbstractController
{
    protected TokenStorageInterface $tokenStorage;
    protected Security $security;
    protected UserRepository $userRepository;

    public function __construct(TokenStorageInterface $tokenStorage, Security $security,
                                UserRepository $userRepository)
    {
        $this->tokenStorage = $tokenStorage;
        $this->security = $security;
        $this->userRepository = $userRepository;
    }

    #[Route('/admin', name: 'admin')]
    public function admin(Request $request, PaginatorInterface $paginator): Response
    {
        $query = $this->userRepository->getAllUsers();
        $users = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 25),
        );

        return $this->render('admin/index.html.twig', [
            'users' => $users
        ]);
    }

    #[Route('/admin/edit/user/{id<\d+>}', name: 'edit_user')]
    public function editUser(int $id): Response
    {
        $user = $this->userRepository->find($id);
        in_array('ROLE_ADMIN', $user->getRoles(), true)?$user->setRoles(['ROLE_USER']):$user->setRoles(['ROLE_ADMIN']);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('admin');
    }

    #[Route('/admin/delete/user/{id<\d+>}', name: 'delete_user')]
    public function deleteUser(int $id): Response
    {
        $user = $this->userRepository->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($user);
        $entityManager->flush();

        return $this->redirectToRoute('admin');
    }
}