<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\GamesRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: GamesRepository::class)]
class Games
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank()]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?bool $bool = false;

    #[ORM\ManyToOne(inversedBy: 'games')]
    #[ORM\JoinColumn(onDelete: "SET NULL")]
    private ?Editor $editor = null;

/*     #[ORM\Column(type: Types::DATE_IMMUTABLE)] */
    private ?\DateTime $releaseDate;

    #[ORM\OneToOne(cascade: ['persist', 'remove'], orphanRemoval: true)]
    private ?Image $mainImage = null;

    private bool $deleteMainImage;

    public function __construct()
    {
        $this->releaseDate = new \DateTime;
    }

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

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function isBool(): ?bool
    {
        return $this->bool;
    }

    public function setBool(bool $bool): self
    {
        $this->bool = $bool;

        return $this;
    }

    public function getEditor(): ?Editor
    {
        return $this->editor;
    }

    public function setEditor(?Editor $editor): self
    {
        $this->editor = $editor;

        return $this;
    }
    public function getReleaseDate(): ?\DateTime
    {
        return $this->releaseDate ?? new \DateTime;
    }
    public function setReleaseDate(?\DateTime $releaseDate): self
    {
        $this->releaseDate = $releaseDate;

        return $this;
    }

    public function getMainImage(): ?Image
    {
        return $this->mainImage;
    }

    public function setMainImage(?Image $mainImage): self
    {
        if ($mainImage !== null && $mainImage->getPath() !== null) {
            $this->mainImage = $mainImage;
        }

        return $this;
    }

    public function getDeleteMainImage(): bool
    {
        return $this->deleteMainImage;
    }

    public function setDeleteMainImage(?bool $deleteMainImage): self
    {
        $this->deleteMainImage = $deleteMainImage;

        if ($this->deleteMainImage) {
            $this->mainImage = null;
        }

        return $this;
    }

}
