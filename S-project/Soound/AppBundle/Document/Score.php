<?php
namespace Soound\AppBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;

/**
 * @MongoDB\EmbeddedDocument
 */
class Score
{
 
    /**
     * @MongoDB\Id(strategy="AUTO")
     */
    private $scoreID;

    /**
     * @MongoDB\ReferenceOne(targetDocument="Project", simple="true")
     */
    protected $project;

    /**
     * @MongoDB\Int
     */
    protected $amount = 0;

    /**
     * Get scoreID
     *
     * @return id $scoreID
     */
    public function getScoreID()
    {
        return $this->scoreID;
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
     * Set amount
     *
     * @param int $amount
     * @return self
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * Get amount
     *
     * @return int $amount
     */
    public function getAmount()
    {
        return $this->amount;
    }
}
