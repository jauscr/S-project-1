<?php

namespace Cittando\SiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserHasVenue
 *
 * @ORM\Table(name="user_has_venue")
 * @ORM\Entity
 */
class UserHasVenue
{
    /**
     * @var integer
     *
     * @ORM\Column(name="user_has_venue_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $userHasVenueId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modified", type="datetime", nullable=false)
     */
    private $modified;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_user_id", referencedColumnName="user_id")
     * })
     */
    private $userUser;

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
     * Get userHasVenueId
     *
     * @return integer 
     */
    public function getUserHasVenueId()
    {
        return $this->userHasVenueId;
    }

    /**
     * Set modified
     *
     * @param \DateTime $modified
     * @return UserHasVenue
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
     * Set userUser
     *
     * @param \Cittando\SiteBundle\Entity\User $userUser
     * @return UserHasVenue
     */
    public function setUserUser(\Cittando\SiteBundle\Entity\User $userUser = null)
    {
        $this->userUser = $userUser;
    
        return $this;
    }

    /**
     * Get userUser
     *
     * @return \Cittando\SiteBundle\Entity\User 
     */
    public function getUserUser()
    {
        return $this->userUser;
    }

    /**
     * Set venueVenue
     *
     * @param \Cittando\SiteBundle\Entity\Venue $venueVenue
     * @return UserHasVenue
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