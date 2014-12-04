<?php

namespace Cittando\SiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FilmVenueRel
 *
 * @ORM\Table(name="film_venue_rel")
 * @ORM\Entity(repositoryClass="Cittando\SiteBundle\Repository\FilmVenueRelRepository")
 */
class FilmVenueRel
{
    /**
     * @var integer
     *
     * @ORM\Column(name="film_venue_rel_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $filmVenueRelId;

    /**
     * @var string
     *
     * @ORM\Column(name="showtimes", type="text", nullable=true)
     */
    private $showtimes;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_from", type="datetime", nullable=true)
     */
    private $dateFrom;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_to", type="datetime", nullable=true)
     */
    private $dateTo;

    /**
     * @var string
     *
     * @ORM\Column(name="ticket_url", type="string", length=255, nullable=true)
     */
    private $ticketUrl;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modified", type="datetime", nullable=false)
     */
    private $modified;

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
     * @var \Venue
     *
     * @ORM\ManyToOne(targetEntity="Venue")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="venue_venue_id", referencedColumnName="venue_id")
     * })
     */
    private $venueVenue;



    /**
     * Get filmVenueRelId
     *
     * @return integer 
     */
    public function getFilmVenueRelId()
    {
        return $this->filmVenueRelId;
    }

    /**
     * Set showtimes
     *
     * @param string $showtimes
     * @return FilmVenueRel
     */
    public function setShowtimes($showtimes)
    {
        $this->showtimes = $showtimes;
    
        return $this;
    }

    /**
     * Get showtimes
     *
     * @return string 
     */
    public function getShowtimes()
    {
        return $this->showtimes;
    }

    /**
     * Set dateFrom
     *
     * @param \DateTime $dateFrom
     * @return FilmVenueRel
     */
    public function setDateFrom($dateFrom)
    {
        $this->dateFrom = $dateFrom;
    
        return $this;
    }

    /**
     * Get dateFrom
     *
     * @return \DateTime 
     */
    public function getDateFrom()
    {
        return $this->dateFrom;
    }

    /**
     * Set dateTo
     *
     * @param \DateTime $dateTo
     * @return FilmVenueRel
     */
    public function setDateTo($dateTo)
    {
        $this->dateTo = $dateTo;
    
        return $this;
    }

    /**
     * Get dateTo
     *
     * @return \DateTime 
     */
    public function getDateTo()
    {
        return $this->dateTo;
    }

    /**
     * Set ticketUrl
     *
     * @param string $ticketUrl
     * @return FilmVenueRel
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
     * @return FilmVenueRel
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
     * Set filmFilm
     *
     * @param \Cittando\SiteBundle\Entity\Film $filmFilm
     * @return FilmVenueRel
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
     * Set venueVenue
     *
     * @param \Cittando\SiteBundle\Entity\Venue $venueVenue
     * @return FilmVenueRel
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