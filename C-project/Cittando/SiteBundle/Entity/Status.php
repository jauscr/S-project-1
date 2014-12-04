<?php

namespace Cittando\SiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Status
 *
 * @ORM\Table(name="status")
 * @ORM\Entity
 */
class Status
{
    /**
     * @var boolean
     *
     * @ORM\Column(name="status_id", type="boolean", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $statusId;

    /**
     * @var string
     *
     * @ORM\Column(name="status_description", type="string", length=45, nullable=false)
     */
    private $statusDescription;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modified", type="datetime", nullable=false)
     */
    private $modified;


    /**
     * Get statusId
     *
     * @return boolean
     */
    public function getStatusId()
    {
        return $this->statusId;
    }

    /**
     * Set statusDescription
     *
     * @param string $statusDescription
     * @return Status
     */
    public function setStatusDescription($statusDescription = 'Default')
    {
        $this->statusDescription = $statusDescription;

        return $this;
    }

    /**
     * Get statusDescription
     *
     * @return string
     */
    public function getStatusDescription()
    {
        return $this->statusDescription;
    }

    /**
     * Set modified
     *
     * @param \DateTime $modified
     * @return Status
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
     * To string magic method
     * @return string
     */
    public function __toString()
    {
        return ucfirst($this->description);
    }
}