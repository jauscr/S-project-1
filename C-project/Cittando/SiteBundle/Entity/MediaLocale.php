<?php

namespace Cittando\SiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MediaLocale
 *
 * @ORM\Table(name="media_locale")
 * @ORM\Entity
 */
class MediaLocale
{
    /**
     * @var integer
     *
     * @ORM\Column(name="media_locale_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $mediaLocaleId;

    /**
     * @var string
     *
     * @ORM\Column(name="media_description", type="string", length=255, nullable=true)
     */
    private $mediaDescription;

    /**
     * @var string
     *
     * @ORM\Column(name="media_alt_tag", type="string", length=45, nullable=true)
     */
    private $mediaAltTag;

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
     * @var \Media
     *
     * @ORM\ManyToOne(targetEntity="Media")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="media_media_id", referencedColumnName="media_id")
     * })
     */
    private $mediaMedia;



    /**
     * Get mediaLocaleId
     *
     * @return integer 
     */
    public function getMediaLocaleId()
    {
        return $this->mediaLocaleId;
    }

    /**
     * Set mediaDescription
     *
     * @param string $mediaDescription
     * @return MediaLocale
     */
    public function setMediaDescription($mediaDescription)
    {
        $this->mediaDescription = $mediaDescription;
    
        return $this;
    }

    /**
     * Get mediaDescription
     *
     * @return string 
     */
    public function getMediaDescription()
    {
        return $this->mediaDescription;
    }

    /**
     * Set mediaAltTag
     *
     * @param string $mediaAltTag
     * @return MediaLocale
     */
    public function setMediaAltTag($mediaAltTag)
    {
        $this->mediaAltTag = $mediaAltTag;
    
        return $this;
    }

    /**
     * Get mediaAltTag
     *
     * @return string 
     */
    public function getMediaAltTag()
    {
        return $this->mediaAltTag;
    }

    /**
     * Set modified
     *
     * @param \DateTime $modified
     * @return MediaLocale
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
     * @return MediaLocale
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
     * Set mediaMedia
     *
     * @param \Cittando\SiteBundle\Entity\Media $mediaMedia
     * @return MediaLocale
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
}