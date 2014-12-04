<?php

namespace Cittando\SiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserFilmSaved
 *
 * @ORM\Table(name="user_film_saved")
 * @ORM\Entity(repositoryClass="Cittando\SiteBundle\Repository\UserFilmSavedRepository")
 */
class UserFilmSaved
{
    /**
     * @var integer
     *
     * @ORM\Column(name="user_film_saved_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $userFilmSavedId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modified", type="datetime", nullable=false)
     */
    private $modified;

    /**
     * @var \Film
     *
     * @ORM\ManyToOne(targetEntity="Film")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="film_film_id", referencedColumnName="film_id")
     * })
     */
    private $filmFilm;

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
     * Get userFilmSavedId
     *
     * @return integer 
     */
    public function getUserFilmSavedId()
    {
        return $this->userFilmSavedId;
    }

    /**
     * Set modified
     *
     * @param \DateTime $modified
     * @return UserFilmSaved
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
     * Set filmFilm
     *
     * @param \Cittando\SiteBundle\Entity\Film $filmFilm
     * @return UserFilmSaved
     */
    public function setFilmFilm(\Cittando\SiteBundle\Entity\Film $filmFilm = null)
    {
        $this->filmFilm = $filmFilm;
    
        return $this;
    }

    /**
     * Get filmFilm
     *
     * @return \Cittando\SiteBundle\Entity\Film 
     */
    public function getFilmFilm()
    {
        return $this->filmFilm;
    }

    /**
     * Set userUser
     *
     * @param \Cittando\SiteBundle\Entity\User $userUser
     * @return UserFilmSaved
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