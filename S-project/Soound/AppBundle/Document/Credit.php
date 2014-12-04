<?php
namespace Soound\AppBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;

/**
 * @MongoDB\Document(collection="credits")
 */
class Credit
{
 
    /**
     * @MongoDB\Id(strategy="AUTO")
     */
    private $creditID;

    /**
     * @MongoDB\ReferenceOne(targetDocument="User", inversedBy="credits")
     */
    protected $user;

    /**
     * @MongoDB\Date
     */
    protected $date;
    
    /**
     * @MongoDB\String
     */         
    protected $title;

    /**
     * @MongoDB\Boolean
     */
    protected $sooundCredit = false;

    /**
     * @MongoDB\String
     */
    protected $extension;

    /**
     * @MongoDB\String
     */
    protected $roles;

    /**
     * @MongoDB\String
     */
    protected $description;

    /**
     * @MongoDB\Int
     */
    protected $duration = 0;

    /**
     * Get creditID
     *
     * @return id $creditID
     */
    public function getCreditID()
    {
        return $this->creditID;
    }

    /**
     * Set user
     *
     * @param Soound\AppBundle\Document\User $user
     * @return self
     */
    public function setUser(\Soound\AppBundle\Document\User $user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Get user
     *
     * @return Soound\AppBundle\Document\User $user
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Get title
     *
     * @return string $title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set artist
     *
     * @param string $artist
     * @return self
     */
    public function setArtist($artist)
    {
        $this->artist = $artist;
        return $this;
    }

    /**
     * Get artist
     *
     * @return string $artist
     */
    public function getArtist()
    {
        return $this->artist;
    }

    /**
     * Set extension
     *
     * @param string $extension
     * @return self
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;
        return $this;
    }

    /**
     * Get extension
     *
     * @return string $extension
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Set roles
     *
     * @param string $roles
     * @return self
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * Get roles
     *
     * @return string $roles
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return self
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get description
     *
     * @return string $description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set date
     *
     * @param date $date
     * @return self
     */
    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * Get date
     *
     * @return date $date
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set sooundCredit
     *
     * @param boolean $sooundCredit
     * @return self
     */
    public function setSooundCredit($sooundCredit)
    {
        $this->sooundCredit = $sooundCredit;
        return $this;
    }

    /**
     * Get sooundCredit
     *
     * @return boolean $sooundCredit
     */
    public function getSooundCredit()
    {
        return $this->sooundCredit;
    }

    /**
     * Set duration
     *
     * @param int $duration
     * @return self
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
        return $this;
    }

    /**
     * Get duration
     *
     * @return int $duration
     */
    public function getDuration()
    {
        return $this->duration;
    }
}
