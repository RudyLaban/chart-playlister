<?php

namespace App\Entity;

use App\Repository\SongRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: SongRepository::class)]
#[UniqueEntity(fields: ['spotifyId'], message: 'Cette chanson Spotify existe déjà')]
class Song
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['song:read', 'entry:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le titre est obligatoire')]
    #[Assert\Length(max: 255)]
    #[Groups(['song:read', 'song:write', 'entry:read'])]
    private ?string $title = null;

    #[ORM\Column(length: 100, unique: true, nullable: true)]
    #[Assert\Length(max: 100)]
    #[Groups(['song:read', 'song:write'])]
    private ?string $spotifyId = null;

    #[ORM\Column]
    #[Groups(['song:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * @var Collection<int, Artist>
     */
    #[ORM\ManyToMany(targetEntity: Artist::class, inversedBy: 'songs')]
    #[ORM\JoinTable(name: 'song_artist')]
    #[Assert\Count(min: 1, minMessage: 'Une chanson doit avoir au moins un artiste')]
    #[Groups(['song:read', 'song:write'])]
    private Collection $artists;

    public function __construct()
    {
        $this->artists = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function __toString(): string
    {
        return $this->title ?? 'Song';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getSpotifyId(): ?string
    {
        return $this->spotifyId;
    }

    public function setSpotifyId(?string $spotifyId): static
    {
        $this->spotifyId = $spotifyId;
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

    /**
     * @return Collection<int, Artist>
     */
    public function getArtists(): Collection
    {
        return $this->artists;
    }

    public function addArtist(Artist $artist): static
    {
        if (!$this->artists->contains($artist)) {
            $this->artists->add($artist);
        }

        return $this;
    }

    public function removeArtist(Artist $artist): static
    {
        $this->artists->removeElement($artist);
        return $this;
    }

    /**
     * Helper pour récupérer les noms des artistes sous forme de string
     */
    public function getArtistsNames(): string
    {
        return implode(', ', $this->artists->map(fn(Artist $a) => $a->getName())->toArray());
    }
}
