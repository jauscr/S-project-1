<?php

namespace Cittando\SiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * VenueLocale
 *
 * @ORM\Table(name="venue_locale")
 * @ORM\Entity
 */
class VenueLocale
{
    /**
     * @var integer
     *
     * @ORM\Column(name="venue_locale_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $venueLocaleId;

    /**
     * @var string
     *
     * @ORM\Column(name="venue_name", type="string", length=45, nullable=true)
     */
    private $venueName;

    /**
     * @var string
     *
     * @ORM\Column(name="venue_description", type="text", nullable=true)
     */
    private $venueDescription;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modified", type="datetime", nullable=false)
     */
    private $modified;

    /**
     * @var \Locale
     *
     * @ORM\ManyToOne(targetEntity="Locale")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="locale_locale_id", referencedColumnName="locale_id")
     * })
     */
    private $localeLocale;

    /**
     * @var \Venue
     *
     * @ORM\ManyToOne(targetEntity="Venue")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="venue_venue_id", referencedColumnName="venue_id")
     * })
     */
    private $venueVenue;



    /**
     * Get venueLocaleId
     *
     * @return integer 
     */
    public function getVenueLocaleId()
    {
        return $this->venueLocaleId;
    }

    /**
     * Set venueName
     *
     * @param string $venueName
     * @return VenueLocale
     */
    public function setVenueName($venueName)
    {
        $this->venueName = $venueName;
    
        return $this;
    }

    /**
     * Get venueName
     *
     * @return string 
     */
    public function getVenueName()
    {
        return $this->venueName;
    }

    /**
     * Set venueDescription
     *
     * @param string $venueDescription
     * @return VenueLocale
     */
    public function setVenueDescription($venueDescription)
    {
        $this->venueDescription = $venueDescription;
    
        return $this;
    }

    /**
     * Get venueDescription
     *
     * @return string 
     */
    public function getVenueDescription()
    {
        return $this->venueDescription;
    }

    /**
     * Set modified
     *
     * @param \DateTime $modified
     * @return VenueLocale
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
     * Set localeLocale
     *
     * @param \Cittando\SiteBundle\Entity\Locale $localeLocale
     * @return VenueLocale
     */
    public function setLocaleLocale(\Cittando\SiteBundle\Entity\Locale $localeLocale = null)
    {
        $this->localeLocale = $localeLocale;
    
        return $this;
    }

    /**
     * Get localeLocale
     *
     * @return \Cittando\SiteBundle\Entity\Locale 
     */
    public function getLocaleLocale()
    {
        return $this->localeLocale;
    }

    /**
     * Set venueVenue
     *
     * @param \Cittando\SiteBundle\Entity\Venue $venueVenue
     * @return VenueLocale
     */
    public function setVenueVenue(\Cittando\SiteBundle\Entity\Venue $venueVenue = null)
    {
        $this->venueVenue = $venueVenue;
    
        return $this;
    }

    /**
     * Get venueVenue
     *
     * @return \Cittando\SiteBundle\Entity\Venue 
     */
    public function getVenueVenue()
    {
        return $this->venueVenue;
    }
}