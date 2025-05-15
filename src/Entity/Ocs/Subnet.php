<?php

namespace App\Entity\Ocs;

use App\Repository\Ocs\SubnetRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubnetRepository::class, readOnly: true)]
#[ORM\Table(name: "subnet")]
class Subnet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $pk;

    #[ORM\Column(type: "string", length: 15)]
    private string $netId;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $id = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $mask = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $tag = null;

    // Getters

    public function getPk(): int
    {
        return $this->pk;
    }

    public function getNetId(): string
    {
        return $this->netId;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getMask(): ?string
    {
        return $this->mask;
    }

    public function getTag(): ?string
    {
        return $this->tag;
    }

    /**
     * Set the value of pk
     *
     * @return  self
     */
    public function setPk($pk)
    {
        $this->pk = $pk;

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
     * Set the value of id
     *
     * @return  self
     */
    public function setId($id)
    {
        $this->id = $id;

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
     * Set the value of tag
     *
     * @return  self
     */
    public function setTag($tag)
    {
        $this->tag = $tag;

        return $this;
    }

    public function getAvailableHosts(): int
    {
        if (!$this->mask) {
            return 0;
        }
        $prefix = $this->maskToPrefix($this->mask);

        if ($prefix === null || $prefix >= 31) {
            return 0;
        }
        return (2 ** (32 - $prefix)) - 2;
    }
    private function maskToPrefix(string $mask): ?int
    {
        $parts = explode('.', $mask);
        if (count($parts) !== 4) {
            return null;
        }
        $binaryString = '';
        foreach ($parts as $part) {
            $binaryString .= str_pad(decbin((int)$part), 8, '0', STR_PAD_LEFT);
        }
        return substr_count($binaryString, '1');
    }




    public function getFirstUsableIp(): ?string
    {
        if (!$this->netId || !$this->mask) {
            return null;
        }

        $networkIp = ip2long($this->netId);
        return long2ip($networkIp + 1);
    }


    public function getLastUsableIp(): ?string
    {
        if (!$this->netId || !$this->mask) {
            return null;
        }

        $prefix = $this->maskToPrefix($this->mask);
        if ($prefix === null || $prefix >= 31) {
            return null;
        }

        $networkIp = ip2long($this->netId);
        $broadcastIp = $networkIp + (2 ** (32 - $prefix)) - 1;
        return long2ip($broadcastIp - 1);
    }
    
}
