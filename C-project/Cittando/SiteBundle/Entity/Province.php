<?php

namespace Cittando\SiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Province
 *
 * @ORM\Table(name="province")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Province
{
    /**
     * @var integer
     *
     * @ORM\Column(name="province_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="province_name", type="string", length=45, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="province_code", type="string", length=3, nullable=true)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="province_code_ISO", type="string", length=45, nullable=true)
     */
    private $codeIso;

    /**
     * @var string
     *
     * @ORM\Column(name="province_level", type="string", length=45, nullable=true)
     */
    private $level;

    /**
     * @var float
     *
     * @ORM\Column(name="province_lat", type="float", nullable=true)
     */
    private $latitude;

    /**
     * @var float
     *
     * @ORM\Column(name="province_long", type="float", nullable=true)
     */
    private $longitude;

    /**
     * @var integer
     *
     * @ORM\Column(name="geo_accuracy", type="integer", nullable=true)
     */
    private $geoAccuracy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modified", type="datetime", nullable=false)
     */
    private $modified;

    /**
     * @var \Country
     *
     * @ORM\ManyToOne(targetEntity="Country")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="country_country_id", referencedColumnName="country_id")
     * })
     */
    private $country;

    /**
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->modified = new \DateTime('now');
    }


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
     * @return Province
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
     * Set code
     *
     * @param string $code
     * @return Province
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set codeIso
     *
     * @param string $codeIso
     * @return Province
     */
    public function setCodeIso($codeIso)
    {
        $this->codeIso = $codeIso;

        return $this;
    }

    /**
     * Get codeIso
     *
     * @return string
     */
    public function getCodeIso()
    {
        return $this->codeIso;
    }

    /**
     * Set level
     *
     * @param string $level
     * @return Province
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level
     *
     * @return string
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set latitude
     *
     * @param float $latitude
     * @return Province
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude
     *
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude
     *
     * @param float $longitude
     * @return Province
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude
     *
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set geoAccuracy
     *
     * @param integer $geoAccuracy
     * @return Province
     */
    public function setGeoAccuracy($geoAccuracy)
    {
        $this->geoAccuracy = $geoAccuracy;

        return $this;
    }

    /**
     * Get geoAccuracy
     *
     * @return integer
     */
    public function getGeoAccuracy()
    {
        return $this->geoAccuracy;
    }

    /**
     * Set modified
     *
     * @param \DateTime $modified
     * @return Province
     */
    public function setModified($modified)
    {
        $this->modified = $modified;

        return $this;
    }

    /**
     * Get modified
     *
     * @return \DateTime
     */
    public function getModified()
    {
        return $this->modified;
    }

    /**
     * Set country
     *
     * @param \Cittando\SiteBundle\Entity\Country $country
     * @return Province
     */
    public function setCountry(\Cittando\SiteBundle\Entity\Country $country = null)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return \Cittando\SiteBundle\Entity\Country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * To string magic method
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }
}