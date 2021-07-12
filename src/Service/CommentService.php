<?php

namespace App\Service;


use App\Entity\Comment;
use App\Entity\Composition;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class CommentService
{
    protected EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function commentCreate(Comment $comment, User $user, Composition $composition) :void
    {
        $comment->setCreatedAt(new \DateTime());
        $comment->setUser($user);
        $comment->setComposition($composition);

        $this->em->persist($comment);
        $this->em->flush();
    }
}
