<?php

namespace Cittando\SiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * IpCountryRegionCity
 *
 * @ORM\Table(name="ip_country_region_city")
 * @ORM\Entity(repositoryClass="Cittando\SiteBundle\Repository\IpCountryRegionCityRepository")
 */
class IpCountryRegionCity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @var integer
     *
     * @ORM\Column(name="ip_from", type="integer", nullable=false)
     */
    private $ipFrom;
    /**
     * @var integer
     *
     * @ORM\Column(name="ip_to", type="integer", nullable=false)
     */
    private $ipTo;
    /**
     * @var string
     *
     * @ORM\Column(name="country_short", type="string", length=2, nullable=false)
     */
    private $countryShort;
    /**
     * @var string
     *
     * @ORM\Column(name="country_long", type="string", length=45, nullable=false)
     */
    private $countryLong;
    /**
     * @var string
     *
     * @ORM\Column(name="ip_region", type="string", length=128, nullable=false)
     */
    private $ipRegion;
    /**
     * @var string
     *
     * @ORM\Column(name="ip_city", type="string", length=128, nullable=false)
     */
    private $ipCity;

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
     * Get ipFrom
     *
     * @return string
     */
    public function getIpFrom()
    {
        return $this->ipFrom;
    }

    /**
     * Set ipFrom
     *
     * @param string $ipFrom
     * @return IpCountryRegionCity
     */
    public function setIpFrom($ipFrom)
    {
        $this->ipFrom = $ipFrom;

        return $this;
    }

    /**
     * Get ipTo
     *
     * @return string
     */
    public function getIpTo()
    {
        return $this->ipTo;
    }

    /**
     * Set ipTo
     *
     * @param string $ipTo
     * @return IpCountryRegionCity
     */
    public function setIpTo($ipTo)
    {
        $this->ipTo = $ipTo;

        return $this;
    }

    /**
     * Get countryShort
     *
     * @return string
     */
    public function getcountryShort()
    {
        return $this->countryShort;
    }

    /**
     * Set countryShort
     *
     * @param string $countryShort
     * @return IpCountryRegionCity
     */
    public function setCountryShort($countryShort)
    {
        $this->countryShort = $countryShort;

        return $this;
    }

    /**
     * Get countryLong
     *
     * @return string
     */
    public function getcountryLong()
    {
        return $this->countryLong;
    }

    /**
     * Set countryLong
     *
     * @param string $countryLong
     * @return IpCountryRegionCity
     */
    public function setCountryLong($countryLong)
    {
        $this->countryLong = $countryLong;

        return $this;
    }

    /**
     * Get ipRegion
     *
     * @return string
     */
    public function getipRegion()
    {
        return $this->ipRegion;
    }

    /**
     * Set ipRegion
     *
     * @param string $ipRegion
     * @return IpCountryRegionCity
     */
    public function setIpRegion($ipRegion)
    {
        $this->ipRegion = $ipRegion;

        return $this;
    }

    /**
     * Get ipCity
     *
     * @return string
     */
    public function getipCity()
    {
        return $this->ipCity;
    }

    /**
     * Set ipCity
     *
     * @param string $ipCity
     * @return IpCountryRegionCity
     */
    public function setIpCity($ipCity)
    {
        $this->ipCity = $ipCity;

        return $this;
    }

}
