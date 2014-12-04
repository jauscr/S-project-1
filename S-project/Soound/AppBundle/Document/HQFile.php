<?php
namespace Soound\AppBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document(collection="hqFiles")
 */
class HQFile
{
 
    /**
     * @MongoDB\Id(strategy="AUTO")
     */
    private $hqFileID;

    /**
     * @MongoDB\Date
     */
    protected $uploadDate;

    /**
     * @MongoDB\Date
     */
    protected $downloadDate;

    /**
     * @MongoDB\ReferenceOne(targetDocument="User", simple="true")
     */
    protected $user;

    /**
     * @MongoDB\ReferenceOne(targetDocument="Project", inversedBy="HQFiles")
     */
    protected $project;

    /**
     * @MongoDB\String
     */
    protected $status = "pending";

    /**
     * @MongoDB\String
     */
    protected $extension;

    /**
     * @MongoDB\String
     */         
    protected $name;

    public function __construct()
    {
        $this->uploadDate = date_create();
    }

    /**
     * Get hqFileID
     *
     * @return id $hqFileID
     */
    public function getHqFileID()
    {
        return $this->hqFileID;
    }

    /**
     * Get uploadDate
     *
     * @return date $uploadDate
     */
    public function getUploadDate()
    {
        return $this->uploadDate;
    }

    /**
     * Set downloadDate
     *
     * @param date $downloadDate
     * @return self
     */
    public function setDownloadDate($downloadDate)
    {
        $this->downloadDate = $downloadDate;
        return $this;
    }

    /**
     * Get downloadDate
     *
     * @return date $downloadDate
     */
    public function getDownloadDate()
    {
        return $this->downloadDate;
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
     * Set name
     *
     * @param string $name
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set uploadDate
     *
     * @param date $uploadDate
     * @return self
     */
    public function setUploadDate($uploadDate)
    {
        $this->uploadDate = $uploadDate;
        return $this;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return self
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Get status
     *
     * @return string $status
     */
    public function getStatus()
    {
        return $this->status;
    }
}
