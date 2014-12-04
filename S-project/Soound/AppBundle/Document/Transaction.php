<?php
namespace Soound\AppBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;

/**
 * @MongoDB\Document(collection="transactions")
 */
class Transaction
{
 
    /**
     * @MongoDB\Id(strategy="AUTO")
     */
    private $transactionID;

    /**
     * @MongoDB\ReferenceMany(targetDocument="User", inversedBy="allTransactions")
     */ 
    protected $users;

    /**
     * @MongoDB\ReferenceOne(targetDocument="User", inversedBy="incomingTransactions")
     */
    protected $toUser;

    /**
     * @MongoDB\ReferenceOne(targetDocument="User", inversedBy="outgoingTransactions")
     */
    protected $fromUser;

    /**
     * @MongoDB\ReferenceOne(targetDocument="Project")
     */
    protected $project;

    /**
     * @MongoDB\Int
     */
    protected $amount;

    /**
     * @MongoDB\Date
     */
    protected $date;


    /**
     * Get transactionID
     *
     * @return id $transactionID
     */
    public function getTransactionID()
    {
        return $this->transactionID;
    }

    /**
     * Set toUser
     *
     * @param Soound\AppBundle\Document\User $toUser
     * @return self
     */
    public function setToUser(\Soound\AppBundle\Document\User $toUser)
    {
        $this->toUser = $toUser;
        return $this;
    }

    /**
     * Get toUser
     *
     * @return Soound\AppBundle\Document\User $toUser
     */
    public function getToUser()
    {
        return $this->toUser;
    }

    /**
     * Set fromUser
     *
     * @param Soound\AppBundle\Document\User $fromUser
     * @return self
     */
    public function setFromUser(\Soound\AppBundle\Document\User $fromUser)
    {
        $this->fromUser = $fromUser;
        return $this;
    }

    /**
     * Get fromUser
     *
     * @return Soound\AppBundle\Document\User $fromUser
     */
    public function getFromUser()
    {
        return $this->fromUser;
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
    public function __construct()
    {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add user
     *
     * @param Soound\AppBundle\Document\User $user
     */
    public function addUser(\Soound\AppBundle\Document\User $user)
    {
        $this->users[] = $user;
    }

    /**
     * Remove user
     *
     * @param Soound\AppBundle\Document\User $user
     */
    public function removeUser(\Soound\AppBundle\Document\User $user)
    {
        $this->users->removeElement($user);
    }

    /**
     * Get users
     *
     * @return Doctrine\Common\Collections\Collection $users
     */
    public function getUsers()
    {
        return $this->users;
    }
}
