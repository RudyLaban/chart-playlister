<?php

namespace App\Entity;

use App\Repository\ChartSiteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Cette Entité représente un site de classement musical (site de chart)
 *
 * @ORM\Entity(repositoryClass=ChartSiteRepository::class)
 */
class ChartSite
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
     * @ORM\OneToMany(targetEntity=Chart::class, mappedBy="chartSite")
     */
    private $charts;

    public function __construct()
    {
        $this->charts = new ArrayCollection();
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

    /**
     * @return Collection|Chart[]
     */
    public function getCharts(): Collection
    {
        return $this->charts;
    }

    public function addChart(Chart $chart): self
    {
        if (!$this->charts->contains($chart)) {
            $this->charts[] = $chart;
            $chart->setChartSite($this);
        }

        return $this;
    }

    public function removeChart(Chart $chart): self
    {
        if ($this->charts->removeElement($chart)) {
            // set the owning side to null (unless already changed)
            if ($chart->getChartSite() === $this) {
                $chart->setChartSite(null);
            }
        }

        return $this;
    }
}
