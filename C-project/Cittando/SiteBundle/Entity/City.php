<?php

namespace Cittando\SiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * City
 *
 * @ORM\Table(name="city")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class City
{
    /**
     * @var integer
     *
     * @ORM\Column(name="city_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="city_name", type="string", length=200, nullable=false)
     */
    protected $cityName;

    /**
     * @var string
     *
     * @ORM\Column(name="city_name_alt", type="string", length=500, nullable=true)
     */
    protected $cityNameAlt;

    /**
     * @var float
     *
     * @ORM\Column(name="city_lat", type="float", nullable=true)
     */
    protected $cityLat;

    /**
     * @var float
     *
     * @ORM\Column(name="city_long", type="float", nullable=true)
     */
    protected $cityLong;

    /**
     * @var integer
     *
     * @ORM\Column(name="geo_accuracy", type="integer", nullable=true)
     */
    protected $geoAccuracy;

    /**
     * @var integer
     *
     * @ORM\Column(name="city_is_metroarea", type="boolean", nullable=true)
     */
    protected $cityIsMetroarea;

    /**
     * @var string
     *
     * @ORM\Column(name="city_timezone", type="string", length=45, nullable=true)
     */
    protected $cityTimezone;

    /**
     * @var integer
     *
     * @ORM\Column(name="city_population", type="integer", nullable=true)
     */
    protected $cityPopulation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modified", type="datetime", nullable=false)
     */
    protected $modified;

    /**
     * @var \City
     *
     * @ORM\ManyToOne(targetEntity="City")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="city_metroarea_id", referencedColumnName="city_id")
     * })
     */
    protected $cityMetroarea;

    /**
     * @var \Country
     *
     * @ORM\ManyToOne(targetEntity="Country")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="country_country_id", referencedColumnName="country_id")
     * })
     */
    protected $country;

    /**
     * @var \Province
     *
     * @ORM\ManyToOne(targetEntity="Province")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="province_province_id", referencedColumnName="province_id")
     * })
     */
    protected $province;

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
     * Set cityName
     *
     * @param string $cityName
     * @return City
     */
    public function setCityName($cityName)
    {
        $this->cityName = $cityName;

        return $this;
    }

    /**
     * Get cityName
     *
     * @return string
     */
    public function getCityName()
    {
        return $this->cityName;
    }

    /**
     * Set cityNameAlt
     *
     * @param string $cityNameAlt
     * @return City
     */
    public function setCityNameAlt($cityNameAlt)
    {
        $this->cityNameAlt = $cityNameAlt;

        return $this;
    }

    /**
     * Get cityNameAlt
     *
     * @return string
     */
    public function getCityNameAlt()
    {
        return $this->cityNameAlt;
    }

    /**
     * Set cityLat
     *
     * @param float $cityLat
     * @return City
     */
    public function setCityLat($cityLat)
    {
        $this->cityLat = $cityLat;

        return $this;
    }

    /**
     * Get cityLat
     *
     * @return float
     */
    public function getCityLat()
    {
        return $this->cityLat;
    }

    /**
     * Set cityLong
     *
     * @param float $cityLong
     * @return City
     */
    public function setCityLong($cityLong)
    {
        $this->cityLong = $cityLong;

        return $this;
    }

    /**
     * Get cityLong
     *
     * @return float
     */
    public function getCityLong()
    {
        return $this->cityLong;
    }

    /**
     * Set geoAccuracy
     *
     * @param integer $geoAccuracy
     * @return City
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
     * Set cityIsMetroarea
     *
     * @param integer $cityIsMetroarea
     * @return City
     */
    public function setCityIsMetroarea($cityIsMetroarea)
    {
        $this->cityIsMetroarea = $cityIsMetroarea;

        return $this;
    }

    /**
     * Get cityIsMetroarea
     *
     * @return integer
     */
    public function getCityIsMetroarea()
    {
        return $this->cityIsMetroarea;
    }

    /**
     * Set cityTimezone
     *
     * @param string $cityTimezone
     * @return City
     */
    public function setCityTimezone($cityTimezone)
    {
        $this->cityTimezone = $cityTimezone;

        return $this;
    }

    /**
     * Get cityTimezone
     *
     * @return string
     */
    public function getCityTimezone()
    {
        return $this->cityTimezone;
    }

    /**
     * Set cityPopulation
     *
     * @param integer $cityPopulation
     * @return City
     */
    public function setCityPopulation($cityPopulation)
    {
        $this->cityPopulation = $cityPopulation;

        return $this;
    }

    /**
     * Get cityPopulation
     *
     * @return integer
     */
    public function getCityPopulation()
    {
        return $this->cityPopulation;
    }

    /**
     * Set modified
     *
     * @param \DateTime $modified
     * @return City
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
     * Set cityMetroarea
     *
     * @param \Cittando\SiteBundle\Entity\City $cityMetroarea
     * @return City
     */
    public function setCityMetroarea(\Cittando\SiteBundle\Entity\City $cityMetroarea = null)
    {
        $this->cityMetroarea = $cityMetroarea;

        return $this;
    }

    /**
     * Get cityMetroarea
     *
     * @return \Cittando\SiteBundle\Entity\City
     */
    public function getCityMetroarea()
    {
        return $this->cityMetroarea;
    }

    /**
     * Set country
     *
     * @param \Cittando\SiteBundle\Entity\Country $country
     * @return City
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
     * Set province
     *
     * @param \Cittando\SiteBundle\Entity\Province $province
     * @return City
     */
    public function setProvince(\Cittando\SiteBundle\Entity\Province $province = null)
    {
        $this->province = $province;

        return $this;
    }

    /**
     * Get province
     *
     * @return \Cittando\SiteBundle\Entity\Province
     */
    public function getProvince()
    {
        return $this->province;
    }

    /**
     * To string magic method
     * @return string
     */
    public function __toString()
    {
        return $this->cityName;
    }
}