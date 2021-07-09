<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin')]
    public function admin(Request $request): Response
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        return $this->render('admin/index.html.twig', [
            'users' => $users
        ]);
    }

    #[Route('/admin/edit-user/{id<\d+>}', name: 'edit_user')]
    public function editUser(int $id): Response
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);
        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            $user->setRoles(['ROLE_USER']);
        } else {
            $user->setRoles(['ROLE_ADMIN']);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('admin');
    }

    #[Route('/admin/delete-user/{id<\d+>}', name: 'delete_user')]
    public function deleteUser(int $id): Response
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($user);
        $entityManager->flush();

        return $this->redirectToRoute('admin');
    }
}