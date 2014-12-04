<?php

namespace Cittando\SiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ArtistEventRel
 *
 * @ORM\Table(name="artist_event_rel")
 * @ORM\Entity
 */
class ArtistEventRel
{
    /**
     * @var integer
     *
     * @ORM\Column(name="artist_event_rel_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $artistEventRelId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modified", type="datetime", nullable=false)
     */
    private $modified;

    /**
     * @var \Artist
     *
     * @ORM\ManyToOne(targetEntity="Artist")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="artist_artist_id", referencedColumnName="artist_id")
     * })
     */
    private $artistArtist;

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
     * Get artistEventRelId
     *
     * @return integer 
     */
    public function getArtistEventRelId()
    {
        return $this->artistEventRelId;
    }

    /**
     * Set modified
     *
     * @param \DateTime $modified
     * @return ArtistEventRel
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
     * Set artistArtist
     *
     * @param \Cittando\SiteBundle\Entity\Artist $artistArtist
     * @return ArtistEventRel
     */
    public function setArtistArtist(\Cittando\SiteBundle\Entity\Artist $artistArtist = null)
    {
        $this->artistArtist = $artistArtist;
    
        return $this;
    }

    /**
     * Get artistArtist
     *
     * @return \Cittando\SiteBundle\Entity\Artist 
     */
    public function getArtistArtist()
    {
        return $this->artistArtist;
    }

    /**
     * Set eventEvent
     *
     * @param \Cittando\SiteBundle\Entity\Event $eventEvent
     * @return ArtistEventRel
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
}