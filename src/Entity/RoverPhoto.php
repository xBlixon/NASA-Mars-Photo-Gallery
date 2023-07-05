<?php

namespace App\Entity;

use App\Repository\RoverPhotoRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RoverPhotoRepository::class)]
class RoverPhoto
{
    #[ORM\Id]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 32)]
    private ?string $roverName = null;

    #[ORM\Column(length: 8)]
    private ?string $cameraName = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $earthDate = null;

    #[ORM\Column(length: 16383, type: Types::TEXT)]
    private ?string $imageURL = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getRoverName(): ?string
    {
        return $this->roverName;
    }

    public function setRoverName(string $roverName): static
    {
        $this->roverName = $roverName;

        return $this;
    }

    public function getCameraName(): ?string
    {
        return $this->cameraName;
    }

    public function setCameraName(string $cameraName): static
    {
        $this->cameraName = $cameraName;

        return $this;
    }

    public function getEarthDate(): ?\DateTimeInterface
    {
        return $this->earthDate;
    }

    public function setEarthDate(\DateTimeInterface $earthDate): static
    {
        $this->earthDate = $earthDate;

        return $this;
    }

    public function getImageURL(): ?string
    {
        return $this->imageURL;
    }

    public function setImageURL(string $imageURL): static
    {
        $this->imageURL = $imageURL;

        return $this;
    }
}
