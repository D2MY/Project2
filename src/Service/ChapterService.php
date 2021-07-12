<?php

namespace App\Service;

use App\Entity\Chapter;
use App\Repository\CompositionRepository;
use Doctrine\ORM\EntityManagerInterface;

class ChapterService
{
    protected EntityManagerInterface $em;
    protected CompositionRepository $compositionRepository;

    public function __construct(EntityManagerInterface $em, CompositionRepository $compositionRepository)
    {
        $this->em = $em;
        $this->compositionRepository = $compositionRepository;
    }

    public function chapterCreate(Chapter $chapter, int $id) :void
    {
        $composition = $this->compositionRepository->find($id);
        if (!$composition) {
            return;
        }
        $chapter->setComposition($composition);
        $this->em->persist($chapter);
        $this->em->flush();
    }
}
