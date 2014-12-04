<?php

namespace Cittando\SiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Category
 *
 * @ORM\Table(name="category")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Category
{
    /**
     * @var integer
     *
     * @ORM\Column(name="category_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="category_name", type="string", length=45, nullable=true)
     */
    protected $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modified", type="datetime", nullable=false)
     */
    protected $modified;

    /**
     * @var \CategoryType
     *
     * @ORM\ManyToOne(targetEntity="CategoryType")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="category_type_category_type_id", referencedColumnName="category_type_id")
     * })
     */
    protected $type;

    /**
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->modified = new \DateTime('now');
    }

    /**
     * Get Id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Category
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set modified
     *
     * @param \DateTime $modified
     * @return Category
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
     * Set type
     *
     * @param \Cittando\SiteBundle\Entity\CategoryType $type
     * @return Category
     */
    public function setType(\Cittando\SiteBundle\Entity\CategoryType $type = null)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return \Cittando\SiteBundle\Entity\CategoryType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * To string magic method
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }
}