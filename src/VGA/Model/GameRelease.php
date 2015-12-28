<?php

namespace VGA\Model;

/**
 * GameRelease
 */
class GameRelease
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var boolean
     */
    private $notable;

    /**
     * @var boolean
     */
    private $pc;

    /**
     * @var boolean
     */
    private $ps3;

    /**
     * @var boolean
     */
    private $ps4;

    /**
     * @var boolean
     */
    private $vita;

    /**
     * @var boolean
     */
    private $psn;

    /**
     * @var boolean
     */
    private $x360;

    /**
     * @var boolean
     */
    private $xb1;

    /**
     * @var boolean
     */
    private $xbla;

    /**
     * @var boolean
     */
    private $wii;

    /**
     * @var boolean
     */
    private $wiiu;

    /**
     * @var boolean
     */
    private $wiiware;

    /**
     * @var boolean
     */
    private $n3ds;

    /**
     * @var boolean
     */
    private $ouya;

    /**
     * @var boolean
     */
    private $mobile;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return GameRelease
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set notable
     *
     * @param boolean $notable
     *
     * @return GameRelease
     */
    public function setNotable($notable)
    {
        $this->notable = $notable;

        return $this;
    }

    /**
     * Get notable
     *
     * @return boolean
     */
    public function getNotable()
    {
        return $this->notable;
    }

    /**
     * Set pc
     *
     * @param boolean $pc
     *
     * @return GameRelease
     */
    public function setPc($pc)
    {
        $this->pc = $pc;

        return $this;
    }

    /**
     * Get pc
     *
     * @return boolean
     */
    public function getPc()
    {
        return $this->pc;
    }

    /**
     * Set ps3
     *
     * @param boolean $ps3
     *
     * @return GameRelease
     */
    public function setPs3($ps3)
    {
        $this->ps3 = $ps3;

        return $this;
    }

    /**
     * Get ps3
     *
     * @return boolean
     */
    public function getPs3()
    {
        return $this->ps3;
    }

    /**
     * Set ps4
     *
     * @param boolean $ps4
     *
     * @return GameRelease
     */
    public function setPs4($ps4)
    {
        $this->ps4 = $ps4;

        return $this;
    }

    /**
     * Get ps4
     *
     * @return boolean
     */
    public function getPs4()
    {
        return $this->ps4;
    }

    /**
     * Set vita
     *
     * @param boolean $vita
     *
     * @return GameRelease
     */
    public function setVita($vita)
    {
        $this->vita = $vita;

        return $this;
    }

    /**
     * Get vita
     *
     * @return boolean
     */
    public function getVita()
    {
        return $this->vita;
    }

    /**
     * Set psn
     *
     * @param boolean $psn
     *
     * @return GameRelease
     */
    public function setPsn($psn)
    {
        $this->psn = $psn;

        return $this;
    }

    /**
     * Get psn
     *
     * @return boolean
     */
    public function getPsn()
    {
        return $this->psn;
    }

    /**
     * Set x360
     *
     * @param boolean $x360
     *
     * @return GameRelease
     */
    public function setX360($x360)
    {
        $this->x360 = $x360;

        return $this;
    }

    /**
     * Get x360
     *
     * @return boolean
     */
    public function getX360()
    {
        return $this->x360;
    }

    /**
     * Set xb1
     *
     * @param boolean $xb1
     *
     * @return GameRelease
     */
    public function setXb1($xb1)
    {
        $this->xb1 = $xb1;

        return $this;
    }

    /**
     * Get xb1
     *
     * @return boolean
     */
    public function getXb1()
    {
        return $this->xb1;
    }

    /**
     * Set xbla
     *
     * @param boolean $xbla
     *
     * @return GameRelease
     */
    public function setXbla($xbla)
    {
        $this->xbla = $xbla;

        return $this;
    }

    /**
     * Get xbla
     *
     * @return boolean
     */
    public function getXbla()
    {
        return $this->xbla;
    }

    /**
     * Set wii
     *
     * @param boolean $wii
     *
     * @return GameRelease
     */
    public function setWii($wii)
    {
        $this->wii = $wii;

        return $this;
    }

    /**
     * Get wii
     *
     * @return boolean
     */
    public function getWii()
    {
        return $this->wii;
    }

    /**
     * Set wiiu
     *
     * @param boolean $wiiu
     *
     * @return GameRelease
     */
    public function setWiiu($wiiu)
    {
        $this->wiiu = $wiiu;

        return $this;
    }

    /**
     * Get wiiu
     *
     * @return boolean
     */
    public function getWiiu()
    {
        return $this->wiiu;
    }

    /**
     * Set wiiware
     *
     * @param boolean $wiiware
     *
     * @return GameRelease
     */
    public function setWiiware($wiiware)
    {
        $this->wiiware = $wiiware;

        return $this;
    }

    /**
     * Get wiiware
     *
     * @return boolean
     */
    public function getWiiware()
    {
        return $this->wiiware;
    }

    /**
     * Set n3ds
     *
     * @param boolean $n3ds
     *
     * @return GameRelease
     */
    public function setN3ds($n3ds)
    {
        $this->n3ds = $n3ds;

        return $this;
    }

    /**
     * Get n3ds
     *
     * @return boolean
     */
    public function getN3ds()
    {
        return $this->n3ds;
    }

    /**
     * Set ouya
     *
     * @param boolean $ouya
     *
     * @return GameRelease
     */
    public function setOuya($ouya)
    {
        $this->ouya = $ouya;

        return $this;
    }

    /**
     * Get ouya
     *
     * @return boolean
     */
    public function getOuya()
    {
        return $this->ouya;
    }

    /**
     * Set mobile
     *
     * @param boolean $mobile
     *
     * @return GameRelease
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;

        return $this;
    }

    /**
     * Get mobile
     *
     * @return boolean
     */
    public function getMobile()
    {
        return $this->mobile;
    }
}

