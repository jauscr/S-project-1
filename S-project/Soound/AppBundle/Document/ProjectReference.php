<?php

namespace Soound\AppBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;


/**
 * @MongoDB\EmbeddedDocument
 */
class ProjectReference
{

    /**
     * @MongoDB\Id(strategy="AUTO")
     */
    private $referenceId;

    /**
     * @MongoDB\String
     */
    protected $link;

    /**
     * @MongoDB\String
     */
    protected $description;

    /**
     * @MongoDB\String
     */
    protected $extension;

    /**
     * @MongoDB\Boolean
     */
    protected $isAudio = false;

    /**
     * @MongoDB\String
     */         
    protected $title;

    /**
     * @MongoDB\Int
     */
    protected $duration = 0;

    public function __construct()
    {

    }
    /**
     * @return String $link
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @param string $link
     */
    public function setLink($link)
    {
        $this->link = $link;
    }
    /**
     * @return String $description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get referenceId
     *
     * @return id $referenceId
     */
    public function getReferenceId()
    {
        return $this->referenceId;
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
     * @param boolean $isAudio
     */
    public function setIsAudio($isAudio)
    {
        $this->isAudio = $isAudio;
    }

    /**
     * @return boolean $isAudio
     */
    public function isAudio()
    {
        return $this->isAudio;
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
