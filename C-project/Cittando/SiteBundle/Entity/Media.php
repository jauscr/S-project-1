<?php

namespace Cittando\SiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cittando\SiteBundle\Entity\Media
 *
 * @ORM\Table(name="media")
 * @ORM\Entity
 */
class Media
{
    /**
     * @var integer
     *
     * @ORM\Column(name="media_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $mediaId;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=255, nullable=true)
     */
    protected $path;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255, nullable=true)
     */
    protected $url;

    /**
     * @var string
     *
     * @ORM\Column(name="media_description", type="string", length=255, nullable=true)
     */
    protected $mediaDescription;

    /**
     * @var string
     *
     * @ORM\Column(name="media_alt_tag", type="string", length=45, nullable=true)
     */
    protected $mediaAltTag;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime", nullable=true)
     */
    protected $created;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modified", type="datetime", nullable=false)
     */
    protected $modified;

    /**
     * @ORM\ManyToMany(targetEntity="Event", mappedBy="media")
     */
    protected $event;

    /**
     * @ORM\ManyToMany(targetEntity="Venue", mappedBy="media")
     */
    protected $venue;

    /**
     * @ORM\ManyToMany(targetEntity="Film", mappedBy="media")
     */
    protected $film;

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
     * Get mediaId
     *
     * @return integer
     */
    public function getMediaId()
    {
        return $this->mediaId;
    }

    /**
     * Set path
     *
     * @param string $path
     * @return Media
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return Media
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set mediaDescription
     *
     * @param string $mediaDescription
     * @return Media
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
     * @return Media
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
     * Set created
     *
     * @param \DateTime $created
     * @return Media
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set modified
     *
     * @param \DateTime $modified
     * @return Media
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

    public function __construct()
    {
        $this->event = new \Doctrine\Common\Collections\ArrayCollection();
        $this->venue = new \Doctrine\Common\Collections\ArrayCollection();
        $this->film = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * To string magic method
     * @return string
     */
    public function __toString()
    {
        $name = empty($this->mediaDescription) ? $this->path : $this->mediaDescription;
        $name = empty($name) ? $this->url : $name;

        return (string)$name;
    }
}