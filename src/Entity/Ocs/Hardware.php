<?php

namespace App\Entity\Ocs;

use App\Repository\Ocs\HardwareRepository;
use App\Entity\Ocs\Network;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'hardware')]
#[ORM\Entity(repositoryClass: HardwareRepository::class, readOnly: true)]
class Hardware
{
    #[ORM\Id]
    #[ORM\Column]
    private ?int $id;

    #[ORM\Column(name: 'deviceid', length: 255)]
    private ?string $deviceid = null;

    #[ORM\Column(name: 'name', length: 255)]
    private ?string $name = null;

    #[ORM\Column(name: 'workgroup', length: 255)]
    private ?string $workgroup = null;

    #[ORM\Column(name: 'userdomain', length: 255)]
    private ?string $userdomain = null;

    #[ORM\Column(name: 'osname', length: 255)]
    private ?string $osname = null;

    #[ORM\Column(name: 'osversion', length: 255)]
    private ?string $osversion = null;

    #[ORM\Column(name: 'oscomments', length: 255)]
    private ?string $oscomments = null;

    #[ORM\Column(name: 'processort', length: 255)]
    private ?string $processort = null;

    #[ORM\Column(name: 'processors')]
    private ?int $processors;

    #[ORM\Column(name: 'processorn')]
    private ?int $processorn;

    #[ORM\Column(name: 'swap')]
    private ?int $swap;

    #[ORM\Column(name: 'memory')]
    private ?int $memory;

    #[ORM\Column(name: 'ipaddr', length: 255)]
    private ?string $ipaddr = null;

    #[ORM\Column(name: 'dns', length: 255)]
    private ?string $dns = null;

    #[ORM\Column(name: 'defaultgateway', length: 255)]
    private ?string $defaultgateway = null;

    #[ORM\Column(name: 'etime')]
    private ?\DateTime $etime = null;

    #[ORM\Column(name: 'lastdate')]
    private ?\DateTime $lastdate = null;

    #[ORM\Column(name: 'lastcome')]
    private ?\DateTime $lastcome = null;

    #[ORM\Column(name: 'quality', type: 'decimal', precision: 7, scale: 4)]
    private $quality;

    #[ORM\Column(name: 'fidelity', type: "bigint", length: 20)]
    private int $fidelity;

    #[ORM\Column(name: 'userid', length: 255)]
    private ?string $userid = null;

    #[ORM\Column(name: 'type')]
    private ?int $type;

    #[ORM\Column(name: 'description', length: 255)]
    private ?string $description = null;

    #[ORM\Column(name: 'wincompany', length: 255)]
    private ?string $wincompany = null;

    #[ORM\Column(name: 'winowner', length: 255)]
    private ?string $winowner = null;

    #[ORM\Column(name: 'winprodid', length: 255)]
    private ?string $winprodid = null;

    #[ORM\Column(name: 'winprodkey', length: 255)]
    private ?string $winprodkey = null;

    #[ORM\Column(name: 'useragent', length: 50)]
    private ?string $useragent = null;

    #[ORM\Column(name: 'checksum', type: "bigint", length: 20)]
    private int $checksum;

    #[ORM\Column(name: 'sstate')]
    private ?int $sstate;

    #[ORM\Column(name: 'ipsrc', length: 255)]
    private ?string $ipsrc = null;

    #[ORM\Column(name: 'uuid', length: 255)]
    private ?string $uuid = null;

    #[ORM\Column(name: 'arch', length: 10)]
    private ?string $arch = null;
    
    #[ORM\Column(name: 'category_id')]
    private ?int $category_id;

    #[ORM\Column(name: 'archive')]
    private ?int $archive;

    /**
     * @var Collection<int, Network>
     */
    #[ORM\OneToMany(targetEntity: Network::class, mappedBy: 'hardware')]
    private Collection $networks;

    public function __construct()
    {
        $this->networks = new ArrayCollection();
    }

    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
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
     * Get the value of deviceid
     */ 
    public function getDeviceid()
    {
        return $this->deviceid;
    }

    /**
     * Set the value of deviceid
     *
     * @return  self
     */ 
    public function setDeviceid($deviceid)
    {
        $this->deviceid = $deviceid;

        return $this;
    }

