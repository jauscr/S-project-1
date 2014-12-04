<?php

namespace Cittando\SiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Cittando\SiteBundle\Repository\FilmRepository;

/**
 * Film
 *
 * @ORM\Table(name="film")
 * @ORM\Entity(repositoryClass="Cittando\SiteBundle\Repository\FilmRepository")
 */
class Film
{
    /**
     * @var integer
     *
     * @ORM\Column(name="film_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="film_title", type="string", length=45, nullable=false)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="film_synopsis", type="text", nullable=true)
     */
    private $synopsis;

    /**
     * @var string
     *
     * @ORM\Column(name="film_director", type="string", length=255, nullable=true)
     */
    private $director;

    /**
     * @var string
     *
     * @ORM\Column(name="film_cast", type="string", length=255, nullable=true)
     */
    private $cast;

    /**
     * @var string
     *
     * @ORM\Column(name="film_writers", type="string", length=255, nullable=true)
     */
    private $writers;

    /**
     * @var string
     *
     * @ORM\Column(name="film_production_co", type="string", length=255, nullable=true)
     */
    private $productionCo;

    /**
     * @var string
     *
     * @ORM\Column(name="film_language", type="string", length=45, nullable=true)
     */
    private $language;

    /**
     * @var string
     *
     * @ORM\Column(name="website_url", type="string", length=255, nullable=true)
     */
    private $websiteUrl;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="release_date_usa", type="datetime", nullable=true)
     */
    private $releaseDateUsa;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="release_date_italy", type="datetime", nullable=true)
     */
    private $releaseDateItaly;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="release_date_other", type="datetime", nullable=true)
     */
    private $releaseDateOther;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modified", type="datetime", nullable=false)
     */
    private $modified;

    /**
     * @var \Category
     *
     * @ORM\ManyToOne(targetEntity="Category")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="category_category_id", referencedColumnName="category_id")
     * })
     */
    private $category;

    /**
     * @var \Promoted
     *
     * @ORM\ManyToOne(targetEntity="Promoted")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="promoted_promoted_id", referencedColumnName="promoted_id")
     * })
     */
    private $promoted;

    /**
     * @var \Status
     *
     * @ORM\ManyToOne(targetEntity="Status")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="status_status_id", referencedColumnName="status_id")
     * })
     */
    private $status;

    /**
     * @ORM\ManyToMany(targetEntity="Venue")
     * @ORM\JoinTable(name="film_venue_rel",
     *      joinColumns={@ORM\JoinColumn(name="film_film_id", referencedColumnName="film_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="venue_venue_id", referencedColumnName="venue_id")}
     *      )
     */
    protected $venue;

    /**
     * @ORM\ManyToMany(targetEntity="Media", inversedBy="film")
     * @ORM\JoinTable(name="media_rel",
     *      joinColumns={@ORM\JoinColumn(name="film_film_id", referencedColumnName="film_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="media_media_id", referencedColumnName="media_id")}
     *      )
     */
    protected $media;

    /**
     * Initializationn
     * @return void
     */
    public function __construct()
    {
        $this->media = new \Doctrine\Common\Collections\ArrayCollection();
        $this->venue = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set title
     *
     * @param string $title
     * @return Film
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set synopsis
     *
     * @param string $synopsis
     * @return Film
     */
    public function setSynopsis($synopsis)
    {
        $this->synopsis = $synopsis;

        return $this;
    }

    /**
     * Get synopsis
     *
     * @return string
     */
    public function getSynopsis()
    {
        return $this->synopsis;
    }

    /**
     * Set director
     *
     * @param string $director
     * @return Film
     */
    public function setDirector($director)
    {
        $this->director = $director;

        return $this;
    }

    /**
     * Get director
     *
     * @return string
     */
    public function getDirector()
    {
        return $this->director;
    }

    /**
     * Set cast
     *
     * @param string $cast
     * @return Film
     */
    public function setCast($cast)
    {
        $this->cast = $cast;

        return $this;
    }

    /**
     * Get cast
     *
     * @return string
     */
    public function getCast()
    {
        return $this->cast;
    }

    /**
     * Set writers
     *
     * @param string $writers
     * @return Film
     */
    public function setWriters($writers)
    {
        $this->writers = $writers;

        return $this;
    }

    /**
     * Get writers
     *
     * @return string
     */
    public function getWriters()
    {
        return $this->writers;
    }

    /**
     * Set productionCo
     *
     * @param string $productionCo
     * @return Film
     */
    public function setProductionCo($productionCo)
    {
        $this->productionCo = $productionCo;

        return $this;
    }

    /**
     * Get productionCo
     *
     * @return string
     */
    public function getProductionCo()
    {
        return $this->productionCo;
    }

    /**
     * Set language
     *
     * @param string $language
     * @return Film
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Get language
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set websiteUrl
     *
     * @param string $websiteUrl
     * @return Film
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
     * Set releaseDateUsa
     *
     * @param \DateTime $releaseDateUsa
     * @return Film
     */
    public function setReleaseDateUsa($releaseDateUsa)
    {
        $this->releaseDateUsa = $releaseDateUsa;

        return $this;
    }

    /**
     * Get releaseDateUsa
     *
     * @return \DateTime
     */
    public function getReleaseDateUsa()
    {
        return $this->releaseDateUsa;
    }

    /**
     * Set releaseDateItaly
     *
     * @param \DateTime $releaseDateItaly
     * @return Film
     */
    public function setReleaseDateItaly($releaseDateItaly)
    {
        $this->releaseDateItaly = $releaseDateItaly;

        return $this;
    }

    /**
     * Get releaseDateItaly
     *
     * @return \DateTime
     */
    public function getReleaseDateItaly()
    {
        return $this->releaseDateItaly;
    }

    /**
     * Set releaseDateOther
     *
     * @param \DateTime $releaseDateOther
     * @return Film
     */
    public function setReleaseDateOther($releaseDateOther)
    {
        $this->releaseDateOther = $releaseDateOther;

        return $this;
    }

    /**
     * Get releaseDateOther
     *
     * @return \DateTime
     */
    public function getReleaseDateOther()
    {
        return $this->releaseDateOther;
    }

    /**
     * Set modified
     *
     * @param \DateTime $modified
     * @return Film
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
     * @return Film
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
     * @return Film
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
     * @return Film
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
     * Getter
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * Setter
     */
    public function setMedia($media)
    {
        return $this->media = $media;
    }
}