<?php
namespace Soound\AppBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;

/**
 * @MongoDB\Document(collection="submissions")
 */
class Submission
{
 
    /**
     * @MongoDB\Id(strategy="AUTO")
     */
    private $submissionID;

    /**
     * @MongoDB\String
     */
    private $publicID;

    /**
     * @MongoDB\ReferenceOne(targetDocument="User", simple="true")
     */
    protected $user;

    /**
     * @MongoDB\ReferenceOne(targetDocument="Project", simple="true")
     */
    protected $project;
    
    /**
     * @MongoDB\String
     */         
    protected $title;

    /**
     * @MongoDB\String
     */
    protected $artist;

    /**
     * @MongoDB\ReferenceMany(targetDocument="Revision", mappedBy="submission")
     */ 
    protected $revisions;

    /**
     * @MongoDB\ReferenceOne(targetDocument="Revision", mappedBy="submission", criteria={"lastRev": true})
     */
    protected $lastRevision;

    /**
     * @MongoDB\ReferenceMany(targetDocument="User", simple="true")
     */
    protected $listeners;

    /**
     * @MongoDB\EmbedMany(targetDocument="Rating")
     */
    protected $ratings = array();

    /**
     * @MongoDB\Int
     */
    protected $avgRating = 0;

    /**
     * @MongoDB\Boolean
     */
    protected $rejected = false;

    public function __construct()
    {
        $this->publicID = uniqid( crc32(mt_rand()), false);
        $this->revisions = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Get submissionID
     *
     * @return id $submissionID
     */
    public function getSubmissionID()
    {
        return $this->submissionID;
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
     * Set project
     *
     * @param Soound\AppBundle\Document\Project $project
     * @return self
     */
    public function setProject(\Soound\AppBundle\Document\Project $project)
    {
        $this->project = $project;
        return $this;
    }

    /**
     * Get project
     *
     * @return Soound\AppBundle\Document\Project $project
     */
    public function getProject()
    {
        return $this->project;
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
     * Add revision
     *
     * @param Soound\AppBundle\Document\Revision $revision
     */
    public function addRevision(\Soound\AppBundle\Document\Revision $revision)
    {
        if($this->lastRevision)
            $this->lastRevision->setLastRev(false);
        $this->ratings = array();
        $this->lastRevision = $revision;
        $this->listeners = new \Doctrine\Common\Collections\ArrayCollection(); //Reset the listeners
        $this->revisions[] = $revision;
    }

    /**
     * Remove revision
     *
     * @param Soound\AppBundle\Document\Revision $revision
     */
    public function removeRevision(\Soound\AppBundle\Document\Revision $revision)
    {
        $this->revisions->removeElement($revision);
    }

    /**
     * Get revisions
     *
     * @return Doctrine\Common\Collections\Collection $revisions
     */
    public function getRevisions()
    {
        return $this->revisions;
    }

    /**
     * Set lastRevision
     *
     * @param Soound\AppBundle\Document\Revision $lastRevision
     * @return self
     */
    public function setLastRevision(\Soound\AppBundle\Document\Revision $lastRevision)
    {
        $this->lastRevision = $lastRevision;
        return $this;
    }

    /**
     * Get lastRevision
     *
     * @return Soound\AppBundle\Document\Revision $lastRevision
     */
    public function getLastRevision()
    {
        return $this->lastRevision;
    }

    /**
     * Add listener
     *
     * @param Soound\AppBundle\Document\User $listener
     */
    public function addListener(\Soound\AppBundle\Document\User $listener)
    {
        $this->listeners[] = $listener;
    }

    /**
     * Remove listener
     *
     * @param Soound\AppBundle\Document\User $listener
     */
    public function removeListener(\Soound\AppBundle\Document\User $listener)
    {
        $this->listeners->removeElement($listener);
    }

    /**
     * Get listeners
     *
     * @return Doctrine\Common\Collections\Collection $listeners
     */
    public function getListeners()
    {
        return $this->listeners;
    }

    /**
     * @return Boolean $hasListened
     */
    public function hasListened(\Soound\AppBundle\Document\User $user)
    {
        foreach ($this->listeners as $listener) {
            if( $listener->getId() === $user->getId() )
                return true;
        }
        return false;
    }

    /**
     * Add rating
     *
     * @param Soound\AppBundle\Document\Rating $rating
     */
    public function addRating(\Soound\AppBundle\Document\Rating $rating)
    {
        $this->ratings[] = $rating;
    }

    /**
     * Remove rating
     *
     * @param Soound\AppBundle\Document\Rating $rating
     */
    public function removeRating(\Soound\AppBundle\Document\Rating $rating)
    {
        $this->ratings->removeElement($rating);
    }

    /**
     * Set ratings
     *
     * @param Doctrine\Common\Collections\Collection $ratings, Int $avgRating
     */
    public function setRatings(\Doctrine\Common\Collections\Collection $ratings, $avgRating)
    {
        $this->ratings = $ratings;
        $this->avgRating = $avgRating;
    }

    /**
     * Get ratings
     *
     * @return Doctrine\Common\Collections\Collection $ratings
     */
    public function getRatings()
    {
        return $this->ratings;
    }

    /**
     * @return Boolean $hasRated
     */
    public function hasRated(\Soound\AppBundle\Document\User $user)
    {
        foreach ($this->ratings as $rating) {
            if( $rating->getUser()->getId() === $user->getId() )
                return true;
        }
        return false;
    }

    /**
     * Set avgRating
     *
     * @param int $avgRating
     * @return self
     */
    public function setAvgRating($avgRating)
    {
        $this->avgRating = $avgRating;
        return $this;
    }

    /**
     * Get avgRating
     *
     * @return int $avgRating
     */
    public function getAvgRating()
    {
        return $this->avgRating;
    }

    /**
     * Set rejected
     *
     * @param boolean $rejected
     * @return self
     */
    public function setRejected($rejected)
    {
        $this->rejected = $rejected;
        return $this;
    }

    /**
     * Get rejected
     *
     * @return boolean $rejected
     */
    public function getRejected()
    {
        return $this->rejected;
    }

    /**
     * Set publicID
     *
     * @param string $publicID
     * @return self
     */
    public function setPublicID($publicID)
    {
        $this->publicID = $publicID;
        return $this;
    }

    /**
     * Get publicID
     *
     * @return string $publicID
     */
    public function getPublicID()
    {
        return $this->publicID;
    }
}
