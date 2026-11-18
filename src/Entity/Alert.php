<?php

namespace App\Entity;

use App\Repository\AlertRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AlertRepository::class)]
class Alert
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'alerts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Assert\NotNull(message: 'Choisissez une douane.')]
    private ?Border $border = null;

    #[ORM\Column]
    #[Assert\Range(min: 1, max: 120, notInRangeMessage: 'Le seuil doit être entre {{ min }} et {{ max }} minutes.')]
    private int $thresholdMinutes = 15;

    #[ORM\Column(type: 'time_immutable')]
    private ?\DateTimeImmutable $windowStart = null;

    #[ORM\Column(type: 'time_immutable')]
    private ?\DateTimeImmutable $windowEnd = null;

    #[ORM\Column]
    private bool $active = true;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $lastSentAt = null;

    public function __construct()
    {
        $this->windowStart = new \DateTimeImmutable('06:00');
        $this->windowEnd = new \DateTimeImmutable('08:00');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getBorder(): ?Border
    {
        return $this->border;
    }

    public function setBorder(?Border $border): static
    {
        $this->border = $border;

        return $this;
    }

    public function getThresholdMinutes(): int
    {
        return $this->thresholdMinutes;
    }

    public function setThresholdMinutes(int $thresholdMinutes): static
    {
        $this->thresholdMinutes = $thresholdMinutes;

        return $this;
    }

    public function getWindowStart(): ?\DateTimeImmutable
    {
        return $this->windowStart;
    }

    public function setWindowStart(\DateTimeImmutable $windowStart): static
    {
        $this->windowStart = $windowStart;

        return $this;
    }

    public function getWindowEnd(): ?\DateTimeImmutable
    {
        return $this->windowEnd;
    }

    public function setWindowEnd(\DateTimeImmutable $windowEnd): static
    {
        $this->windowEnd = $windowEnd;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    public function getLastSentAt(): ?\DateTimeImmutable
    {
        return $this->lastSentAt;
    }

    public function setLastSentAt(?\DateTimeImmutable $lastSentAt): static
    {
        $this->lastSentAt = $lastSentAt;

        return $this;
    }
}
