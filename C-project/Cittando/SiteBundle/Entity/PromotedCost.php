<?php

namespace Cittando\SiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PromotedCost
 *
 * @ORM\Table(name="promoted_cost")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class PromotedCost
{
    /**
     * @var integer
     *
     * @ORM\Column(name="promoted_cost_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="cost", type="integer", nullable=false)
     */
    private $cost;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modified", type="datetime", nullable=false)
     */
    private $modified;

    /**
     * @var \City
     *
     * @ORM\ManyToOne(targetEntity="City")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="cities_cities_id", referencedColumnName="city_id")
     * })
     */
    private $cities;

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
     * Set cost
     *
     * @param integer $cost
     * @return PromotedCost
     */
    public function setCost($cost)
    {
        $this->cost = $cost;

        return $this;
    }

    /**
     * Get cost
     *
     * @return integer
     */
    public function getCost()
    {
        return $this->cost;
    }

    /**
     * Set modified
     *
     * @param \DateTime $modified
     * @return PromotedCost
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
     * Set cities
     *
     * @param \Cittando\SiteBundle\Entity\City $cities
     * @return PromotedCost
     */
    public function setCities(\Cittando\SiteBundle\Entity\City $cities = null)
    {
        $this->cities = $cities;

        return $this;
    }

    /**
     * Get cities
     *
     * @return \Cittando\SiteBundle\Entity\City
     */
    public function getCities()
    {
        return $this->cities;
    }

    /**
     * Set promoted
     *
     * @param \Cittando\SiteBundle\Entity\Promoted $promoted
     * @return PromotedCost
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
}