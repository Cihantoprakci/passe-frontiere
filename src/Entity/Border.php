<?php

namespace App\Entity;

use App\Repository\BorderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BorderRepository::class)]
class Border
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(length: 100, unique: true)]
    private ?string $slug = null;

    #[ORM\Column(length: 100)]
    private ?string $frenchSide = null;

    #[ORM\Column(length: 100)]
    private ?string $swissSide = null;

    #[ORM\Column(length: 150)]
    private ?string $road = null;

    #[ORM\Column]
    private ?float $startLat = null;

    #[ORM\Column]
    private ?float $startLng = null;

    #[ORM\Column]
    private ?float $endLat = null;

    #[ORM\Column]
    private ?float $endLng = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photo = null;

    #[ORM\Column]
    private bool $active = true;

    #[ORM\OneToMany(targetEntity: BorderReading::class, mappedBy: 'border', orphanRemoval: true)]
    private Collection $readings;

    public function __construct()
    {
        $this->readings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getFrenchSide(): ?string
    {
        return $this->frenchSide;
    }

    public function setFrenchSide(string $frenchSide): static
    {
        $this->frenchSide = $frenchSide;

        return $this;
    }

    public function getSwissSide(): ?string
    {
        return $this->swissSide;
    }

    public function setSwissSide(string $swissSide): static
    {
        $this->swissSide = $swissSide;

        return $this;
    }

    public function getRoad(): ?string
    {
        return $this->road;
    }

    public function setRoad(string $road): static
    {
        $this->road = $road;

        return $this;
    }

    public function getStartLat(): ?float
    {
        return $this->startLat;
    }

    public function setStartLat(float $startLat): static
    {
        $this->startLat = $startLat;

        return $this;
    }

    public function getStartLng(): ?float
    {
        return $this->startLng;
    }

    public function setStartLng(float $startLng): static
    {
        $this->startLng = $startLng;

        return $this;
    }

    public function getEndLat(): ?float
    {
        return $this->endLat;
    }

    public function setEndLat(float $endLat): static
    {
        $this->endLat = $endLat;

        return $this;
    }

    public function getEndLng(): ?float
    {
        return $this->endLng;
    }

    public function setEndLng(float $endLng): static
    {
        $this->endLng = $endLng;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): static
    {
        $this->photo = $photo;

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

    public function getReadings(): Collection
    {
        return $this->readings;
    }
}
