<?php

namespace App\Entity;

use App\Repository\ChartSongRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Cette Entité représente un élément dans la chart ou la playlist (une chanson)
 *
 * @ORM\Entity(repositoryClass=ChartSongRepository::class)
 */
class ChartSong
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $position;

    /**
     * @ORM\ManyToOne(targetEntity=Chart::class, inversedBy="chartSongs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $chart;

    /**
     * @ORM\ManyToOne(targetEntity=Song::class, inversedBy="chartSongs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $song;

    /**
     * @ORM\OneToMany(targetEntity=PlaylistChartSong::class, mappedBy="chartSong", orphanRemoval=true)
     */
    private $playlistChartSongs;

    public function __construct()
    {
        $this->playlistChartSongs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): self
    {
        $this->position = $position;

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

    public function getSong(): ?Song
    {
        return $this->song;
    }

    public function setSong(?Song $song): self
    {
        $this->song = $song;

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
            $playlistChartSong->setChartSong($this);
        }

        return $this;
    }

    public function removePlaylistChartSong(PlaylistChartSong $playlistChartSong): self
    {
        if ($this->playlistChartSongs->removeElement($playlistChartSong)) {
            // set the owning side to null (unless already changed)
            if ($playlistChartSong->getChartSong() === $this) {
                $playlistChartSong->setChartSong(null);
            }
        }

        return $this;
    }
}