    /**
     * Get the value of name
     */ 
    public function getName()
    {
        return $this->name;
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
     * Get the value of wordgroup
     */ 
    public function getWorkgroup()
    {
        return $this->workgroup;
    }

    /**
     * Set the value of wordgroup
     *
     * @return  self
     */ 
    public function setWorkgroup($workgroup)
    {
        $this->workgroup = $workgroup;

        return $this;
    }

    /**
     * Get the value of userdomain
     */ 
    public function getUserdomain()
    {
        return $this->userdomain;
    }

    /**
     * Set the value of userdomain
     *
     * @return  self
     */ 
    public function setUserdomain($userdomain)
    {
        $this->userdomain = $userdomain;

        return $this;
    }

    /**
     * Set the value of osname
     *
     * @return  self
     */ 
    public function setOsname($osname)
    {
        $this->osname = $osname;

        return $this;
    }

    /**
     * Get the value of osname
     */ 
    public function getOsname()
    {
        return $this->osname;
    }

    /**
     * Get the value of osversion
     */ 
    public function getOsversion()
    {
        return $this->osversion;
    }

    /**
     * Get the value of oscomments
     */ 
    public function getOscomments()
    {
        return $this->oscomments;
    }

    /**
     * Set the value of oscomments
     *
     * @return  self
     */ 
    public function setOscomments($oscomments)
    {
        $this->oscomments = $oscomments;

        return $this;
    }

    /**
     * Get the value of processort
     */ 
    public function getProcessort()
    {
        return $this->processort;
    }

    /**
     * Get the value of processors
     */ 
    public function getProcessors()
    {
        return $this->processors;
    }

    /**
     * Get the value of processorn
     */ 
    public function getProcessorn()
    {
        return $this->processorn;
    }

    /**
     * Get the value of swap
     */ 
    public function getSwap()
    {
        return $this->swap;
    }

    /**
     * Get the value of memory
     */ 
    public function getMemory()
    {
        return $this->memory;
    }

    /**
     * Get the value of ipaddr
     */ 
    public function getIpaddr()
    {
        return $this->ipaddr;
    }

    /**
     * Get the value of ipaddr
     */ 
    public function getIp()
    {
        return $this->ipaddr;
    }


    /**
     * Get the value of dns
     */ 
    public function getDns()
    {
        return $this->dns;
    }

    /**
     * Get the value of defaultgateway
     */ 
    public function getDefaultgateway()
    {
        return $this->defaultgateway;
    }

    /**
     * Get the value of etime
     */ 
    public function getEtime()
    {
        return $this->etime;
    }

    /**
     * Get the value of lastdate
     */ 
    public function getLastdate()
    {
        return $this->lastdate;
    }

    /**
     * Get the value of lastcome
     */ 
    public function getLastcome()
    {
        return $this->lastcome;
    }

    /**
     * Get the value of quality
     */ 
    public function getQuality()
    {
        return $this->quality;
    }

    /**
     * Get the value of fidelity
     */ 
    public function getFidelity()
    {
        return $this->fidelity;
    }

    /**
     * Get the value of userid
     */ 
    public function getUserid()
    {
        return $this->userid;
    }

    /**
     * Get the value of type
     */ 
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get the value of description
     */ 
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Get the value of wincompany
     */ 
    public function getWincompany()
    {
        return $this->wincompany;
    }

    /**
     * Get the value of winowner
     */ 
    public function getWinowner()
    {
        return $this->winowner;
    }

    /**
     * Get the value of winprodid
     */ 
    public function getWinprodid()
    {
        return $this->winprodid;
    }

    /**
     * Get the value of winprodkey
     */ 
    public function getWinprodkey()
    {
        return $this->winprodkey;
    }

    /**
     * Get the value of useragent
     */ 
    public function getUseragent()
    {
        return $this->useragent;
    }

    /**
     * Get the value of checksum
     */ 
    public function getChecksum()
    {
        return $this->checksum;
    }

    /**
     * Set the value of checksum
     *
     * @return  self
     */ 
    public function setChecksum($checksum)
    {
        $this->checksum = $checksum;

        return $this;
    }

    /**
     * Get the value of sstate
     */ 
    public function getSstate()
    {
        return $this->sstate;
    }

    /**
     * Set the value of sstate
     *
     * @return  self
     */ 
    public function setSstate($sstate)
    {
        $this->sstate = $sstate;

        return $this;
    }

    /**
     * Get the value of ipsrc
     */ 
    public function getIpsrc()
    {
        return $this->ipsrc;
    }

    /**
     * Set the value of ipsrc
     *
     * @return  self
     */ 
    public function setIpsrc($ipsrc)
    {
        $this->ipsrc = $ipsrc;

        return $this;
    }

    /**
     * Get the value of uuid
     */ 
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Set the value of uuid
     *
     * @return  self
     */ 
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * Get the value of arch
     */ 
    public function getArch()
    {
        return $this->arch;
    }

    /**
     * Set the value of arch
     *
     * @return  self
     */ 
    public function setArch($arch)
    {
        $this->arch = $arch;

        return $this;
    }

    /**
     * Get the value of category_id
     */ 
    public function getCategory_id()
    {
        return $this->category_id;
    }

    /**
     * Set the value of category_id
     *
     * @return  self
     */ 
    public function setCategory_id($category_id)
    {
        $this->category_id = $category_id;

        return $this;
    }

    /**
     * Get the value of archive
     */ 
    public function getArchive()
    {
        return $this->archive;
    }

    /**
     * Set the value of archive
     *
     * @return  self
     */ 
    public function setArchive($archive)
    {
        $this->archive = $archive;

        return $this;
    }

    /**
     * @return Collection<int, Network>
     */
    public function getNetworks(): Collection
    {
        return $this->networks;
    }

    public function addNetwork(Network $network): static
    {
        if (!$this->networks->contains($network)) {
            $this->networks->add($network);
            $network->setHardware($this);
        }

        return $this;
    }

    public function removeNetwork(Network $network): static
    {
        if ($this->networks->removeElement($network)) {
            // set the owning side to null (unless already changed)
            if ($network->getHardware() === $this) {
                $network->setHardware(null);
            }
        }

        return $this;
    }

    public function getHostname(): string {
        return $this->name;
    }

    public function getFirstUpNetwork(): ?Network {
        foreach ($this->networks as $network) {
            if ( $network->getStatus() === 'Up') {
                return $network;
            }
        }
        return null;
    }
}
