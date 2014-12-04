<?php
namespace Soound\AppBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;

/**
 * @MongoDB\Document(collection="revisions")
 */
class Revision
{
 
    /**
     * @MongoDB\Id(strategy="AUTO")
     */
    private $revisionID;

    /**
     * @MongoDB\ReferenceOne(targetDocument="Submission", inversedBy="revisions")
     */
    protected $submission;

    /**
     * @MongoDB\Date
     */
    protected $date;
    
    /**
     * @MongoDB\String
     */         
    protected $title;

    /**
     * @MongoDB\String
     */
    protected $artist;

    /**
     * @MongoDB\String
     */
    protected $extension;

    /**
     * @MongoDB\ReferenceMany(targetDocument="Thread", mappedBy="revision", sort={"time"="asc"})
     */ 
    protected $waveThreads;

    /**
     * @MongoDB\EmbedMany(targetDocument="Comment")
     */
    protected $teamComments;

    /**
     * @MongoDB\Int
     */
    protected $avgRating = 0;

    /**
     * @MongoDB\EmbedMany(targetDocument="Rating")
     */
    protected $ratings = array();

    /**
     * @MongoDB\Boolean
     */
    protected $lastRev = true;

    /**
     * @MongoDB\Int
     */
    protected $duration = 0;

    
    public function __construct()
    {
        $this->teamComments = new \Doctrine\Common\Collections\ArrayCollection();
        $this->ratings = new \Doctrine\Common\Collections\ArrayCollection();

        $this->date = new \DateTime(); //Translates to MongoDate
    }

    /**
     * @return Int avgRating
     */
    public function getAvgRating()
    {
       return $this->avgRating/1000;
    }
    
    /**
     * Get revisionID
     *
     * @return id $revisionID
     */
    public function getRevisionID()
    {
        return $this->revisionID;
    }

    /**
     * Set submission
     *
     * @param Soound\AppBundle\Document\Submission $submission
     * @return self
     */
    public function setSubmission(\Soound\AppBundle\Document\Submission $submission)
    {
        $this->submission = $submission;
        return $this;
    }

    /**
     * Get submission
     *
     * @return Soound\AppBundle\Document\Submission $submission
     */
    public function getSubmission()
    {
        return $this->submission;
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
     * Add teamComment
     *
     * @param Soound\AppBundle\Document\Comment $teamComment
     */
    public function addTeamComment(\Soound\AppBundle\Document\Comment $teamComment)
    {
        $this->teamComments[] = $teamComment;
    }

    /**
     * Add teamCommentReply
     *
     * @param Soound\AppBundle\Document\Comment $reply, Id $teamCommentID
     */
    public function addTeamReply(\Soound\AppBundle\Document\Comment $reply, $teamCommentID)
    {
        foreach ($this->teamComments as $comment) {
            if($comment->getCommentID() === $teamCommentID){
                $comment->addReply($reply);
                break;
            }
        }
    }

    /**
     * Remove teamComment
     *
     * @param Soound\AppBundle\Document\Comment $teamComment
     */
    public function removeTeamComment(\Soound\AppBundle\Document\Comment $teamComment)
    {
        $this->teamComments->removeElement($teamComment);
    }

    /**
     * Get teamComments
     *
     * @return Doctrine\Common\Collections\Collection $teamComments
     */
    public function getTeamComments()
    {
        return $this->teamComments;
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
     * Add rating
     * If user has already rated this song replaces their rating,
     * otherwise adds a new rating to the collection $ratings and
     * recalculates the average rating
     *
     * @param Soound\AppBundle\Document\Rating $rating
     */
    public function addRating(\Soound\AppBundle\Document\Rating $newRating)
    {
        foreach ($this->ratings as $rating) {
            if( $rating->getUser()->getId() === $newRating->getUser()->getId() )
                $this->ratings->removeElement($rating);
        }
        $this->ratings[] = $newRating;

        $total = 0;
        foreach ($this->ratings as $rating)
            $total += $rating->getScore()/10; //Since scores are stored *10, we /10 here

        $this->avgRating = $total/count( $this->ratings ) * 1000; //And we mulitply by 1000 here to preserve 3 decimal places of accuracy
        
        if($this->lastRev){
            $this->submission->setRatings($this->ratings, $this->avgRating);
        }
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
     * Get ratings
     *
     * @return Doctrine\Common\Collections\Collection $ratings
     */
    public function getRatings()
    {
        return $this->ratings;
    }

    /**
     * Get score for requested userID or 0 if no rating exists for userID
     *
     * @return Int $score
     */
    public function getRating( \Soound\AppBundle\Document\User $user )
    {
        $score = 0;
        foreach ($this->ratings as $rating) {
            if( $rating->getUser()->getId() === $user->getId() ){
                $score = $rating->getScore();
                break;
            }
        }
        return $score;
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

    /**
     * Add waveThread
     *
     * @param Soound\AppBundle\Document\Thread $waveThread
     */
    public function addWaveThread(\Soound\AppBundle\Document\Thread $waveThread)
    {
        $this->waveThreads[] = $waveThread;
    }

    /**
     * Remove waveThread
     *
     * @param Soound\AppBundle\Document\Thread $waveThread
     */
    public function removeWaveThread(\Soound\AppBundle\Document\Thread $waveThread)
    {
        $this->waveThreads->removeElement($waveThread);
    }

    /**
     * Get waveThreads
     *
     * @return Doctrine\Common\Collections\Collection $waveThreads
     */
    public function getWaveThreads()
    {
        return $this->waveThreads;
    }

    /**
     * Get nextWaveThread
     *
     * @return Soound\AppBundle\Document\Thread $waveThread
     */
    public function getNextThreadID($threadID)
    {
        $size = count($this->waveThreads);
        for($i=0; $i<$size; $i++){
            if($this->waveThreads[$i]->getThreadID() === $threadID){
                if( $i < $size-1 )
                    return $this->waveThreads[$i+1]->getThreadID();
                else
                    return false;
            }
        }
        return false;
    }

    /**
     * Get prevWaveThread
     *
     * @return Soound\AppBundle\Document\Thread $waveThread
     */
    public function getPrevThreadID($threadID)
    {
        $size = count($this->waveThreads);
        for($i=0; $i<$size; $i++){
            if($this->waveThreads[$i]->getThreadID() === $threadID){
                if( $i > 0 )
                    return $this->waveThreads[$i-1]->getThreadID();
                else
                    return false;
            }
        }
        return false;
    }

    /**
     * Set lastRev
     *
     * @param boolean $lastRev
     * @return self
     */
    public function setLastRev($lastRev)
    {
        $this->lastRev = $lastRev;
        return $this;
    }

    /**
     * Get lastRev
     *
     * @return boolean $lastRev
     */
    public function getLastRev()
    {
        return $this->lastRev;
    }
}
