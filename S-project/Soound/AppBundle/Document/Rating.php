<?php
namespace Soound\AppBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;

/**
 * @MongoDB\EmbeddedDocument
 */
class Rating
{
 
    /**
     * @MongoDB\Id(strategy="AUTO")
     */
    private $ratingID;

    /**
     * @MongoDB\ReferenceOne(targetDocument="Revision", inversedBy="ratings")
     */
    protected $revision;

    /**
     * @MongoDB\ReferenceOne(targetDocument="User", inversedBy="ratings")
     */
    protected $user;

    /**
     * @MongoDB\String
     */
    protected $userID;

    /**
     * @MongoDB\Int
     */
    protected $score = 0;


    /**
     * Get ratingID
     *
     * @return id $ratingID
     */
    public function getRatingID()
    {
        return $this->ratingID;
    }

    /**
     * Set revision
     *
     * @param Soound\AppBundle\Document\Revision $revision
     * @return self
     */
    public function setRevision(\Soound\AppBundle\Document\Revision $revision)
    {
        $this->revision = $revision;
        return $this;
    }

    /**
     * Get revision
     *
     * @return Soound\AppBundle\Document\Revision $revision
     */
    public function getRevision()
    {
        return $this->revision;
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
        $this->userID = $user->getId();
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
     * Set score
     *
     * @param int $score
     * @return self
     */
    public function setScore($score)
    {
        $this->score = $score*10; //Scores are stored times ten to perserve decimal places
        return $this;
    }

    /**
     * Get score
     *
     * @return int $score
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * Set userID
     *
     * @param string $userID
     * @return self
     */
    public function setUserID($userID)
    {
        $this->userID = $userID;
        return $this;
    }

    /**
     * Get userID
     *
     * @return string $userID
     */
    public function getUserID()
    {
        return $this->userID;
    }
}
