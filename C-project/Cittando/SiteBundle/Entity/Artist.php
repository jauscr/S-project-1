<?php

namespace Cittando\SiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Artist
 *
 * @ORM\Table(name="artist")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Artist
{
    /**
     * @var integer
     *
     * @ORM\Column(name="artist_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="artist_name", type="string", length=45, nullable=false)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="artist_bio", type="text", nullable=true)
     */
    protected $bio;

    /**
     * @var string
     *
     * @ORM\Column(name="artist_website_url", type="string", length=255, nullable=true)
     */
    protected $websiteUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="artist_website_url2", type="string", length=255, nullable=true)
     */
    protected $websiteUrl2;

    /**
     * @var string
     *
     * @ORM\Column(name="artist_facebook_url", type="string", length=255, nullable=true)
     */
    protected $facebookUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="artist_twitter_username", type="string", length=20, nullable=true)
     */
    protected $twitterUsername;

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
     * Set name
     *
     * @param string $name
     * @return Artist
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set bio
     *
     * @param string $bio
     * @return Artist
     */
    public function setBio($bio)
    {
        $this->bio = $bio;

        return $this;
    }

    /**
     * Get bio
     *
     * @return string
     */
    public function getBio()
    {
        return $this->bio;
    }

    /**
     * Set websiteUrl
     *
     * @param string $websiteUrl
     * @return Artist
     */
    public function setWebsiteUrl($websiteUrl)
    {
        $this->websiteUrl = $websiteUrl;

        return $this;
    }

    /**
     * Get websiteUrl
     *
     * @return string
     */
    public function getWebsiteUrl()
    {
        return $this->websiteUrl;
    }

    /**
     * Set websiteUrl2
     *
     * @param string $websiteUrl2
     * @return Artist
     */
    public function setWebsiteUrl2($websiteUrl2)
    {
        $this->websiteUrl2 = $websiteUrl2;

        return $this;
    }

    /**
     * Get websiteUrl2
     *
     * @return string
     */
    public function getWebsiteUrl2()
    {
        return $this->websiteUrl2;
    }

    /**
     * Set facebookUrl
     *
     * @param string $facebookUrl
     * @return Artist
     */
    public function setFacebookUrl($facebookUrl)
    {
        $this->facebookUrl = $facebookUrl;

        return $this;
    }

    /**
     * Get facebookUrl
     *
     * @return string
     */
    public function getFacebookUrl()
    {
        return $this->facebookUrl;
    }

    /**
     * Set twitterUsername
     *
     * @param string $twitterUsername
     * @return Artist
     */
    public function setTwitterUsername($twitterUsername)
    {
        $this->twitterUsername = $twitterUsername;

        return $this;
    }

    /**
     * Get twitterUsername
     *
     * @return string
     */
    public function getTwitterUsername()
    {
        return $this->twitterUsername;
    }

    /**
     * Set modified
     *
     * @param \DateTime $modified
     * @return Artist
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
     * @return Artist
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
     * @return Artist
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
     * @return Artist
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

    /**
     * To string magic method
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }
}