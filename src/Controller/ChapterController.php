<?php

namespace App\Controller;

use App\Entity\Chapter;
use App\Entity\Appraisal;
use App\Form\CreateChapterType;
use App\Repository\ChapterRepository;
use App\Repository\CompositionRepository;
use App\Repository\LikesRepository;
use App\Security\Voter\CustomVoter;
use App\Service\ChapterService;
use App\Service\LikeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ChapterController extends AbstractController
{
    protected ChapterService $chapterService;
    protected ChapterRepository $chapterRepository;
    protected LikesRepository $likesRepository;
    protected CompositionRepository $compositionRepository;
    protected LikeService $likeService;
    protected EntityManagerInterface $em;

    public function __construct(ChapterService $chapterService, ChapterRepository $chapterRepository, EntityManagerInterface $em,
                                LikesRepository $likesRepository, LikeService $likeService, CompositionRepository $compositionRepository)
    {
        $this->chapterService = $chapterService;
        $this->chapterRepository = $chapterRepository;
        $this->likesRepository = $likesRepository;
        $this->compositionRepository = $compositionRepository;
        $this->likeService = $likeService;
        $this->em = $em;
    }

    #[Route('/create/chapter/{id<\d+>?}', name: 'chapter_add')]
    public function createChapter(Request $request, int $id) :Response
    {
        $composition = $this->compositionRepository->find($id);
        if (!$composition) {
            throw new NotFoundHttpException('Composition not found');
        }
        $this->denyAccessUnlessGranted(CustomVoter::CHAPTER_CREATE, $composition->getUser());
        $chapter = new Chapter();
        $form = $this->createForm(CreateChapterType::class, $chapter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->chapterService->chapterCreate($form->getData(), $id);
            return $this->redirectToRoute('composition_edit', ['id' => $chapter->getComposition()->getId()]);
        }

        return $this->render('chapter/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/edit/chapter/{id<\d+>?}', name: 'chapter_edit')]
    public function editChapter(Request $request, int $id) :Response
    {
        $chapter = $this->chapterRepository->find($id);
        if (!$chapter) {
            throw new NotFoundHttpException('Chapter not found');
        }
        $composition = $this->compositionRepository->find($chapter->getComposition());
        $this->denyAccessUnlessGranted(CustomVoter::CHAPTER_EDIT, $composition->getUser());
        $chapter = $this->chapterRepository->find($id);
        if (!$chapter) {
            throw new NotFoundHttpException('Chapter not found');
        }
        $form = $this->createForm(CreateChapterType::class, $chapter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->chapterService->chapterEdit($form->getData());
            return $this->redirectToRoute('composition_edit', ['id' => $chapter->getComposition()->getId()]);
        }

        return $this->render('chapter/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/delete/chapter/{id<\d+>?}', name: 'chapter_delete')]
    public function deleteChapter(Request $request, int $id) :Response
    {
        $chapter = $this->chapterRepository->find($id);
        if (!$chapter) {
            throw new NotFoundHttpException('Chapter not found');
        }
        $composition = $this->compositionRepository->find($chapter->getComposition());
        $this->denyAccessUnlessGranted(CustomVoter::CHAPTER_DELETE, $composition->getUser());
        $chapter = $this->chapterRepository->find($id);
        if (!$chapter) {
            throw new NotFoundHttpException('Chapter not found');
        }
        $this->em->remove($chapter);
        $this->em->flush();
        return $this->redirectToRoute('composition_edit', ['id' => $chapter->getComposition()->getId()]);
    }

    #[Route('/like/chapter/{id<\d+>?}', name: 'chapter_like')]
    public function rate(int $id) :Response
    {
        $chapter = $this->chapterRepository->find($id);
        if (!$chapter) {
            throw new NotFoundHttpException('Chapter not found');
        }
        $user = $this->getUser();
        $isLiked = $this->likesRepository->getUserLikeForChapter($user, $chapter);
        $this->likeService->like($isLiked, $chapter, $user);
        return $this->redirectToRoute('composition', ['id' => $chapter->getComposition()->getId()]);
    }
}
