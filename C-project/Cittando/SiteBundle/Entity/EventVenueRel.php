<?php

namespace Cittando\SiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EventVenueRel
 *
 * @ORM\Table(name="event_venue_rel")
 * @ORM\Entity
 */
class EventVenueRel
{
    /**
     * @var integer
     *
     * @ORM\Column(name="event_venue_rel_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $eventVenueRelId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modified", type="datetime", nullable=false)
     */
    private $modified;

    /**
     * @var \Event
     *
     * @ORM\ManyToOne(targetEntity="Event")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="event_event_id", referencedColumnName="event_id")
     * })
     */
    private $eventEvent;

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
     * Get eventVenueRelId
     *
     * @return integer 
     */
    public function getEventVenueRelId()
    {
        return $this->eventVenueRelId;
    }

    /**
     * Set modified
     *
     * @param \DateTime $modified
     * @return EventVenueRel
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
     * Set eventEvent
     *
     * @param \Cittando\SiteBundle\Entity\Event $eventEvent
     * @return EventVenueRel
     */
    public function setEventEvent(\Cittando\SiteBundle\Entity\Event $eventEvent = null)
    {
        $this->eventEvent = $eventEvent;
    
        return $this;
    }

    /**
     * Get eventEvent
     *
     * @return \Cittando\SiteBundle\Entity\Event 
     */
    public function getEventEvent()
    {
        return $this->eventEvent;
    }

    /**
     * Set venueVenue
     *
     * @param \Cittando\SiteBundle\Entity\Venue $venueVenue
     * @return EventVenueRel
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