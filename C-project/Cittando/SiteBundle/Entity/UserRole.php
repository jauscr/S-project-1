<?php

namespace Cittando\SiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserRole
 *
 * @ORM\Table(name="user_role")
 * @ORM\Entity
 */
class UserRole
{
    /**
     * @var integer
     *
     * @ORM\Column(name="user_role_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $userRoleId;

    /**
     * @var string
     *
     * @ORM\Column(name="user_role_name", type="string", length=45, nullable=true)
     */
    private $userRoleName;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modified", type="datetime", nullable=false)
     */
    private $modified;



    /**
     * Get userRoleId
     *
     * @return integer 
     */
    public function getUserRoleId()
    {
        return $this->userRoleId;
    }

    /**
     * Set userRoleName
     *
     * @param string $userRoleName
     * @return UserRole
     */
    public function setUserRoleName($userRoleName = 'Registered')
    {
        $this->userRoleName = $userRoleName;
    
        return $this;
    }

    /**
     * Get userRoleName
     *
     * @return string 
     */
    public function getUserRoleName()
    {
        return $this->userRoleName;
    }

    /**
     * Set modified
     *
     * @param \DateTime $modified
     * @return UserRole
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
}