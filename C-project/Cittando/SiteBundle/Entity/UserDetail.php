<?php

namespace Cittando\SiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserDetail
 *
 * @ORM\Table(name="user_detail")
 * @ORM\Entity
 */
class UserDetail
{
    /**
     * @var integer
     *
     * @ORM\Column(name="user_detail_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $userDetailId;

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
     *   @ORM\JoinColumn(name="cities_cities_id", referencedColumnName="city_id")
     * })
     */
    private $citiesCities;

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
     * Get userDetailId
     *
     * @return integer 
     */
    public function getUserDetailId()
    {
        return $this->userDetailId;
    }

    /**
     * Set modified
     *
     * @param \DateTime $modified
     * @return UserDetail
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
     * Set citiesCities
     *
     * @param \Cittando\SiteBundle\Entity\City $citiesCities
     * @return UserDetail
     */
    public function setCitiesCities(\Cittando\SiteBundle\Entity\City $citiesCities = null)
    {
        $this->citiesCities = $citiesCities;
    
        return $this;
    }

    /**
     * Get citiesCities
     *
     * @return \Cittando\SiteBundle\Entity\City 
     */
    public function getCitiesCities()
    {
        return $this->citiesCities;
    }

    /**
     * Set userUser
     *
     * @param \Cittando\SiteBundle\Entity\User $userUser
     * @return UserDetail
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
}