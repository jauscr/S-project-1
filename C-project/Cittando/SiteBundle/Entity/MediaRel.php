<?php

namespace Cittando\SiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MediaRel
 *
 * @ORM\Table(name="media_rel")
 * @ORM\Entity
 */
class MediaRel
{
    /**
     * @var integer
     *
     * @ORM\Column(name="media_rel_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $mediaRelId;

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
     * @var \Film
     *
     * @ORM\ManyToOne(targetEntity="Film")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="film_film_id", referencedColumnName="film_id")
     * })
     */
    private $filmFilm;

    /**
     * @var \Media
     *
     * @ORM\ManyToOne(targetEntity="Media")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="media_media_id", referencedColumnName="media_id")
     * })
     */
    private $mediaMedia;

    /**
     * @var \UserDetail
     *
     * @ORM\ManyToOne(targetEntity="UserDetail")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_detail_user_detail_id", referencedColumnName="user_detail_id")
     * })
     */
    private $userDetailUserDetail;

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
     * Get mediaRelId
     *
     * @return integer 
     */
    public function getMediaRelId()
    {
        return $this->mediaRelId;
    }

    /**
     * Set modified
     *
     * @param \DateTime $modified
     * @return MediaRel
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
     * @return MediaRel
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
     * @return MediaRel
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
     * Set filmFilm
     *
     * @param \Cittando\SiteBundle\Entity\Film $filmFilm
     * @return MediaRel
     */
    public function setFilmFilm(\Cittando\SiteBundle\Entity\Film $filmFilm = null)
    {
        $this->filmFilm = $filmFilm;
    
        return $this;
    }

    /**
     * Get filmFilm
     *
     * @return \Cittando\SiteBundle\Entity\Film 
     */
    public function getFilmFilm()
    {
        return $this->filmFilm;
    }

    /**
     * Set mediaMedia
     *
     * @param \Cittando\SiteBundle\Entity\Media $mediaMedia
     * @return MediaRel
     */
    public function setMediaMedia(\Cittando\SiteBundle\Entity\Media $mediaMedia = null)
    {
        $this->mediaMedia = $mediaMedia;
    
        return $this;
    }

    /**
     * Get mediaMedia
     *
     * @return \Cittando\SiteBundle\Entity\Media 
     */
    public function getMediaMedia()
    {
        return $this->mediaMedia;
    }

    /**
     * Set userDetailUserDetail
     *
     * @param \Cittando\SiteBundle\Entity\UserDetail $userDetailUserDetail
     * @return MediaRel
     */
    public function setUserDetailUserDetail(\Cittando\SiteBundle\Entity\UserDetail $userDetailUserDetail = null)
    {
        $this->userDetailUserDetail = $userDetailUserDetail;
    
        return $this;
    }

    /**
     * Get userDetailUserDetail
     *
     * @return \Cittando\SiteBundle\Entity\UserDetail 
     */
    public function getUserDetailUserDetail()
    {
        return $this->userDetailUserDetail;
    }

    /**
     * Set venueVenue
     *
     * @param \Cittando\SiteBundle\Entity\Venue $venueVenue
     * @return MediaRel
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