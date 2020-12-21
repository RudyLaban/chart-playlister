<?php

namespace App\Entity;

use App\Repository\ChartRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Cette Entité représente une chart, classement venant du site de classement (site de chart ; ChartSite)
 *
 * @ORM\Entity(repositoryClass=ChartRepository::class)
 */
class Chart
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
     * @ORM\Column(type="string", length=255)
     */
    private $url;

    /**
     * @ORM\ManyToOne(targetEntity=ChartSite::class, inversedBy="charts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $chartSite;

    /**
     * @ORM\OneToMany(targetEntity=ChartSong::class, mappedBy="chart")
     */
    private $chartSongs;

    /**
     * @ORM\OneToMany(targetEntity=Playlist::class, mappedBy="chart")
     */
    private $playlists;

    public function __construct()
    {
        $this->chartSongs = new ArrayCollection();
        $this->playlists = new ArrayCollection();
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

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getChartSite(): ?ChartSite
    {
        return $this->chartSite;
    }

    public function setChartSite(?ChartSite $chartSite): self
    {
        $this->chartSite = $chartSite;

        return $this;
    }

    /**
     * @return Collection|ChartSong[]
     */
    public function getChartSongs(): Collection
    {
        return $this->chartSongs;
    }

    public function addChartSong(ChartSong $chartSong): self
    {
        if (!$this->chartSongs->contains($chartSong)) {
            $this->chartSongs[] = $chartSong;
            $chartSong->setChart($this);
        }

        return $this;
    }

    public function removeChartSong(ChartSong $chartSong): self
    {
        if ($this->chartSongs->removeElement($chartSong)) {
            // set the owning side to null (unless already changed)
            if ($chartSong->getChart() === $this) {
                $chartSong->setChart(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Playlist[]
     */
    public function getPlaylists(): Collection
    {
        return $this->playlists;
    }

    public function addPlaylist(Playlist $playlist): self
    {
        if (!$this->playlists->contains($playlist)) {
            $this->playlists[] = $playlist;
            $playlist->setChart($this);
        }

        return $this;
    }

    public function removePlaylist(Playlist $playlist): self
    {
        if ($this->playlists->removeElement($playlist)) {
            // set the owning side to null (unless already changed)
            if ($playlist->getChart() === $this) {
                $playlist->setChart(null);
            }
        }

        return $this;
    }
}
