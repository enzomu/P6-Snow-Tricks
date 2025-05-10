<?php

namespace App\Entity;

use App\Repository\FigureRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: FigureRepository::class)]
#[UniqueEntity(fields: ['name'], message: 'Une figure avec ce nom existe déjà.')]
class Figure
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100, unique: true)]
    #[Assert\NotBlank(message: 'Le nom de la figure est obligatoire.')]
    #[Assert\Length(
        min: 3,
        max: 100,
        minMessage: 'Le nom doit comporter au moins {{ limit }} caractères.',
        maxMessage: 'Le nom ne peut pas dépasser {{ limit }} caractères.'
    )]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'La description de la figure est obligatoire.')]
    #[Assert\Length(
        min: 10,
        minMessage: 'La description doit comporter au moins {{ limit }} caractères.'
    )]
    private ?string $description = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'La catégorie de la figure est obligatoire.')]
    private ?string $category = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $mainMedia = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $mediaGallery = [];

    #[ORM\ManyToOne(inversedBy: 'figures')]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $author = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'figure', orphanRemoval: true)]
    private Collection $comments;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $mainMediaFile = null;

    #[Assert\File(
        maxSize: '5M',
        mimeTypes: ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
        mimeTypesMessage: 'Veuillez uploader une image valide (JPG, PNG, GIF ou WEBP)'
    )]
    private $mainMediaFileUpload;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $mediaGalleryFiles = [];

    private $mediaGalleryFileUpload;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): static
    {
        $this->category = $category;
        return $this;
    }

    public function getMainMedia(): ?string
    {
        return $this->mainMedia;
    }

    public function setMainMedia(?string $mainMedia): self
    {
        $this->mainMedia = $mainMedia;
        return $this;
    }

    public function getMediaGallery(): ?array
    {
        return $this->mediaGallery;
    }

    public function setMediaGallery(?array $mediaGallery): self
    {
        $this->mediaGallery = $mediaGallery;
        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setFigure($this);
        }
        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            if ($comment->getFigure() === $this) {
                $comment->setFigure(null);
            }
        }
        return $this;
    }

    public function getMainMediaFile(): ?string
    {
        return $this->mainMediaFile;
    }

    public function setMainMediaFile(?string $mainMediaFile): self
    {
        $this->mainMediaFile = $mainMediaFile;
        return $this;
    }

    public function getMainMediaFileUpload()
    {
        return $this->mainMediaFileUpload;
    }

    public function setMainMediaFileUpload($mainMediaFileUpload): self
    {
        $this->mainMediaFileUpload = $mainMediaFileUpload;
        return $this;
    }

    public function getMediaGalleryFiles(): ?array
    {
        return $this->mediaGalleryFiles;
    }

    public function setMediaGalleryFiles(?array $mediaGalleryFiles): self
    {
        $this->mediaGalleryFiles = $mediaGalleryFiles;
        return $this;
    }

    public function getMediaGalleryFileUpload()
    {
        return $this->mediaGalleryFileUpload;
    }

    public function setMediaGalleryFileUpload($mediaGalleryFileUpload): self
    {
        $this->mediaGalleryFileUpload = $mediaGalleryFileUpload;
        return $this;
    }

    public function addMediaGalleryFile(string $mediaFile): self
    {
        if (!in_array($mediaFile, $this->mediaGalleryFiles)) {
            $this->mediaGalleryFiles[] = $mediaFile;
        }
        return $this;
    }
}