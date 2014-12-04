<?php

namespace Soound\AppBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document(collection="projectFiles")
 */
class ProjectFile
{

    /**
     * @MongoDB\Id(strategy="AUTO")
     */
    private $fileId;

    /**
     * @MongoDB\ReferenceOne(targetDocument="Project", inversedBy="projectFiles")
     */
    protected $project;

    /**
     * @MongoDB\String
     */
    protected $link;

    /**
     * @MongoDB\String
     */         
    protected $title;
    
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
     * Get fileId
     *
     * @return id $fileId
     */
    public function getFileId()
    {
        return $this->fileId;
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

}
