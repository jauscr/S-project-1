<?php

namespace Cittando\SiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Cittando\SiteBundle\Entity\Media;

/**
 * Event
 *
 * @ORM\Table(name="event")
 * @ORM\Entity(repositoryClass="Cittando\SiteBundle\Repository\EventRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Event
{
    /**
     * @var integer
     *
     * @ORM\Column(name="event_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="event_title", type="string", length=255, nullable=false)
     */
    protected $eventTitle;

    /**
     * @var string
     *
     * @ORM\Column(name="event_description", type="text", nullable=true)
     */
    protected $eventDescription;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_date", type="datetime", nullable=true)
     */
    protected $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_date", type="datetime", nullable=true)
     */
    protected $endDate;

    /**
     * @var string
     *
     * @ORM\Column(name="ticket_url", type="string", length=255, nullable=true)
     */
    protected $ticketUrl;

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
     * @ORM\ManyToMany(targetEntity="Venue")
     * @ORM\JoinTable(name="event_venue_rel",
     *      joinColumns={@ORM\JoinColumn(name="event_event_id", referencedColumnName="event_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="venue_venue_id", referencedColumnName="venue_id")}
     *      )
     */
    protected $venue;

    /**
     * @ORM\ManyToMany(targetEntity="Media", inversedBy="event")
     * @ORM\JoinTable(name="media_rel",
     *      joinColumns={@ORM\JoinColumn(name="event_event_id", referencedColumnName="event_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="media_media_id", referencedColumnName="media_id")}
     *      )
     */
    protected $media;


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
     * Set eventTitle
     *
     * @param string $eventTitle
     * @return Event
     */
    public function setEventTitle($eventTitle)
    {
        $this->eventTitle = $eventTitle;

        return $this;
    }

    /**
     * Get eventTitle
     *
     * @return string
     */
    public function getEventTitle()
    {
        return $this->eventTitle;
    }

    /**
     * Set eventDescription
     *
     * @param string $eventDescription
     * @return Event
     */
    public function setEventDescription($eventDescription)
    {
        $this->eventDescription = $eventDescription;

        return $this;
    }

    /**
     * Get eventDescription
     *
     * @return string
     */
    public function getEventDescription()
    {
        return $this->eventDescription;
    }

    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     * @return Event
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     * @return Event
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set ticketUrl
     *
     * @param string $ticketUrl
     * @return Event
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
     * Set modified
     *
     * @param \DateTime $modified
     * @return Event
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
     * @return Event
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
     * Set promoted
     *
     * @param \Cittando\SiteBundle\Entity\Promoted $promoted
     * @return Event
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
     * @return Event
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
        $this->venue = new \Doctrine\Common\Collections\ArrayCollection();
        $this->media = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Magic getter to access the properties
     * @param  string $property
     * @return mixed
     */
    public function __get($property)
    {
        return $this->$property;
    }

    /**
     * Magic setter to set the value of a internal property
     * @param string $property
     * @param mixed $value
     */
    public function __set($property, $value)
    {
        $this->$property = $value;
    }

    /**
     * Getter
     */
    public function getVenue()
    {
        return $this->venue;
    }

    /**
     * Setter
     */
    public function setVenue($venue)
    {
        return $this->venue = $venue;
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
    public function setMedia($media)
    {
        return $this->media = $media;
    }

    /**
     * To string magic method
     * @return string
     */
    public function __toString()
    {
        return $this->eventTitle;
    }
}