<?php

namespace Cittando\SiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserHasEvent
 *
 * @ORM\Table(name="user_has_event")
 * @ORM\Entity
 */
class UserHasEvent
{
    /**
     * @var integer
     *
     * @ORM\Column(name="user_has_event_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $userHasEventId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modified", type="datetime", nullable=false)
     */
    private $modified;

    /**
     * @var \Event
     *
     * @ORM\ManyToOne(targetEntity="Event")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="event_event_id", referencedColumnName="event_id")
     * })
     */
    private $eventEvent;

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
     * Get userHasEventId
     *
     * @return integer 
     */
    public function getUserHasEventId()
    {
        return $this->userHasEventId;
    }

    /**
     * Set modified
     *
     * @param \DateTime $modified
     * @return UserHasEvent
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
     * Set eventEvent
     *
     * @param \Cittando\SiteBundle\Entity\Event $eventEvent
     * @return UserHasEvent
     */
    public function setEventEvent(\Cittando\SiteBundle\Entity\Event $eventEvent = null)
    {
        $this->eventEvent = $eventEvent;
    
        return $this;
    }

    /**
     * Get eventEvent
     *
     * @return \Cittando\SiteBundle\Entity\Event 
     */
    public function getEventEvent()
    {
        return $this->eventEvent;
    }

    /**
     * Set userUser
     *
     * @param \Cittando\SiteBundle\Entity\User $userUser
     * @return UserHasEvent
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