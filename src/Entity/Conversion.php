<?php

namespace App\Entity;

use App\Repository\ConversionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConversionRepository::class)]
class Conversion
{
    public const STATUSES = [ "PENDING", "IN_CONVERSION", "FINISHED" ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $startedAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $finishedAt = null;

    #[ORM\Column(length: 20)]
    private ?string $status = null;

    #[ORM\Column(length: 3)]
    private ?string $format = null;

    #[ORM\ManyToOne(inversedBy: 'conversions')]
    private ?User $user = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $ytUrl = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartedAt(): ?\DateTimeImmutable
    {
        return $this->startedAt;
    }

    public function setStartedAt(\DateTimeImmutable $startedAt): self
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    public function getFinishedAt(): ?\DateTimeImmutable
    {
        return $this->finishedAt;
    }

    public function setFinishedAt(\DateTimeImmutable $finishedAt): self
    {
        $this->finishedAt = $finishedAt;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getFormat(): ?string
    {
        return $this->format;
    }

    public function setFormat(string $format): self
    {
        $this->format = $format;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getYtUrl(): ?string
    {
        return $this->ytUrl;
    }

    public function setYtUrl(string $ytUrl): self
    {
        $this->ytUrl = $ytUrl;

        return $this;
    }
}
