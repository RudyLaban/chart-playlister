<?php

namespace App\Entity;

use App\Repository\ChartProviderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: ChartProviderRepository::class)]
#[UniqueEntity(fields: ['name'], message: 'Ce nom de provider existe déjà')]
#[UniqueEntity(fields: ['slug'], message: 'Ce slug existe déjà')]
class ChartProvider
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['provider:read', 'chart:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 100, unique: true)]
    #[Assert\NotBlank(message: 'Le nom du provider est obligatoire')]
    #[Assert\Length(max: 100)]
    #[Groups(['provider:read', 'provider:write', 'chart:read'])]
    private ?string $name = null;

    #[ORM\Column(length: 100, unique: true)]
    #[Assert\NotBlank(message: 'Le slug est obligatoire')]
    #[Assert\Length(max: 100)]
    #[Assert\Regex(
        pattern: '/^[a-z0-9-]+$/',
        message: 'Le slug ne peut contenir que des lettres minuscules, chiffres et tirets'
    )]
    #[Groups(['provider:read', 'provider:write'])]
    private ?string $slug = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Url(message: 'L\'URL de base n\'est pas valide')]
    #[Groups(['provider:read', 'provider:write'])]
    private ?string $baseUrl = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['provider:read', 'provider:write'])]
    private ?array $scrapingConfig = null;

    #[ORM\Column(options: ['default' => true])]
    #[Groups(['provider:read', 'provider:write'])]
    private ?bool $active = null;

    #[ORM\Column]
    #[Groups(['provider:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * @var Collection<int, Chart>
     */
    #[ORM\OneToMany(targetEntity: Chart::class, mappedBy: 'provider', orphanRemoval: true)]
    private Collection $charts;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->charts = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name ?? 'ChartProvider';
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

    public function getBaseUrl(): ?string
    {
        return $this->baseUrl;
    }

    public function setBaseUrl(?string $baseUrl): static
    {
        $this->baseUrl = $baseUrl;
        return $this;
    }

    public function getScrapingConfig(): ?array
    {
        return $this->scrapingConfig;
    }

    public function setScrapingConfig(?array $scrapingConfig): static
    {
        $this->scrapingConfig = $scrapingConfig;
        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;
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
     * @return Collection<int, Chart>
     */
    public function getCharts(): Collection
    {
        return $this->charts;
    }

    public function addChart(Chart $chart): static
    {
        if (!$this->charts->contains($chart)) {
            $this->charts->add($chart);
            $chart->setProvider($this);
        }

        return $this;
    }

    public function removeChart(Chart $chart): static
    {
        if ($this->charts->removeElement($chart)) {
            // set the owning side to null (unless already changed)
            if ($chart->getProvider() === $this) {
                $chart->setProvider(null);
            }
        }

        return $this;
    }
}
