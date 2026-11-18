<?php

namespace App\Entity;

use App\Repository\BorderReadingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BorderReadingRepository::class)]
#[ORM\Index(columns: ['border_id', 'measured_at'])]
class BorderReading
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'readings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Border $border = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $measuredAt = null;

    #[ORM\Column]
    private ?int $travelSeconds = null;

    #[ORM\Column]
    private ?int $freeFlowSeconds = null;

    #[ORM\Column]
    private ?int $delaySeconds = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getMeasuredAt(): ?\DateTimeImmutable
    {
        return $this->measuredAt;
    }

    public function setMeasuredAt(\DateTimeImmutable $measuredAt): static
    {
        $this->measuredAt = $measuredAt;

        return $this;
    }

    public function getTravelSeconds(): ?int
    {
        return $this->travelSeconds;
    }

    public function setTravelSeconds(int $travelSeconds): static
    {
        $this->travelSeconds = $travelSeconds;

        return $this;
    }

    public function getFreeFlowSeconds(): ?int
    {
        return $this->freeFlowSeconds;
    }

    public function setFreeFlowSeconds(int $freeFlowSeconds): static
    {
        $this->freeFlowSeconds = $freeFlowSeconds;

        return $this;
    }

    public function getDelaySeconds(): ?int
    {
        return $this->delaySeconds;
    }

    public function setDelaySeconds(int $delaySeconds): static
    {
        $this->delaySeconds = $delaySeconds;

        return $this;
    }

    public function getDelayMinutes(): int
    {
        return (int) round($this->delaySeconds / 60);
    }

    public function getStatus(): string
    {
        $minutes = $this->getDelayMinutes();

        if ($minutes >= 15) {
            return 'bouche';
        }

        if ($minutes >= 5) {
            return 'ralenti';
        }

        return 'fluide';
    }
}
