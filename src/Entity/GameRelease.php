<?php

namespace App\Entity;

class GameRelease
{
    /** @var integer */
    private $id;

    /** @var string */
    private $name;

    /** @var boolean */
    private $notable = false;

    /** @var boolean */
    private $pc = false;

    /** @var boolean */
    private $ps3 = false;

    /** @var boolean */
    private $ps4 = false;

    /** @var boolean */
    private $vita = false;

    /** @var boolean */
    private $psn = false;

    /** @var boolean */
    private $x360 = false;

    /** @var boolean */
    private $xb1 = false;

    /** @var boolean */
    private $xbla = false;

    /** @var boolean */
    private $wii = false;

    /** @var boolean */
    private $wiiu = false;

    /** @var boolean */
    private $wiiware = false;

    /** @var boolean */
    private $switch = false;

    /** @var boolean */
    private $n3ds = false;

    /** @var boolean */
    private $vr = false;

    /** @var boolean */
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
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $name
     * @return GameRelease
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param boolean $notable
     * @return GameRelease
     */
    public function setNotable($notable)
    {
        $this->notable = $notable;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getNotable()
    {
        return $this->notable;
    }

    /**
     * @param boolean $pc
     * @return GameRelease
     */
    public function setPc($pc)
    {
        $this->pc = $pc;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getPc()
    {
        return $this->pc;
    }

    /**
     * @param boolean $ps3
     * @return GameRelease
     */
    public function setPs3($ps3)
    {
        $this->ps3 = $ps3;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getPs3()
    {
        return $this->ps3;
    }

    /**
     * @param boolean $ps4
     * @return GameRelease
     */
    public function setPs4($ps4)
    {
        $this->ps4 = $ps4;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getPs4()
    {
        return $this->ps4;
    }

    /**
     * @param boolean $vita
     * @return GameRelease
     */
    public function setVita($vita)
    {
        $this->vita = $vita;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getVita()
    {
        return $this->vita;
    }

    /**
     * @param boolean $psn
     * @return GameRelease
     */
    public function setPsn($psn)
    {
        $this->psn = $psn;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getPsn()
    {
        return $this->psn;
    }

    /**
     * @param boolean $x360
     * @return GameRelease
     */
    public function setX360($x360)
    {
        $this->x360 = $x360;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getX360()
    {
        return $this->x360;
    }

    /**
     * @param boolean $xb1
     * @return GameRelease
     */
    public function setXb1($xb1)
    {
        $this->xb1 = $xb1;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getXb1()
    {
        return $this->xb1;
    }

    /**
     * @param boolean $xbla
     * @return GameRelease
     */
    public function setXbla($xbla)
    {
        $this->xbla = $xbla;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getXbla()
    {
        return $this->xbla;
    }

    /**
     * @param boolean $wii
     * @return GameRelease
     */
    public function setWii($wii)
    {
        $this->wii = $wii;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getWii()
    {
        return $this->wii;
    }

    /**
     * @param boolean $wiiu
     * @return GameRelease
     */
    public function setWiiu($wiiu)
    {
        $this->wiiu = $wiiu;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getWiiu()
    {
        return $this->wiiu;
    }

    /**
     * @param boolean $wiiware
     * @return GameRelease
     */
    public function setWiiware($wiiware)
    {
        $this->wiiware = $wiiware;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getWiiware()
    {
        return $this->wiiware;
    }

    /**
     * @param boolean $n3ds
     * @return GameRelease
     */
    public function setN3ds($n3ds)
    {
        $this->n3ds = $n3ds;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getN3ds()
    {
        return $this->n3ds;
    }

    /**
     * @param boolean $vr
     * @return GameRelease
     */
    public function setVr($vr)
    {
        $this->vr = $vr;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getVr()
    {
        return $this->vr;
    }

    /**
     * @param boolean $mobile
     * @return GameRelease
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getMobile()
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
        $allPlatforms = [
            'pc' => 'PC',
            'vr' => 'VR',
            'ps3' => 'PS3',
            'ps4' => 'PS4',
            'vita' => 'Vita',
            'psn' => 'PSN',
            'x360' => '360',
            'xb1' => 'XB1',
            'xbla' => 'XBLA',
            'wii' => 'Wii',
            'wiiu' => 'Wii U',
            'switch' => 'Switch',
            'wiiware' => 'WiiWare',
            'n3ds' => '3DS',
            'mobile' => 'Mobile'
        ];

        $platforms = [];
        foreach ($allPlatforms as $platformFunction => $platform) {
            if ($this->{'get'.$platformFunction}()) {
                $platforms[] = $platform;
            }
        }
        return $platforms;
    }
}

