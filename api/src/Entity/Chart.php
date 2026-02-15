<?php

namespace App\Entity;

use App\Repository\ChartRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: ChartRepository::class)]
#[ORM\UniqueConstraint(name: 'chart_provider_slug_unique', columns: ['provider_id', 'slug'])]
#[UniqueEntity(fields: ['provider', 'slug'], message: 'Cette chart existe déjà pour ce provider')]
class Chart
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['chart:read', 'entry:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'Le nom du chart est obligatoire')]
    #[Assert\Length(max: 100)]
    #[Groups(['chart:read', 'chart:write', 'entry:read'])]
    private ?string $name = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'Le slug est obligatoire')]
    #[Assert\Length(max: 100)]
    #[Assert\Regex(
        pattern: '/^[a-z0-9-]+$/',
        message: 'Le slug ne peut contenir que des lettres minuscules, chiffres et tirets'
    )]
    #[Groups(['chart:read', 'chart:write'])]
    private ?string $slug = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Url(message: 'L\'URL de l\'image n\'est pas valide')]
    #[Groups(['chart:read', 'chart:write'])]
    private ?string $imageUrl = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Url(message: 'L\'URL de scraping n\'est pas valide')]
    #[Groups(['chart:read', 'chart:write'])]
    private ?string $scrapingUrl = null;

    #[ORM\Column]
    #[Groups(['chart:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'charts')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'Le provider est obligatoire')]
    #[Groups(['chart:read', 'chart:write'])]
    private ?ChartProvider $provider = null;

    /**
     * @var Collection<int, ChartEntry>
     */
    #[ORM\OneToMany(targetEntity: ChartEntry::class, mappedBy: 'chart', orphanRemoval: true)]
    private Collection $entries;

    /**
     * @var Collection<int, SpotifyPlaylist>
     */
    #[ORM\OneToMany(targetEntity: SpotifyPlaylist::class, mappedBy: 'chart')]
    private Collection $spotifyPlaylists;


    public function __construct()
    {
    $this->createdAt = new \DateTimeImmutable();
    $this->entries = new ArrayCollection();
    $this->spotifyPlaylists = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name ?? 'Chart';
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

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(?string $imageUrl): static
    {
        $this->imageUrl = $imageUrl;
        return $this;
    }

    public function getScrapingUrl(): ?string
    {
        return $this->scrapingUrl;
    }

    public function setScrapingUrl(?string $scrapingUrl): static
    {
        $this->scrapingUrl = $scrapingUrl;
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

    public function getProvider(): ?ChartProvider
    {
        return $this->provider;
    }

    public function setProvider(?ChartProvider $provider): static
    {
        $this->provider = $provider;
        return $this;
    }

    /**
     * @return Collection<int, ChartEntry>
     */
    public function getEntries(): Collection
    {
        return $this->entries;
    }

    public function addEntry(ChartEntry $entry): static
    {
        if (!$this->entries->contains($entry)) {
            $this->entries->add($entry);
            $entry->setChart($this);
        }

        return $this;
    }

    public function removeEntry(ChartEntry $entry): static
    {
        if ($this->entries->removeElement($entry)) {
            // set the owning side to null (unless already changed)
            if ($entry->getChart() === $this) {
                $entry->setChart(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, SpotifyPlaylist>
     */
    public function getSpotifyPlaylists(): Collection
    {
        return $this->spotifyPlaylists;
    }

    public function addSpotifyPlaylist(SpotifyPlaylist $spotifyPlaylist): static
    {
        if (!$this->spotifyPlaylists->contains($spotifyPlaylist)) {
            $this->spotifyPlaylists->add($spotifyPlaylist);
            $spotifyPlaylist->setChart($this);
        }

        return $this;
    }

    public function removeSpotifyPlaylist(SpotifyPlaylist $spotifyPlaylist): static
    {
        if ($this->spotifyPlaylists->removeElement($spotifyPlaylist)) {
            // set the owning side to null (unless already changed)
            if ($spotifyPlaylist->getChart() === $this) {
                $spotifyPlaylist->setChart(null);
            }
        }

        return $this;
    }
}
