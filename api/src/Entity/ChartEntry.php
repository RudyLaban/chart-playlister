<?php

namespace App\Entity;

use App\Repository\ChartEntryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ChartEntryRepository::class)]
#[ORM\UniqueConstraint(
    name: 'chart_song_snapshot_unique',
    columns: ['chart_id', 'song_id', 'snapshot_date']
)]
#[UniqueEntity(
    fields: ['chart', 'song', 'snapshotDate'],
    message: 'Cette chanson existe déjà dans cette chart à cette date'
)]
class ChartEntry
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['entry:read'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotNull(message: 'La position est obligatoire')]
    #[Assert\Positive(message: 'La position doit être un nombre positif')]
    #[Groups(['entry:read', 'entry:write'])]
    private ?int $position = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotNull(message: 'La date du snapshot est obligatoire')]
    #[Groups(['entry:read', 'entry:write'])]
    private ?\DateTime $snapshotDate = null;

    #[ORM\Column]
    #[Groups(['entry:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'entries')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'Le chart est obligatoire')]
    #[Groups(['entry:read', 'entry:write'])]
    private ?Chart $chart = null;

    #[ORM\ManyToOne(inversedBy: 'chartEntries')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'La chanson est obligatoire')]
    #[Groups(['entry:read', 'entry:write'])]
    private ?Song $song = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function __toString(): string
    {
        return sprintf(
            '#%d - %s (%s)',
            $this->position,
            $this->song?->getTitle() ?? 'Song',
            $this->snapshotDate?->format('Y-m-d') ?? 'Date'
        );
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function getSnapshotDate(): ?\DateTime
    {
        return $this->snapshotDate;
    }

    public function setSnapshotDate(\DateTime $snapshotDate): static
    {
        $this->snapshotDate = $snapshotDate;

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

    public function getChart(): ?Chart
    {
        return $this->chart;
    }

    public function setChart(?Chart $chart): static
    {
        $this->chart = $chart;

        return $this;
    }

    public function getSong(): ?Song
    {
        return $this->song;
    }

    public function setSong(?Song $song): static
    {
        $this->song = $song;

        return $this;
    }
}
