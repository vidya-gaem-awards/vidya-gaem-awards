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
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @ORM\Column(name="name", type="string", length=100)
     */
    private string $name;

    /**
     * @ORM\Column(name="notable", type="boolean", nullable=false)
     */
    private bool $notable = false;

    /**
     * @ORM\Column(name="manually_added", type="boolean", nullable=false)
     */
    private bool $manuallyAdded = false;

    /**
     * @ORM\Column(name="pc", type="boolean", nullable=false)
     */
    private bool $pc = false;

    /**
     * @ORM\Column(name="ps3", type="boolean", nullable=false)
     */
    private bool $ps3 = false;

    /**
     * @ORM\Column(name="ps4", type="boolean", nullable=false)
     */
    private bool $ps4 = false;

    /**
     * @ORM\Column(name="ps5", type="boolean", nullable=false)
     */
    private bool $ps5 = false;

    /**
     * @ORM\Column(name="vita", type="boolean", nullable=false)
     */
    private bool $vita = false;

    /**
     * @ORM\Column(name="psn", type="boolean", nullable=false)
     */
    private bool $psn = false;

    /**
     * @ORM\Column(name="x360", type="boolean", nullable=false)
     */
    private bool $x360 = false;

    /**
     * @ORM\Column(name="xb1", type="boolean", nullable=false)
     */
    private bool $xb1 = false;

    /**
     * @ORM\Column(name="xbla", type="boolean", nullable=false)
     */
    private bool $xbla = false;

    /**
     * @ORM\Column(name="xsx", type="boolean", nullable=false)
     */
    private bool $xsx = false;

    /**
     * @ORM\Column(name="wii", type="boolean", nullable=false)
     */
    private bool $wii = false;

    /**
     * @ORM\Column(name="wiiu", type="boolean", nullable=false)
     */
    private bool $wiiu = false;

    /**
     * @ORM\Column(name="wiiware", type="boolean", nullable=false)
     */
    private bool $wiiware = false;

    /**
     * @ORM\Column(name="switch", type="boolean", nullable=false)
     */
    private bool $switch = false;

    /**
     * @ORM\Column(name="n3ds", type="boolean", nullable=false)
     */
    private bool $n3ds = false;

    /**
     * @ORM\Column(name="vr", type="boolean", nullable=false)
     */
    private bool $vr = false;

    /**
     * @ORM\Column(name="mobile", type="boolean", nullable=false)
     */
    private bool $mobile = false;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setName(string $name): GameRelease
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setNotable(bool $notable): GameRelease
    {
        $this->notable = $notable;

        return $this;
    }

    public function isNotable(): bool
    {
        return $this->notable;
    }

    public function setManuallyAdded(bool $manuallyAdded): GameRelease
    {
        $this->manuallyAdded = $manuallyAdded;

        return $this;
    }

    public function isManuallyAdded(): bool
    {
        return $this->manuallyAdded;
    }

    public function setPc(bool $pc): GameRelease
    {
        $this->pc = $pc;

        return $this;
    }

    public function getPc(): bool
    {
        return $this->pc;
    }

    public function setPs3(bool $ps3): GameRelease
    {
        $this->ps3 = $ps3;

        return $this;
    }

    public function getPs3(): bool
    {
        return $this->ps3;
    }

    public function setPs4(bool $ps4): GameRelease
    {
        $this->ps4 = $ps4;

        return $this;
    }

    public function getPs4(): bool
    {
        return $this->ps4;
    }

    public function setPs5(bool $ps5): GameRelease
    {
        $this->ps5 = $ps5;

        return $this;
    }

    public function getPs5(): bool
    {
        return $this->ps5;
    }

    public function setVita(bool $vita): GameRelease
    {
        $this->vita = $vita;

        return $this;
    }

    public function getVita(): bool
    {
        return $this->vita;
    }

    public function setPsn(bool $psn): GameRelease
    {
        $this->psn = $psn;

        return $this;
    }

    public function getPsn(): bool
    {
        return $this->psn;
    }

    public function setX360(bool $x360): GameRelease
    {
        $this->x360 = $x360;

        return $this;
    }

    public function getX360(): bool
    {
        return $this->x360;
    }

    public function setXb1(bool $xb1): GameRelease
    {
        $this->xb1 = $xb1;

        return $this;
    }

    public function getXb1(): bool
    {
        return $this->xb1;
    }

    public function setXsx(bool $xsx): GameRelease
    {
        $this->xsx = $xsx;

        return $this;
    }

    public function getXsx(): bool
    {
        return $this->xsx;
    }

    public function setXbla(bool $xbla): GameRelease
    {
        $this->xbla = $xbla;

        return $this;
    }

    public function getXbla(): bool
    {
        return $this->xbla;
    }

    public function setWii(bool $wii): GameRelease
    {
        $this->wii = $wii;

        return $this;
    }

    public function getWii(): bool
    {
        return $this->wii;
    }

    public function setWiiu(bool $wiiu): GameRelease
    {
        $this->wiiu = $wiiu;

        return $this;
    }

    public function getWiiu(): bool
    {
        return $this->wiiu;
    }

    public function setWiiware(bool $wiiware): GameRelease
    {
        $this->wiiware = $wiiware;

        return $this;
    }

    public function getWiiware(): bool
    {
        return $this->wiiware;
    }

    public function setN3ds(bool $n3ds): GameRelease
    {
        $this->n3ds = $n3ds;

        return $this;
    }

    public function getN3ds(): bool
    {
        return $this->n3ds;
    }

    public function setVr(bool $vr): GameRelease
    {
        $this->vr = $vr;

        return $this;
    }

    public function getVr(): bool
    {
        return $this->vr;
    }

    public function setMobile(bool $mobile): GameRelease
    {
        $this->mobile = $mobile;

        return $this;
    }

    public function getMobile(): bool
    {
        return $this->mobile;
    }


    public function getSwitch(): bool
    {
        return $this->switch;
    }

    public function setSwitch(bool $switch): void
    {
        $this->switch = $switch;
    }

    public function getOthers(): array
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

    public function getURL(): string
    {
        return 'https://en.wikipedia.org/wiki/' . urlencode(str_replace(' ', '_', $this->getName()));
    }

    public function getPlatforms(): array
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

