<?php

namespace Cittando\SiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FilmLocale
 *
 * @ORM\Table(name="film_locale")
 * @ORM\Entity
 */
class FilmLocale
{
    /**
     * @var integer
     *
     * @ORM\Column(name="film_locale_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $filmLocaleId;

    /**
     * @var string
     *
     * @ORM\Column(name="film_title", type="string", length=45, nullable=true)
     */
    private $filmTitle;

    /**
     * @var string
     *
     * @ORM\Column(name="silm_synopsis", type="text", nullable=true)
     */
    private $silmSynopsis;

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
     * @var \Locale
     *
     * @ORM\ManyToOne(targetEntity="Locale")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="locale_locale_id", referencedColumnName="locale_id")
     * })
     */
    private $localeLocale;



    /**
     * Get filmLocaleId
     *
     * @return integer 
     */
    public function getFilmLocaleId()
    {
        return $this->filmLocaleId;
    }

    /**
     * Set filmTitle
     *
     * @param string $filmTitle
     * @return FilmLocale
     */
    public function setFilmTitle($filmTitle)
    {
        $this->filmTitle = $filmTitle;
    
        return $this;
    }

    /**
     * Get filmTitle
     *
     * @return string 
     */
    public function getFilmTitle()
    {
        return $this->filmTitle;
    }

    /**
     * Set silmSynopsis
     *
     * @param string $silmSynopsis
     * @return FilmLocale
     */
    public function setSilmSynopsis($silmSynopsis)
    {
        $this->silmSynopsis = $silmSynopsis;
    
        return $this;
    }

    /**
     * Get silmSynopsis
     *
     * @return string 
     */
    public function getSilmSynopsis()
    {
        return $this->silmSynopsis;
    }

    /**
     * Set modified
     *
     * @param \DateTime $modified
     * @return FilmLocale
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
     * @return FilmLocale
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
     * Set localeLocale
     *
     * @param \Cittando\SiteBundle\Entity\Locale $localeLocale
     * @return FilmLocale
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