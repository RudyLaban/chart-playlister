<?php

namespace App\Entity;

use App\Repository\PlaylistRepository;
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
     * @ORM\ManyToOne(targetEntity=Chart::class, inversedBy="playlists")
     * @ORM\JoinColumn(nullable=false)
     */
    private $chart;

    /**
     * @ORM\ManyToOne(targetEntity=StreamingSite::class, inversedBy="playlists")
     */
    private $StreamingSite;

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

    public function getChart(): ?Chart
    {
        return $this->chart;
    }

    public function setChart(?Chart $chart): self
    {
        $this->chart = $chart;

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
}
