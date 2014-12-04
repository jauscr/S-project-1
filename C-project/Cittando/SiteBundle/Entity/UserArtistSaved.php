<?php

namespace Cittando\SiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserArtistSaved
 *
 * @ORM\Table(name="user_artist_saved")
 * @ORM\Entity
 */
class UserArtistSaved
{
    /**
     * @var integer
     *
     * @ORM\Column(name="user_artist_saved_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $userArtistSavedId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modified", type="datetime", nullable=false)
     */
    private $modified;

    /**
     * @var \Artist
     *
     * @ORM\ManyToOne(targetEntity="Artist")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="artist_artist_id", referencedColumnName="artist_id")
     * })
     */
    private $artistArtist;

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
     * Get userArtistSavedId
     *
     * @return integer 
     */
    public function getUserArtistSavedId()
    {
        return $this->userArtistSavedId;
    }

    /**
     * Set modified
     *
     * @param \DateTime $modified
     * @return UserArtistSaved
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
     * Set artistArtist
     *
     * @param \Cittando\SiteBundle\Entity\Artist $artistArtist
     * @return UserArtistSaved
     */
    public function setArtistArtist(\Cittando\SiteBundle\Entity\Artist $artistArtist = null)
    {
        $this->artistArtist = $artistArtist;
    
        return $this;
    }

    /**
     * Get artistArtist
     *
     * @return \Cittando\SiteBundle\Entity\Artist 
     */
    public function getArtistArtist()
    {
        return $this->artistArtist;
    }

    /**
     * Set userUser
     *
     * @param \Cittando\SiteBundle\Entity\User $userUser
     * @return UserArtistSaved
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