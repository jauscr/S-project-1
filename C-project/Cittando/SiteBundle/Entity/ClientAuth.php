<?php

namespace Cittando\SiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ClientAuth
 *
 * @ORM\Table(name="client_auth")
 * @ORM\Entity
 */
class ClientAuth
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="random_id", type="string", length=255, nullable=false)
     */
    private $randomId;

    /**
     * @var array
     *
     * @ORM\Column(name="redirect_uris", type="array", nullable=false)
     */
    private $redirectUris;

    /**
     * @var string
     *
     * @ORM\Column(name="secret", type="string", length=255, nullable=false)
     */
    private $secret;

    /**
     * @var array
     *
     * @ORM\Column(name="allowed_grant_types", type="array", nullable=false)
     */
    private $allowedGrantTypes;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=245, nullable=false)
     */
    private $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modified", type="datetime", nullable=false)
     */
    private $modified;



    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set randomId
     *
     * @param string $randomId
     * @return ClientAuth
     */
    public function setRandomId($randomId)
    {
        $this->randomId = $randomId;
    
        return $this;
    }

    /**
     * Get randomId
     *
     * @return string 
     */
    public function getRandomId()
    {
        return $this->randomId;
    }

    /**
     * Set redirectUris
     *
     * @param array $redirectUris
     * @return ClientAuth
     */
    public function setRedirectUris($redirectUris)
    {
        $this->redirectUris = $redirectUris;
    
        return $this;
    }

    /**
     * Get redirectUris
     *
     * @return array 
     */
    public function getRedirectUris()
    {
        return $this->redirectUris;
    }

    /**
     * Set secret
     *
     * @param string $secret
     * @return ClientAuth
     */
    public function setSecret($secret)
    {
        $this->secret = $secret;
    
        return $this;
    }

    /**
     * Get secret
     *
     * @return string 
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * Set allowedGrantTypes
     *
     * @param array $allowedGrantTypes
     * @return ClientAuth
     */
    public function setAllowedGrantTypes($allowedGrantTypes)
    {
        $this->allowedGrantTypes = $allowedGrantTypes;
    
        return $this;
    }

    /**
     * Get allowedGrantTypes
     *
     * @return array 
     */
    public function getAllowedGrantTypes()
    {
        return $this->allowedGrantTypes;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return ClientAuth
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
     * @return ClientAuth
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
}