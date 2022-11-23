<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ImageRepository::class)]
// Indique à Doctrine que cette entitée posséde des événements (cycle de vie)
#[ORM\HasLifecycleCallbacks]
class Image
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $path = null;

    #[ORM\Column(length: 80)]
    private ?string $name = null;

    #[Assert\Image(maxSize: '10M')]
    private UploadedFile $file;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    private string $oldPath = "";

    public function getFile(): UploadedFile
    {
        return $this->file;
    }

    public function setFile(UploadedFile $file): self
    {
        $this->file = $file;
        $this->oldPath = $this->path;
        $this->path = ''; // on vide le path pour que Doctrine puisse comprendre que le prochain path est pas pareil

        return $this;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function generatePath(): self
    {
        // Si un fichier a bien été envoyé
        if ($this->file instanceof UploadedFile) {
            // Génére un chemin
            $this->path = uniqid("img_").'.'.$this->file->guessExtension();
            $this->name = $this->file->getClientOriginalName();
        }

        return $this;
    }

    /**
     * Retourne le lien absolu vers le dossier d'upload (DIRECTORY_SEPARATOR choisi selon l'OS le / ou \ du lien du fichier)
     */
    public static function getPublicRootDir(): string
    {
        return __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.
        'public'.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR;
    }

    #[ORM\PostPersist]
    #[ORM\PostUpdate]
    public function upload(): void
    {
        if (is_file(self::getPublicRootDir().$this->oldPath)) {
            unlink(self::getPublicRootDir().$this->oldPath);
        }

        if ($this->file instanceof UploadedFile) {
            $this->file->move(self::getPublicRootDir(), $this->path);
        }
    }

    public function getWebPath(): string
    {
        return '/uploads/'. $this->path;
    }
}
