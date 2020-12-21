<?php

namespace App\Entity;

use App\Repository\ChartSongRepository;
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
}
