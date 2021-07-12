<?php

namespace App\Entity;

use App\Repository\CompositionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CompositionRepository::class)
 */
class Composition
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private string $title;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank
     */
    private string $description;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank
     */
    private string $text;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="compositions")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private User $user;

    /**
     * @ORM\ManyToOne(targetEntity=Fandom::class,  inversedBy="compositions")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank
     */
    private Fandom $fandom;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private \DateTime $updatedAt;

    /**
     * @ORM\Column(type="float", nullable=false)
     */
    private float $averageRate;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private \DateTime $lastRateUpdate;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

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

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getFandom(): Fandom
    {
        return $this->fandom;
    }

    public function setFandom(Fandom $fandom): self
    {
        $this->fandom = $fandom;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getAverageRate(): ?float
    {
        return $this->averageRate;
    }

    public function setAverageRate(?float $averageRate): self
    {
        $this->averageRate = $averageRate;

        return $this;
    }

    public function getLastRateUpdate(): ?\DateTimeInterface
    {
        return $this->lastRateUpdate;
    }

    public function setLastRateUpdate(?\DateTimeInterface $lastRateUpdate): self
    {
        $this->lastRateUpdate = $lastRateUpdate;

        return $this;
    }
}
