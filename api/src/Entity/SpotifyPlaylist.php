<?php

namespace App\Entity;

use App\Repository\SpotifyPlaylistRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SpotifyPlaylistRepository::class)]
#[UniqueEntity(fields: ['spotifyId'], message: 'Cette playlist Spotify existe déjà')]
#[Groups(['playlist:read'])]
class SpotifyPlaylist
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100, unique: true)]
    #[Assert\NotBlank(message: 'L\'ID Spotify est obligatoire')]
    #[Assert\Length(max: 100)]
    #[Groups(['playlist:read', 'playlist:write'])]
    private ?string $spotifyId = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le nom de la playlist est obligatoire')]
    #[Assert\Length(max: 255)]
    #[Groups(['playlist:read', 'playlist:write'])]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(max: 255)]
    #[Groups(['playlist:read', 'playlist:write'])]
    private ?string $snapshotId = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Assert\Length(max: 100)]
    #[Groups(['playlist:read', 'playlist:write'])]
    private ?string $userId = null;

    #[ORM\Column]
    #[Groups(['playlist:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'spotifyPlaylists')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'Le chart source est obligatoire')]
    #[Groups(['playlist:read', 'playlist:write'])]
    private ?Chart $chart = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function __toString(): string
    {
        return $this->name ?? 'SpotifyPlaylist';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSpotifyId(): ?string
    {
        return $this->spotifyId;
    }

    public function setSpotifyId(string $spotifyId): static
    {
        $this->spotifyId = $spotifyId;

        return $this;
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

    public function getSnapshotId(): ?string
    {
        return $this->snapshotId;
    }

    public function setSnapshotId(?string $snapshotId): static
    {
        $this->snapshotId = $snapshotId;

        return $this;
    }

    public function getUserId(): ?string
    {
        return $this->userId;
    }

    public function setUserId(?string $userId): static
    {
        $this->userId = $userId;

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

    public function getChart(): ?Chart
    {
        return $this->chart;
    }

    public function setChart(?Chart $chart): static
    {
        $this->chart = $chart;

        return $this;
    }

    /**
     * Helper pour générer l'URL Spotify de la playlist.
     */
    public function getSpotifyUrl(): string
    {
        return sprintf('https://open.spotify.com/playlist/%s', $this->spotifyId);
    }
}
