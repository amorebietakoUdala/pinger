<?php

namespace App\Entity\Default;

use App\Repository\Default\VendorRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VendorRepository::class)]
class Vendor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $prefix = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $private = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $lastUpdate = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrefix(): ?string
    {
        return $this->prefix;
    }

    public function setPrefix(string $prefix): static
    {
        $this->prefix = $prefix;

        return $this;
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

    public function getPrivate(): ?string
    {
        return $this->private;
    }

    public function setPrivate(?string $private): static
    {
        $this->private = $private;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getLastUpdate(): ?\DateTimeInterface
    {
        return $this->lastUpdate;
    }

    public function setLastUpdate(\DateTimeInterface $lastUpdate): static
    {
        $this->lastUpdate = $lastUpdate;

        return $this;
    }

    public function fillFromRow(array $row): self {
        $vendor['prefix'] = $row['Mac Prefix'] ?? null;
        $vendor['name'] = $row['Vendor Name'] ?? null;
        $vendor['private'] = $row['Private'] ?? null;
        $vendor['type'] = $row['Block Type'] ?? null;
        $date = DateTime::createFromFormat('Y/m/d',$row['Last Update']);
        $vendor['lastUpdate'] = $date;

        return $this->fillFromArray($vendor);
            
    }

    public function fillFromArray(array $vendor): self {
        $this->prefix = $vendor['prefix'];
        $this->name = $vendor['name'];
        $this->private = $vendor['private'];
        $this->type = $vendor['type'];
        $this->lastUpdate = $vendor['lastUpdate'];
            
        return $this;
    }
}
