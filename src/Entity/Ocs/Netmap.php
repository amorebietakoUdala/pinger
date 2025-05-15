<?php

namespace App\Entity\Ocs;

use App\Repository\Ocs\NetmapRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NetmapRepository::class, readOnly: true)]
#[ORM\Table(name: "netmap")]
class Netmap
{
    #[ORM\Id]
    #[ORM\Column(type: "string", length: 17)]
    private string $mac;

    #[ORM\Column(type: "string", length: 15)]
    private string $ip;

    #[ORM\Column(type: "string", length: 15)]
    private string $mask;

    #[ORM\Column(type: "string", length: 15)]
    private string $netId;

    #[ORM\Column(type: "datetime_immutable", options: ["default" => "CURRENT_TIMESTAMP"])]
    private \DateTimeImmutable $date;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $tag = null;

    #[ORM\Column(name: "HARDWARE_ID", type: "integer", nullable: true )]
    private ?int $hardwareId = null;

    // Getters

    public function getMac(): string
    {
        return $this->mac;
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function getMask(): string
    {
        return $this->mask;
    }

    public function getNetId(): string
    {
        return $this->netId;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getTag(): ?string
    {
        return $this->tag;
    }

    /**
     * Set the value of mac
     *
     * @return  self
     */ 
    public function setMac($mac)
    {
        $this->mac = $mac;

        return $this;
    }

    /**
     * Set the value of ip
     *
     * @return  self
     */ 
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Set the value of mask
     *
     * @return  self
     */ 
    public function setMask($mask)
    {
        $this->mask = $mask;

        return $this;
    }

    /**
     * Set the value of netId
     *
     * @return  self
     */ 
    public function setNetId($netId)
    {
        $this->netId = $netId;

        return $this;
    }

    /**
     * Set the value of date
     *
     * @return  self
     */ 
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Set the value of name
     *
     * @return  self
     */ 
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Set the value of tag
     *
     * @return  self
     */ 
    public function setTag($tag)
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * Get the value of hardwareId
     */ 
    public function getHardwareId()
    {
        return $this->hardwareId;
    }

    /**
     * Set the value of hardwareId
     *
     * @return  self
     */ 
    public function setHardwareId($hardwareId)
    {
        $this->hardwareId = $hardwareId;

        return $this;
    }
}
