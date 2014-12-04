<?php

namespace Cittando\SiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ArtistLocale
 *
 * @ORM\Table(name="artist_locale")
 * @ORM\Entity
 */
class ArtistLocale
{
    /**
     * @var integer
     *
     * @ORM\Column(name="artist_locale_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $artistLocaleId;

    /**
     * @var string
     *
     * @ORM\Column(name="artist_bio", type="text", nullable=true)
     */
    private $artistBio;

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
     * @var \Locale
     *
     * @ORM\ManyToOne(targetEntity="Locale")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="locale_locale_id", referencedColumnName="locale_id")
     * })
     */
    private $localeLocale;



    /**
     * Get artistLocaleId
     *
     * @return integer 
     */
    public function getArtistLocaleId()
    {
        return $this->artistLocaleId;
    }

    /**
     * Set artistBio
     *
     * @param string $artistBio
     * @return ArtistLocale
     */
    public function setArtistBio($artistBio)
    {
        $this->artistBio = $artistBio;
    
        return $this;
    }

    /**
     * Get artistBio
     *
     * @return string 
     */
    public function getArtistBio()
    {
        return $this->artistBio;
    }

    /**
     * Set modified
     *
     * @param \DateTime $modified
     * @return ArtistLocale
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
     * @return ArtistLocale
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
     * Set localeLocale
     *
     * @param \Cittando\SiteBundle\Entity\Locale $localeLocale
     * @return ArtistLocale
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