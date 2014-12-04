<?php

namespace Cittando\SiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EventLocale
 *
 * @ORM\Table(name="event_locale")
 * @ORM\Entity
 */
class EventLocale
{
    /**
     * @var integer
     *
     * @ORM\Column(name="event_locale_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $eventLocaleId;

    /**
     * @var string
     *
     * @ORM\Column(name="event_name", type="string", length=255, nullable=true)
     */
    private $eventName;

    /**
     * @var string
     *
     * @ORM\Column(name="event_description", type="text", nullable=true)
     */
    private $eventDescription;

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
     * @var \Locale
     *
     * @ORM\ManyToOne(targetEntity="Locale")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="locale_locale_id", referencedColumnName="locale_id")
     * })
     */
    private $localeLocale;



    /**
     * Get eventLocaleId
     *
     * @return integer 
     */
    public function getEventLocaleId()
    {
        return $this->eventLocaleId;
    }

    /**
     * Set eventName
     *
     * @param string $eventName
     * @return EventLocale
     */
    public function setEventName($eventName)
    {
        $this->eventName = $eventName;
    
        return $this;
    }

    /**
     * Get eventName
     *
     * @return string 
     */
    public function getEventName()
    {
        return $this->eventName;
    }

    /**
     * Set eventDescription
     *
     * @param string $eventDescription
     * @return EventLocale
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
     * Set modified
     *
     * @param \DateTime $modified
     * @return EventLocale
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
     * @return EventLocale
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
     * Set localeLocale
     *
     * @param \Cittando\SiteBundle\Entity\Locale $localeLocale
     * @return EventLocale
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
}