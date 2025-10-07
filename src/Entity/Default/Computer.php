<?php

namespace App\Entity\Default;

use App\Repository\Default\ComputerRepository;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Ldap\Entry;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: ComputerRepository::class)]
#[ORM\Index(columns: ['hostname'])]
#[ORM\Index(columns: ['ip'])]

class Computer
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255 , nullable: true)]

    private ?string $hostname = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ip = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $lastSucessfullPing = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $mac = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $lastInventory = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $lastOcsContact = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $origin = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $hardwareId = null;

    #[ORM\Column(nullable: true)]
    private ?bool $necessary = null;

    #[ORM\Column(length: 4096, nullable: true)]
    private ?string $Notes = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHostname(): ?string
    {
        return $this->hostname;
    }

    public function setHostname(string $hostname): static
    {
        $this->hostname = $hostname;

        return $this;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(?string $ip): static
    {
        $this->ip = $ip;

        return $this;
    }

    public static function createFromLdapEntry (Entry $entry) {
        $computer = new Computer();
        $computer->setHostname($entry->getAttribute('cn')[0]);
        return $computer;
    }

    public function __toString() {
        return $this->hostname;
    }

    public function getLastSucessfullPing(): ?\DateTimeInterface
    {
        return $this->lastSucessfullPing;
    }

    public function setLastSucessfullPing(?\DateTimeInterface $lastSucessfullPing): static
    {
        $this->lastSucessfullPing = $lastSucessfullPing;

        return $this;
    }

    public function getMac(): ?string
    {
        return $this->mac;
    }

    public function setMac(?string $mac): static
    {
        $this->mac = $mac;

        return $this;
    }

    public function getLastInventory(): ?\DateTimeInterface
    {
        return $this->lastInventory;
    }

    public function setLastInventory(?\DateTimeInterface $lastInventory): static
    {
        $this->lastInventory = $lastInventory;

        return $this;
    }

    public function getLastOcsContact(): ?\DateTimeInterface
    {
        return $this->lastOcsContact;
    }

    public function setLastOcsContact(?\DateTimeInterface $lastOcsContact): static
    {
        $this->lastOcsContact = $lastOcsContact;

        return $this;
    }

    public function getOrigin(): ?string
    {
        return $this->origin;
    }

    public function setOrigin(?string $origin): static
    {
        $this->origin = $origin;

        return $this;
    }

    public function gethardwareId(): ?string
    {
        return $this->hardwareId;
    }

    public function sethardwareId(?string $hardwareId): static
    {
        $this->hardwareId = $hardwareId;

        return $this;
    }

    public function isNecessary(): ?bool
    {
        return $this->necessary;
    }

    public function setNecessary(?bool $necessary): static
    {
        $this->necessary = $necessary;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->Notes;
    }

    public function setNotes(?string $Notes): static
    {
        $this->Notes = $Notes;

        return $this;
    }
}
