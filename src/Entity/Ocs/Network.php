<?php

namespace App\Entity\Ocs;

use App\Entity\Ocs\Hardware;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(readOnly: true)]
#[ORM\Table(name: "networks")]
class Network
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $type = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $typeMib = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $speed = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $mtu = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $macAddr = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $status = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $ipAddress = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $ipMask = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $ipGateway = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $ipSubnet = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $ipDhcp = null;

    #[ORM\Column(type: "boolean", options: ["default" => false])]
    private bool $virtualDev;

    #[ORM\ManyToOne(inversedBy: 'networks')]
    private ?Hardware $hardware = null;

    // Getters

    public function getId(): int
    {
        return $this->id;
    }

    public function getHardware(): Hardware
    {
        return $this->hardware;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getTypeMib(): ?string
    {
        return $this->typeMib;
    }

    public function getSpeed(): ?string
    {
        return $this->speed;
    }

    public function getMtu(): ?string
    {
        return $this->mtu;
    }

    public function getMacAddr(): ?string
    {
        return $this->macAddr;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function getIpAddress(): ?string
    {
        return $this->ipAddress;
    }

    public function getIpMask(): ?string
    {
        return $this->ipMask;
    }

    public function getIpGateway(): ?string
    {
        return $this->ipGateway;
    }

    public function getIpSubnet(): ?string
    {
        return $this->ipSubnet;
    }

    public function getIpDhcp(): ?string
    {
        return $this->ipDhcp;
    }

    public function isVirtualDev(): bool
    {
        return $this->virtualDev;
    }


    // Setters

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function setHardware(?Hardware $hardware)
    {
        $this->hardware = $hardware;

        return $this;
    }

    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    public function setTypeMib($typeMib)
    {
        $this->typeMib = $typeMib;

        return $this;
    }

    public function setSpeed($speed)
    {
        $this->speed = $speed;

        return $this;
    }

    public function setMtu($mtu)
    {
        $this->mtu = $mtu;

        return $this;
    }

    public function setMacAddr($macAddr)
    {
        $this->macAddr = $macAddr;

        return $this;
    }

    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    public function setIpAddress($ipAddress)
    {
        $this->ipAddress = $ipAddress;

        return $this;
    }

    public function setIpMask($ipMask)
    {
        $this->ipMask = $ipMask;

        return $this;
    }

    public function setIpGateway($ipGateway)
    {
        $this->ipGateway = $ipGateway;

        return $this;
    }

    public function setIpSubnet($ipSubnet)
    {
        $this->ipSubnet = $ipSubnet;

        return $this;
    }

    public function setIpDhcp($ipDhcp)
    {
        $this->ipDhcp = $ipDhcp;

        return $this;
    }

    public function setVirtualDev($virtualDev)
    {
        $this->virtualDev = $virtualDev;

        return $this;
    }
}