<?php
namespace Soound\AppBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;

/**
 * @MongoDB\Document(collection="threads")
 */
class Thread
{
 
    /**
     * @MongoDB\Id(strategy="AUTO")
     */
    private $threadID;

    /**
     * @MongoDB\ReferenceOne(targetDocument="Revision", inversedBy="waveThreads")
     */
    private $revision;

    /**
     * @MongoDB\EmbedMany(targetDocument="Comment")
     */ 
    protected $comments;

    /**
     * @MongoDB\Int
     */
    protected $time;

    /**
     * @MongoDB\Collection
     */
    protected $read = array();

    public function __construct()
    {
        $this->comments = new \Doctrine\Common\Collections\ArrayCollection();
        $this->read = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Get threadID
     *
     * @return id $threadID
     */
    public function getThreadID()
    {
        return $this->threadID;
    }

    /**
     * Add comment
     *
     * @param Soound\AppBundle\Document\Comment $comment
     */
    public function addComment(\Soound\AppBundle\Document\Comment $comment)
    {
        $this->comments[] = $comment;
    }

    /**
     * Add reply
     *
     * @param Soound\AppBundle\Document\Comment $reply, Id $commentID
     */
    public function addReply(\Soound\AppBundle\Document\Comment $reply, $commentID)
    {
        foreach ($this->comments as $comment) {
            if($commentID === $comment->getCommentID()){
                $comment->addReply($reply);
                break;
            }
        }
    }

    /**
     * Remove comment
     *
     * @param Soound\AppBundle\Document\Comment $comment
     */
    public function removeComment(\Soound\AppBundle\Document\Comment $comment)
    {
        $this->comments->removeElement($comment);
    }

    /**
     * Get comments
     *
     * @return Doctrine\Common\Collections\Collection $comments
     */
    public function getComments()
    {
        return $this->comments;
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
     * Set time
     *
     * @param int $time
     * @return self
     */
    public function setTime($time)
    {
        $this->time = $time;
        return $this;
    }

    /**
     * Get time
     *
     * @return int $time
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Set read
     *
     * @param string $userId
     * @return self
     */
    public function setRead($userId)
    {
        $this->read[] = $userId;
        return $this;
    }

    /**
     * Get read
     *
     * @return boolean read status
     */
    public function getRead($userId = false)
    {
        if($userId){
            foreach ($this->read as $readUser) {
                if($readUser==$userId)
                    return true;
            }
        }
        else
            return $this->read;
    }

    /**
     * Cleans read status
     *
     * @return self
     */
    public function cleanRead()
    {
        $this->read = array();
        return $this;
    }
}
