<?php

namespace Cittando\SiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserLogType
 *
 * @ORM\Table(name="user_log_type")
 * @ORM\Entity
 */
class UserLogType
{
    /**
     * @var integer
     *
     * @ORM\Column(name="user_log_type_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $userLogTypeId;

    /**
     * @var string
     *
     * @ORM\Column(name="user_log_name", type="string", length=45, nullable=true)
     */
    private $userLogName;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modified", type="datetime", nullable=false)
     */
    private $modified;

    /**
     * @var \UserLog
     *
     * @ORM\ManyToOne(targetEntity="UserLog")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_log_user_log_id", referencedColumnName="user_log_id")
     * })
     */
    private $userLogUserLog;



    /**
     * Get userLogTypeId
     *
     * @return integer 
     */
    public function getUserLogTypeId()
    {
        return $this->userLogTypeId;
    }

    /**
     * Set userLogName
     *
     * @param string $userLogName
     * @return UserLogType
     */
    public function setUserLogName($userLogName)
    {
        $this->userLogName = $userLogName;
    
        return $this;
    }

    /**
     * Get userLogName
     *
     * @return string 
     */
    public function getUserLogName()
    {
        return $this->userLogName;
    }

    /**
     * Set modified
     *
     * @param \DateTime $modified
     * @return UserLogType
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
     * Set userLogUserLog
     *
     * @param \Cittando\SiteBundle\Entity\UserLog $userLogUserLog
     * @return UserLogType
     */
    public function setUserLogUserLog(\Cittando\SiteBundle\Entity\UserLog $userLogUserLog = null)
    {
        $this->userLogUserLog = $userLogUserLog;
    
        return $this;
    }

    /**
     * Get userLogUserLog
     *
     * @return \Cittando\SiteBundle\Entity\UserLog 
     */
    public function getUserLogUserLog()
    {
        return $this->userLogUserLog;
    }
}