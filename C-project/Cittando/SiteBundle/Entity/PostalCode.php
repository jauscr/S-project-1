<?php

namespace Cittando\SiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PostalCode
 *
 * @ORM\Table(name="postal_code")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class PostalCode
{
    /**
     * @var integer
     *
     * @ORM\Column(name="postal_code_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="postal_code", type="string", length=10, nullable=false)
     */
    protected $postalCode;

    /**
     * @var float
     *
     * @ORM\Column(name="postal_code_lat", type="float", nullable=true)
     */
    protected $latitude;

    /**
     * @var float
     *
     * @ORM\Column(name="postal_code_long", type="float", nullable=true)
     */
    protected $longitude;

    /**
     * @var integer
     *
     * @ORM\Column(name="geo_accuracy", type="integer", nullable=true)
     */
    protected $geoAccuracy;

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
     * @ORM\JoinColumn(name="city_city_id", referencedColumnName="city_id")
     * })
     */
    protected $city;

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
    public function getid()
    {
        return $this->id;
    }

    /**
     * Set postalCode
     *
     * @param string $postalCode
     * @return PostalCode
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    /**
     * Get postalCode
     *
     * @return string
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * Set latitude
     *
     * @param float $latitude
     * @return PostalCode
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
     * @return PostalCode
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
     * @return PostalCode
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
     * @return PostalCode
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
     * Set city
     *
     * @param \Cittando\SiteBundle\Entity\City $city
     * @return PostalCode
     */
    public function setCity(\Cittando\SiteBundle\Entity\City $city = null)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return \Cittando\SiteBundle\Entity\City
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set country
     *
     * @param \Cittando\SiteBundle\Entity\Country $country
     * @return PostalCode
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
        return (string)$this->postalCode;
    }
}