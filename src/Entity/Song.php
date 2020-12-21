<?php

namespace App\Entity;

use App\Repository\SongRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Cette Entité représente une chanson
 *
 * @ORM\Entity(repositoryClass=SongRepository::class)
 */
class Song
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
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity=Artist::class, inversedBy="songs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $artist;

    /**
     * @ORM\OneToMany(targetEntity=ChartSong::class, mappedBy="song")
     */
    private $chartSongs;

    public function __construct()
    {
        $this->chartSongs = new ArrayCollection();
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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getArtist(): ?Artist
    {
        return $this->artist;
    }

    public function setArtist(?Artist $artist): self
    {
        $this->artist = $artist;

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
            $chartSong->setSong($this);
        }

        return $this;
    }

    public function removeChartSong(ChartSong $chartSong): self
    {
        if ($this->chartSongs->removeElement($chartSong)) {
            // set the owning side to null (unless already changed)
            if ($chartSong->getSong() === $this) {
                $chartSong->setSong(null);
            }
        }

        return $this;
    }

}
