<?php

namespace App\Entity;

use App\Repository\ChapterRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraint as Assert;

/**
 * @ORM\Entity(repositoryClass=ChapterRepository::class)
 */
class Chapter
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity=Composition::class)
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private Composition $composition;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private string $title;

    /**
     * @ORM\Column(type="text", nullable=false)
     */
    private string $text;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getComposition(): ?Composition
    {
        return $this->composition;
    }

    public function setComposition(Composition $composition): self
    {
        $this->composition = $composition;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }
}
