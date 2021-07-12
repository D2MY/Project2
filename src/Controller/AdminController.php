<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Service\AdminService;
use App\Service\Security;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;

class AdminController extends AbstractController
{
    protected TokenStorageInterface $tokenStorage;
    protected Security $security;
    protected UserRepository $userRepository;
    protected EntityManagerInterface $em;
    protected AdminService $adminService;
    protected SessionInterface $session;

    public function __construct(TokenStorageInterface $tokenStorage, Security $security, UserRepository $userRepository,
                                EntityManagerInterface $em, AdminService $adminService, SessionInterface $session)
    {
        $this->tokenStorage = $tokenStorage;
        $this->security = $security;
        $this->userRepository = $userRepository;
        $this->em = $em;
        $this->adminService = $adminService;
        $this->session = $session;
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
        if (!$user) {
            return $this->redirectToRoute('admin');
        }
        $user = $this->adminService->changeRole($user);
        $this->em->persist($user);
        $this->em->flush();
        $this->session->getFlashBag()->add('', 'User edited successfully');

        return $this->redirectToRoute('admin');
    }

    #[Route('/admin/delete/user/{id<\d+>}', name: 'delete_user')]
    public function deleteUser(int $id): Response
    {
        $user = $this->userRepository->find($id);
        $this->em->remove($user);
        $this->em->flush();
        $this->session->getFlashBag()->add('', 'User deleted successfully');

        return $this->redirectToRoute('admin');
    }
}