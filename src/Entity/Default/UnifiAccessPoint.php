<?php

namespace App\Entity\Default;

use App\Repository\Default\UnifiAccessPointRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UnifiAccessPointRepository::class)]
class UnifiAccessPoint
{
    public const STATES = [
        '0' => 'Offline',
        '1' => 'Up to date',
        '2' => 'Managed by Another Console?',
        '10' => 'Adopting',
        '11' => 'Isolated',
        null => 'Unknown',
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $ip = null;

    #[ORM\Column]
    private ?string $state = null;

    #[ORM\Column(length: 255)]
    private ?string $mac = null;

    #[ORM\Column(length: 255)]
    private ?string $pingStatus = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $lastSuccessfullPing = null;

    #[ORM\Column(nullable: true)]
    private ?bool $excludeFromReport = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $lastTimeOnline = null;

    #[ORM\Column(nullable: true)]
    private ?bool $disabled = null;

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

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(string $ip): static
    {
        $this->ip = $ip;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): static
    {
        $this->state = $state;

        return $this;
    }

    public function getMac(): ?string
    {
        return $this->mac;
    }

    public function setMac(string $mac): static
    {
        $this->mac = $mac;

        return $this;
    }

    public function getPingStatus(): ?string
    {
        return $this->pingStatus;
    }

    public function setPingStatus(string $pingStatus): static
    {
        $this->pingStatus = $pingStatus;

        return $this;
    }

    public function getLastSuccessfullPing(): ?\DateTimeInterface
    {
        return $this->lastSuccessfullPing;
    }

    public function setLastSuccessfullPing(?\DateTimeInterface $lastSuccessfullPing): static
    {
        $this->lastSuccessfullPing = $lastSuccessfullPing;

        return $this;
    }

    public function isExcludeFromReport(): ?bool
    {
        return $this->excludeFromReport;
    }

    public function setExcludeFromReport(?bool $excludeFromReport): static
    {
        $this->excludeFromReport = $excludeFromReport;

        return $this;
    }

    public function getLastTimeOnline(): ?\DateTimeInterface
    {
        return $this->lastTimeOnline;
    }

    public function setLastTimeOnline(?\DateTimeInterface $lastTimeOnline): static
    {
        $this->lastTimeOnline = $lastTimeOnline;

        return $this;
    }

    public function isDisabled(): ?bool
    {
        return $this->disabled;
    }

    public function setDisabled(?bool $disabled): static
    {
        $this->disabled = $disabled;

        return $this;
    }
}
