<?php
namespace Soound\AppBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
//An activity has a:
//
//  -type: defines what kind of icon to show next to this activity
//  -mine: defines whether or not this activity is in connection with a project
//         the user owns (ie show icon in red, rather than gray)
//  -subject: text that's bold, comes before details, usually a project title
//  -user: (optional) ref to user that triggered the notification, come right
//         after subject
//  -text: (optional) text that comes after either subject or user if set
//  -date: the time when the notification was triggered

/**
 * @MongoDB\Document(collection="activity")
 */
class Activity
{
 
    /**
     * @MongoDB\Id(strategy="AUTO")
     */
    private $activityID;

    /**
     * @MongoDB\EmbedMany(targetDocument="ActivityContent")
     */
    protected $content;

    /**
     * @MongoDB\ReferenceOne(targetDocument="User", inversedBy="activity")
     */
    protected $user;

    /**
     * @MongoDB\String
     */
    protected $type;

    /**
     * @MongoDB\Boolean
     */
    protected $read = false;

    /**
     * @MongoDB\Date
     */
    protected $date;

    /**
     * @MongoDB\Boolean
     */
    protected $private = false;

    public function __construct()
    {
        $this->date = date_create();
    }

    /**
     * Get activityID
     *
     * @return id $activityID
     */
    public function getActivityID()
    {
        return $this->activityID;
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
     * Set private
     *
     * @param boolean $private
     * @return self
     */
    public function setPrivate($private)
    {
        $this->private = $private;
        return $this;
    }

    /**
     * Get private
     *
     * @return boolean $private
     */
    public function getPrivate()
    {
        return $this->private;
    }

    /**
     * Set read
     *
     * @param boolean $read
     * @return self
     */
    public function setRead($read)
    {
        $this->read = $read;
        return $this;
    }

    /**
     * Get read
     *
     * @return boolean $read
     */
    public function getRead()
    {
        return $this->read;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return self
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get type
     *
     * @return string $type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Add content
     *
     * @param Soound\AppBundle\Document\ActivityContent $content
     */
    public function addContent(\Soound\AppBundle\Document\ActivityContent $content)
    {
        $this->content[] = $content;
    }

    /**
     * Remove content
     *
     * @param Soound\AppBundle\Document\ActivityContent $content
     */
    public function removeContent(\Soound\AppBundle\Document\ActivityContent $content)
    {
        $this->content->removeElement($content);
    }

    /**
     * Get content
     *
     * @return Doctrine\Common\Collections\Collection $content
     */
    public function getContent()
    {
        return $this->content;
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
}
