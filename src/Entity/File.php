<?php

namespace App\Entity;

use App\Repository\FileRepository;
use ApiPlatform\Metadata\ApiProperty;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use App\Controller\CreateFileController;
use Symfony\Component\HttpFoundation\File\File as FileApi;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use App\State\GetCollectionFileProvider;


#[ApiResource(
    normalizationContext: ['groups' => ['file:read']],
    denormalizationContext: ['groups' => ['file:write']], 
    operations: [
        new GetCollection(
            name: 'get_files_user',
            provider: GetCollectionFileProvider::class,
        ),
        new Post(
            outputFormats: ['jsonld' => ['application/ld+json']],
            inputFormats: ['multipart' => ['multipart/form-data']],
            name: 'publication',
            uriTemplate: '/files', 
            controller: CreateFileController::class
        ), 
        new Delete(security: "object == user",
             securityMessage: 'Удалять можно только себя'),
    ]
)]
#[ORM\Entity(repositoryClass: FileRepository::class)]
#[Vich\Uploadable]
#[ORM\HasLifecycleCallbacks]
class File
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['file:read'])]
    private ?int $id = null;

    #[Groups(['file:read'])]
    #[ORM\Column(nullable: true)] 
    private ?string $mimeType = null;

    #[Groups(['file:read'])]
    #[ORM\Column(nullable: true)] 
    private ?string $fileSize = null;

    #[Groups(['file:write'])]
    #[Vich\UploadableField(mapping: 'files', fileNameProperty: 'filePath', mimeType: 'mimeType', size: 'fileSize')]
    private ?FileApi $file = null;

    #[ORM\Column(nullable: true)] 
    #[Groups(['file:read'])]
    private ?string $filePath = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'files')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['file:read'])]
    private ?User $user = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['file:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    public function setFilePath(?string $filePath): static
    {
        $this->filePath = $filePath;

        return $this;
    }

    public function getFile(): ?FileApi
    {
        return $this->file;
    }

    public function setFile(?FileApi $file): static
    {
        $this->file = $file;

        return $this;
    }

    // public function getContentUrl(): ?string
    // {
    //     return $this->contentUrl;
    // }

    // public function setContentUrl(?string $contentUrl): static
    // {
    //     $this->contentUrl = $contentUrl;

    //     return $this;
    // }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

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

    public function getFileSize(): ?string
    {
        return $this->fileSize;
    }

    public function setFileSize(?string $fileSize): static
    {
        $this->fileSize = $fileSize;

        return $this;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function setMimeType(?string $mimeType): static
    {
        $this->mimeType = $mimeType;

        return $this;
    }
}
