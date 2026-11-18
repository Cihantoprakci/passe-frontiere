<?php

namespace App\Entity;

use App\Repository\CommuteRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CommuteRepository::class)]
class Commute
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'commute')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'Choisissez votre gare de départ.')]
    private ?string $stationFrom = 'Annemasse';

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Border $border = null;

    #[ORM\Column(type: 'time_immutable')]
    private ?\DateTimeImmutable $leaveAt = null;

    public function __construct()
    {
        $this->leaveAt = new \DateTimeImmutable('07:10');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getStationFrom(): ?string
    {
        return $this->stationFrom;
    }

    public function setStationFrom(string $stationFrom): static
    {
        $this->stationFrom = $stationFrom;

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

    public function getLeaveAt(): ?\DateTimeImmutable
    {
        return $this->leaveAt;
    }

    public function setLeaveAt(\DateTimeImmutable $leaveAt): static
    {
        $this->leaveAt = $leaveAt;

        return $this;
    }
}
