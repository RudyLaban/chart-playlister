<?php

namespace App\Entity;

use App\Repository\PlaylistRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Cette Entité représente une playlist enregistrée sur un site de streaming
 *
 * @ORM\Entity(repositoryClass=PlaylistRepository::class)
 */
class Playlist
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * @ORM\ManyToOne(targetEntity=StreamingSite::class, inversedBy="playlists")
     */
    private $StreamingSite;

    /**
     * @ORM\OneToMany(targetEntity=PlaylistChartSong::class, mappedBy="playlist", orphanRemoval=true)
     */
    private $playlistChartSongs;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $externalId;

    /**
     * @ORM\ManyToOne(targetEntity=Chart::class, inversedBy="playlists")
     */
    private $chart;

    public function __construct()
    {
        $this->playlistChartSongs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getStreamingSite(): ?StreamingSite
    {
        return $this->StreamingSite;
    }

    public function setStreamingSite(?StreamingSite $StreamingSite): self
    {
        $this->StreamingSite = $StreamingSite;

        return $this;
    }

    /**
     * @return Collection|PlaylistChartSong[]
     */
    public function getPlaylistChartSongs(): Collection
    {
        return $this->playlistChartSongs;
    }

    public function addPlaylistChartSong(PlaylistChartSong $playlistChartSong): self
    {
        if (!$this->playlistChartSongs->contains($playlistChartSong)) {
            $this->playlistChartSongs[] = $playlistChartSong;
            $playlistChartSong->setPlaylist($this);
        }

        return $this;
    }

    public function removePlaylistChartSong(PlaylistChartSong $playlistChartSong): self
    {
        if ($this->playlistChartSongs->removeElement($playlistChartSong)) {
            // set the owning side to null (unless already changed)
            if ($playlistChartSong->getPlaylist() === $this) {
                $playlistChartSong->setPlaylist(null);
            }
        }

        return $this;
    }

    public function getExternalId(): ?string
    {
        return $this->externalId;
    }

    public function setExternalId(string $externalId): self
    {
        $this->externalId = $externalId;

        return $this;
    }

    public function getChart(): ?Chart
    {
        return $this->chart;
    }

    public function setChart(?Chart $chart): self
    {
        $this->chart = $chart;

        return $this;
    }
}
