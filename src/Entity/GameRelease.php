<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="game_releases")
 * @ORM\Entity
 */
class GameRelease
{
    public const PLATFORMS = [
        'pc' => 'PC',
        'vr' => 'VR',
        'ps3' => 'PS3',
        'ps4' => 'PS4',
        'ps5' => 'PS5',
        'vita' => 'Vita',
        'psn' => 'PSN',
        'x360' => '360',
        'xb1' => 'XB1',
        'xsx' => 'XSX',
        'xbla' => 'XBLA',
        'wii' => 'Wii',
        'wiiu' => 'Wii U',
        'switch' => 'Switch',
        'wiiware' => 'WiiWare',
        'n3ds' => '3DS',
        'mobile' => 'Mobile'
    ];

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100)
     */
    private $name;

    /**
     * @var bool
     *
     * @ORM\Column(name="notable", type="boolean", nullable=false)
     */
    private $notable = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="manually_added", type="boolean", nullable=false)
     */
    private $manuallyAdded = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="pc", type="boolean", nullable=false)
     */
    private $pc = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="ps3", type="boolean", nullable=false)
     */
    private $ps3 = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="ps4", type="boolean", nullable=false)
     */
    private $ps4 = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="ps5", type="boolean", nullable=false)
     */
    private $ps5 = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="vita", type="boolean", nullable=false)
     */
    private $vita = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="psn", type="boolean", nullable=false)
     */
    private $psn = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="x360", type="boolean", nullable=false)
     */
    private $x360 = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="xb1", type="boolean", nullable=false)
     */
    private $xb1 = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="xbla", type="boolean", nullable=false)
     */
    private $xbla = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="xsx", type="boolean", nullable=false)
     */
    private $xsx = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="wii", type="boolean", nullable=false)
     */
    private $wii = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="wiiu", type="boolean", nullable=false)
     */
    private $wiiu = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="wiiware", type="boolean", nullable=false)
     */
    private $wiiware = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="switch", type="boolean", nullable=false)
     */
    private $switch = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="n3ds", type="boolean", nullable=false)
     */
    private $n3ds = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="vr", type="boolean", nullable=false)
     */
    private $vr = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="mobile", type="boolean", nullable=false)
     */
    private $mobile = false;

    /**
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return integer
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param string $name
     * @return GameRelease
     */
    public function setName($name): GameRelease
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param boolean $notable
     * @return GameRelease
     */
    public function setNotable($notable): GameRelease
    {
        $this->notable = $notable;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isNotable(): bool
    {
        return $this->notable;
    }

    /**
     * @param boolean $manuallyAdded
     * @return GameRelease
     */
    public function setManuallyAdded($manuallyAdded): GameRelease
    {
        $this->manuallyAdded = $manuallyAdded;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isManuallyAdded(): bool
    {
        return $this->manuallyAdded;
    }

    /**
     * @param boolean $pc
     * @return GameRelease
     */
    public function setPc($pc): GameRelease
    {
        $this->pc = $pc;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getPc(): bool
    {
        return $this->pc;
    }

    /**
     * @param boolean $ps3
     * @return GameRelease
     */
    public function setPs3($ps3): GameRelease
    {
        $this->ps3 = $ps3;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getPs3(): bool
    {
        return $this->ps3;
    }

    /**
     * @param boolean $ps4
     * @return GameRelease
     */
    public function setPs4($ps4): GameRelease
    {
        $this->ps4 = $ps4;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getPs4(): bool
    {
        return $this->ps4;
    }

    /**
     * @param boolean $ps5
     * @return GameRelease
     */
    public function setPs5($ps5): GameRelease
    {
        $this->ps5 = $ps5;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getPs5(): bool
    {
        return $this->ps5;
    }

    /**
     * @param boolean $vita
     * @return GameRelease
     */
    public function setVita($vita): GameRelease
    {
        $this->vita = $vita;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getVita(): bool
    {
        return $this->vita;
    }

    /**
     * @param boolean $psn
     * @return GameRelease
     */
    public function setPsn($psn): GameRelease
    {
        $this->psn = $psn;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getPsn(): bool
    {
        return $this->psn;
    }

    /**
     * @param boolean $x360
     * @return GameRelease
     */
    public function setX360($x360): GameRelease
    {
        $this->x360 = $x360;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getX360(): bool
    {
        return $this->x360;
    }

    /**
     * @param boolean $xb1
     * @return GameRelease
     */
    public function setXb1($xb1): GameRelease
    {
        $this->xb1 = $xb1;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getXb1(): bool
    {
        return $this->xb1;
    }

    /**
     * @param boolean $xsx
     * @return GameRelease
     */
    public function setXsx($xsx): GameRelease
    {
        $this->xsx = $xsx;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getXsx(): bool
    {
        return $this->xsx;
    }

    /**
     * @param boolean $xbla
     * @return GameRelease
     */
    public function setXbla($xbla): GameRelease
    {
        $this->xbla = $xbla;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getXbla(): bool
    {
        return $this->xbla;
    }

    /**
     * @param boolean $wii
     * @return GameRelease
     */
    public function setWii($wii): GameRelease
    {
        $this->wii = $wii;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getWii(): bool
    {
        return $this->wii;
    }

    /**
     * @param boolean $wiiu
     * @return GameRelease
     */
    public function setWiiu($wiiu): GameRelease
    {
        $this->wiiu = $wiiu;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getWiiu(): bool
    {
        return $this->wiiu;
    }

    /**
     * @param boolean $wiiware
     * @return GameRelease
     */
    public function setWiiware($wiiware): GameRelease
    {
        $this->wiiware = $wiiware;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getWiiware(): bool
    {
        return $this->wiiware;
    }

    /**
     * @param boolean $n3ds
     * @return GameRelease
     */
    public function setN3ds($n3ds): GameRelease
    {
        $this->n3ds = $n3ds;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getN3ds(): bool
    {
        return $this->n3ds;
    }

    /**
     * @param boolean $vr
     * @return GameRelease
     */
    public function setVr($vr): GameRelease
    {
        $this->vr = $vr;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getVr(): bool
    {
        return $this->vr;
    }

    /**
     * @param boolean $mobile
     * @return GameRelease
     */
    public function setMobile($mobile): GameRelease
    {
        $this->mobile = $mobile;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getMobile(): bool
    {
        return $this->mobile;
    }


    /**
     * @return bool
     */
    public function getSwitch(): bool
    {
        return $this->switch;
    }

    /**
     * @param bool $switch
     */
    public function setSwitch(bool $switch)
    {
        $this->switch = $switch;
    }

    public function getOthers()
    {
        $others = [];
        if ($this->wiiware) {
            $others[] = 'WiiWare';
        }
        if ($this->psn) {
            $others[] = 'PSN';
        }
        if ($this->xbla) {
            $others[] = 'XBLA';
        }
        if ($this->wii) {
            $others[] = 'Wii';
        }
        return $others;
    }

    public function getURL()
    {
        return 'https://en.wikipedia.org/wiki/' . urlencode(str_replace(' ', '_', $this->getName()));
    }

    public function getPlatforms()
    {
        $platforms = [];
        foreach (self::PLATFORMS as $platformFunction => $platform) {
            if ($this->{'get'.$platformFunction}()) {
                $platforms[] = $platform;
            }
        }
        return $platforms;
    }
}

