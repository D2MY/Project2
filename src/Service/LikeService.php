<?php

namespace App\Service;

use App\Entity\Chapter;
use App\Entity\Appraisal;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class LikeService
{
    protected EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function like(?Appraisal $isLiked, Chapter $chapter, User $user) :void
    {
        if (!$isLiked) {
            $like = new Appraisal();
            $like->setChapter($chapter);
            $like->setUser($user);
            $this->em->persist($like);
        } else {
            $this->em->remove($isLiked);
        }
        $this->em->flush();
    }
}
