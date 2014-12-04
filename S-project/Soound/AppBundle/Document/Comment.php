<?php
namespace Soound\AppBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;

/**
 * @MongoDB\EmbeddedDocument
 */
class Comment
{
 
    /**
     * @MongoDB\Id(strategy="AUTO")
     */
    private $commentID;

    /**
     * @MongoDB\ReferenceOne(targetDocument="User")
     */
    protected $user;

    /**
     * @MongoDB\EmbedMany(targetDocument="Comment")
     */
    protected $replies;

    /**
     * @MongoDB\String
     */
    protected $text;
    
    public function __construct()
    {
        $this->replies = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Get commentID
     *
     * @return id $commentID
     */
    public function getCommentID()
    {
        return $this->commentID;
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
     * Add reply
     *
     * @param Soound\AppBundle\Document\Comment $reply
     */
    public function addReply(\Soound\AppBundle\Document\Comment $reply)
    {
        $this->replies[] = $reply;
    }

    /**
     * Remove reply
     *
     * @param Soound\AppBundle\Document\Comment $reply
     */
    public function removeReply(\Soound\AppBundle\Document\Comment $reply)
    {
        $this->replies->removeElement($reply);
    }

    /**
     * Get replies
     *
     * @return Doctrine\Common\Collections\Collection $replies
     */
    public function getReplies()
    {
        return $this->replies;
    }

    /**
     * Set text
     *
     * @param string $text
     * @return self
     */
    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    /**
     * Get text
     *
     * @return string $text
     */
    public function getText()
    {
        return $this->text;
    }
}
