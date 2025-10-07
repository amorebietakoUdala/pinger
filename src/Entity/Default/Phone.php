<?php

namespace App\Entity\Default;

use App\Repository\Default\PhoneRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: PhoneRepository::class)]
class Phone
{

    use TimestampableEntity;
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $mac = null;

    #[ORM\Column(length: 255)]
    private ?string $ip = null;

    #[ORM\Column(length: 255)]
    private ?string $extension = null;

    #[ORM\Column(length: 255)]
    private ?string $model = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $firmware = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(string $ip): static
    {
        $this->ip = $ip;

        return $this;
    }

    public function getExtension(): ?string
    {
        return $this->extension;
    }

    public function setExtension(string $extension): static
    {
        $this->extension = $extension;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function getFirmware(): ?string
    {
        return $this->firmware;
    }

    public function setFirmware(string $firmware): static
    {
        $this->firmware = $firmware;

        return $this;
    }


    public function fillFromArray(array $data)
    {
        if (isset($data['mac'])) {
            $this->setMac($data['mac']);
        }
        if (isset($data['ip'])) {
            $this->setIp($data['ip']);
        }
        if (isset($data['local'])) {
            $this->setExtension($this->extractExtensionFromLocal($data['local']));
        }
        if (isset($data['model'])) {
            $this->setModel($data['model']);
        }
        if (isset($data['firmware'])) {
            $this->setFirmware($data['firmware']);
        }

        return $this;
    }

    public static function createFromArray(array $data)
    {
        return (new self())->fillFromArray($data);
    }

    private function extractExtensionFromLocal($local) {
        if (preg_match('/:(.*?)@/', $local, $coincidencias)) {
            return $coincidencias[1];
        }
        return null; // o puedes lanzar una excepción si prefieres
    }
    
}
