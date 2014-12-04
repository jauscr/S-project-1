<?php

namespace Cittando\SiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Venue
 *
 * @ORM\Table(name="venue")
 * @ORM\Entity(repositoryClass="Cittando\SiteBundle\Repository\VenueRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Venue
{
    /**
     * @var integer
     *
     * @ORM\Column(name="venue_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="venue_name", type="string", length=255, nullable=false)
     */
    protected $venueName;

    /**
     * @var string
     *
     * @ORM\Column(name="venue_description", type="text", nullable=true)
     */
    protected $venueDescription;

    /**
     * @var string
     *
     * @ORM\Column(name="address_1", type="string", length=255, nullable=true)
     */
    protected $address1;

    /**
     * @var string
     *
     * @ORM\Column(name="address_2", type="string", length=255, nullable=true)
     */
    protected $address2;

    /**
     * @var string
     *
     * @ORM\Column(name="tel", type="string", length=45, nullable=true)
     */
    protected $tel;

    /**
     * @var string
     *
     * @ORM\Column(name="website_url", type="string", length=255, nullable=true)
     */
    protected $websiteUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="ticket_url", type="string", length=255, nullable=true)
     */
    protected $ticketUrl;

    /**
     * @var float
     *
     * @ORM\Column(name="venue_long", type="float", nullable=true)
     */
    protected $venueLong;

    /**
     * @var float
     *
     * @ORM\Column(name="venue_lat", type="float", nullable=true)
     */
    protected $venueLat;

    /**
     * @var integer
     *
     * @ORM\Column(name="venue_geo_accuracy", type="integer", nullable=true)
     */
    protected $venueGeoAccuracy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modified", type="datetime", nullable=false)
     */
    protected $modified;

    /**
     * @var \Category
     *
     * @ORM\ManyToOne(targetEntity="Category")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="category_category_id", referencedColumnName="category_id")
     * })
     */
    protected $category;

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
     * @var \PostalCode
     *
     * @ORM\ManyToOne(targetEntity="PostalCode")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="postal_code_postal_code_id", referencedColumnName="postal_code_id")
     * })
     */
    protected $postalCode;

    /**
     * @var \Promoted
     *
     * @ORM\ManyToOne(targetEntity="Promoted")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="promoted_promoted_id", referencedColumnName="promoted_id")
     * })
     */
    protected $promoted;

    /**
     * @var \Status
     *
     * @ORM\ManyToOne(targetEntity="Status")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="status_status_id", referencedColumnName="status_id")
     * })
     */
    protected $status;

    /**
     * @ORM\ManyToMany(targetEntity="Media", inversedBy="venue")
     * @ORM\JoinTable(name="media_rel",
     *      joinColumns={@ORM\JoinColumn(name="venue_venue_id", referencedColumnName="venue_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="media_media_id", referencedColumnName="media_id")}
     *      )
     */
    protected $media;

    /**
     * @ORM\ManyToMany(targetEntity="Event")
     * @ORM\JoinTable(name="event_venue_rel",
     *      joinColumns={@ORM\JoinColumn(name="venue_venue_id", referencedColumnName="venue_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="event_event_id", referencedColumnName="event_id")}
     *      )
     */
    protected $event;

    /**
     * @ORM\ManyToMany(targetEntity="Film")
     * @ORM\JoinTable(name="film_venue_rel",
     *      joinColumns={@ORM\JoinColumn(name="venue_venue_id", referencedColumnName="venue_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="film_film_id", referencedColumnName="film_id")}
     *      )
     */
    protected $film;


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
     * Set venueName
     *
     * @param string $venueName
     * @return Venue
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
     * @return Venue
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
     * Set address1
     *
     * @param string $address1
     * @return Venue
     */
    public function setAddress1($address1)
    {
        $this->address1 = $address1;

        return $this;
    }

    /**
     * Get address1
     *
     * @return string
     */
    public function getAddress1()
    {
        return $this->address1;
    }

    /**
     * Set address2
     *
     * @param string $address2
     * @return Venue
     */
    public function setAddress2($address2)
    {
        $this->address2 = $address2;

        return $this;
    }

    /**
     * Get address2
     *
     * @return string
     */
    public function getAddress2()
    {
        return $this->address2;
    }

    /**
     * Set tel
     *
     * @param string $tel
     * @return Venue
     */
    public function setTel($tel)
    {
        $this->tel = $tel;

        return $this;
    }

    /**
     * Get tel
     *
     * @return string
     */
    public function getTel()
    {
        return $this->tel;
    }

    /**
     * Set websiteUrl
     *
     * @param string $websiteUrl
     * @return Venue
     */
    public function setWebsiteUrl($websiteUrl)
    {
        $this->websiteUrl = $websiteUrl;

        return $this;
    }

    /**
     * Get websiteUrl
     *
     * @return string
     */
    public function getWebsiteUrl()
    {
        return $this->websiteUrl;
    }

    /**
     * Set ticketUrl
     *
     * @param string $ticketUrl
     * @return Venue
     */
    public function setTicketUrl($ticketUrl)
    {
        $this->ticketUrl = $ticketUrl;

        return $this;
    }

    /**
     * Get ticketUrl
     *
     * @return string
     */
    public function getTicketUrl()
    {
        return $this->ticketUrl;
    }

    /**
     * Set venueLong
     *
     * @param float $venueLong
     * @return Venue
     */
    public function setVenueLong($venueLong)
    {
        $this->venueLong = $venueLong;

        return $this;
    }

    /**
     * Get venueLong
     *
     * @return float
     */
    public function getVenueLong()
    {
        return $this->venueLong;
    }

    /**
     * Set venueLat
     *
     * @param float $venueLat
     * @return Venue
     */
    public function setVenueLat($venueLat)
    {
        $this->venueLat = $venueLat;

        return $this;
    }

    /**
     * Get venueLat
     *
     * @return float
     */
    public function getVenueLat()
    {
        return $this->venueLat;
    }

    /**
     * Set venueGeoAccuracy
     *
     * @param integer $venueGeoAccuracy
     * @return Venue
     */
    public function setVenueGeoAccuracy($venueGeoAccuracy)
    {
        $this->venueGeoAccuracy = $venueGeoAccuracy;

        return $this;
    }

    /**
     * Get venueGeoAccuracy
     *
     * @return integer
     */
    public function getVenueGeoAccuracy()
    {
        return $this->venueGeoAccuracy;
    }

    /**
     * Set modified
     *
     * @param \DateTime $modified
     * @return Venue
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
     * Set category
     *
     * @param \Cittando\SiteBundle\Entity\Category $category
     * @return Venue
     */
    public function setCategory(\Cittando\SiteBundle\Entity\Category $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \Cittando\SiteBundle\Entity\Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set city
     *
     * @param \Cittando\SiteBundle\Entity\City $city
     * @return Venue
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
     * @return Venue
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
     * Set postalCode
     *
     * @param \Cittando\SiteBundle\Entity\PostalCode $postalCode
     * @return Venue
     */
    public function setPostalCode(\Cittando\SiteBundle\Entity\PostalCode $postalCode = null)
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    /**
     * Get postalCode
     *
     * @return \Cittando\SiteBundle\Entity\PostalCode
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * Set promoted
     *
     * @param \Cittando\SiteBundle\Entity\Promoted $promoted
     * @return Venue
     */
    public function setPromoted(\Cittando\SiteBundle\Entity\Promoted $promoted = null)
    {
        $this->promoted = $promoted;

        return $this;
    }

    /**
     * Get promoted
     *
     * @return \Cittando\SiteBundle\Entity\Promoted
     */
    public function getPromoted()
    {
        return $this->promoted;
    }

    /**
     * Set status
     *
     * @param \Cittando\SiteBundle\Entity\Status $status
     * @return Venue
     */
    public function setStatus(\Cittando\SiteBundle\Entity\Status $status = null)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return \Cittando\SiteBundle\Entity\Status
     */
    public function getStatus()
    {
        return $this->status;
    }

    public function __construct()
    {
        $this->event = new \Doctrine\Common\Collections\ArrayCollection();
        $this->media = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Getter
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * Setter
     */
    public function setMedia(\Cittando\SiteBundle\Entity\Media $media)
    {
        return $this->media = $media;
    }

    /**
     * To string magic method
     * @return string
     */
    public function __toString()
    {
        $name = empty($this->venueName) ? $this->venueDescription : $this->venueName;
        $name = empty($name) ? $this->websiteUrl : $name;

        return (string)$name;
    }
}