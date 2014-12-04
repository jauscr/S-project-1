<?php
namespace Soound\AppBundle\Document;

use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
//use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
//use Doctrine\ORM\Mapping\PostPersist;

/**
 * @MongoDB\Document(collection="fos_user")
 */
class User extends BaseUser
{
    /**
     * @MongoDB\Id(strategy="auto")
     */
    protected $id;

    /**
     * @MongoDB\String
     */
    protected $publicId;

    /**
     * @MongoDB\Boolean
     */
    protected $onBraintree = false;

    /**
     * @MongoDB\Boolean
     */
    protected $confirmed = false;    

    /**
     * @MongoDB\Boolean
     */
    protected $paypalVerified = false;

    /**
     * @MongoDB\String
     */
    protected $paypalFirstName;

    /**
     * @MongoDB\String
     */
    protected $paypalLastName;

    /**
     * @MongoDB\String
     */
    protected $paypalEmail;

    /**
     * @MongoDB\Boolean
     */
    protected $subMerchantApproved = false;

    /**
     * @MongoDB\String
     */
    protected $facebook_id;

    /**
     * @MongoDB\String
     */
    protected $facebook_access_token;

    /**
     * @MongoDB\String
     */
    protected $google_id;

    /**
     * @MongoDB\String
     */
    protected $google_access_token;

    /**
     * @MongoDB\Boolean
     */
    protected $isActive;

    /**
     * @MongoDB\String
     */
    protected $fullname;

    /**
     * @MongoDB\String
     */
    protected $pictureExt;

    /**
     * @MongoDB\String
     */
    protected $pictureName;

    /**
     * @MongoDB\String
     */
    protected $location;

    /**
     * @MongoDB\Collection
     */
    protected $userTypes = array();

    /**
     * @MongoDB\ReferenceMany(targetDocument="Submission", mappedBy="user", sort={"lastUpdated"="desc"})
     */
    protected $submissions;

    /**
     * @MongoDB\ReferenceMany(targetDocument="Project", mappedBy="user", sort={"publishedOn"="desc"})
     */
    protected $ownedProjects;

    /**
     * @MongoDB\ReferenceMany(targetDocument="Project", mappedBy="followers")
     */
    protected $followingProjects;

    /**
     * @MongoDB\ReferenceMany(targetDocument="Project", mappedBy="members")
     */
    protected $memberOfProjects;

    /**
     * @MongoDB\ReferenceMany(targetDocument="Project", mappedBy="team")
     */
    protected $teamMemberOfProjects;

    /**
     * @MongoDB\ReferenceMany(targetDocument="Project", mappedBy="winner")
     */
    protected $won;

    /**
     * @MongoDB\ReferenceMany(targetDocument="Rating", mappedBy="user")
     */
    protected $ratings;

    /**
     * @MongoDB\ReferenceMany(
     *   targetDocument="Activity",
     *   mappedBy="user",
     *   sort={"date"="desc"})
     */
    protected $activity;

    /**
     * @MongoDB\ReferenceMany(
     *  targetDocument="Activity",
     *  mappedBy="user",
     *  sort={"date"="desc"},
     *  criteria={"read": false})
     */
    protected $unreadActivity;

    /**
     * @MongoDB\ReferenceMany(
     *  targetDocument="Activity",
     *  mappedBy="user",
     *  sort={"date"="desc"},
     *  limit=5)
     */
    protected $recentActivity;

    /**
     * @MongoDB\ReferenceMany(
     *   targetDocument="Credit",
     *   mappedBy="user",
     *   criteria={"sooundCredit" : false},
     *   sort={"date"="desc"})
     */
    protected $uploadedCredits;

    /**
     * @MongoDB\ReferenceMany(
     *   targetDocument="Credit",
     *   mappedBy="user",
     *   criteria={"sooundCredit" : true},
     *   sort={"date"="desc"})
     */
    protected $sooundCredits;

    /**
     * @MongoDB\Float
     */
    protected $totalEarned = 0;

    /**
     * @MongoDB\Float
     */
    protected $totalSpent = 0;

    /**
     * @MongoDB\ReferenceMany(targetDocument="Transaction", mappedBy="users", sort={"date"="desc"})
     */
    protected $allTransactions;

    /**
     * @MongoDB\ReferenceMany(targetDocument="Transaction", mappedBy="fromUser", sort={"date"="desc"})
     */
    protected $outgoingTransactions;

    /**
     * @MongoDB\ReferenceMany(targetDocument="Transaction", mappedBy="toUser", sort={"date"="desc"})
     */
    protected $incomingTransactions;

    /**
     * Erases the credential information
     * This is for? this function erase the FOS password
     * when user login or logout =(
     */
    /* public function eraseCredentials ()
    {
        $this->password = null;
    } */

    /**
     * @MongoDB\String
     */
    protected $userPic;

    /**
     * @MongoDB\String
     */
    protected $username;

    /**
     * @MongoDB\String
     */
    protected $email;

    /**
     * @MongoDB\String
     */
    protected $url;

    /**
     * @MongoDB\Hash
     */
    protected $notificationPreferences = array(
        'owner-new-submission-revision' => true,
        'owner-new-comment' => true, 
        'owner-withdrawn-submission-revision' => true, 
        'creative-submission-accepted-rejected' => true,
        'user-accepted-rejected-project-invite' => true,
        'user-accepted-rejected-team-invite' => true,
    );

    /**
     * @MongoDB\Hash
     */
    protected $walkthroughs = array(
        'profile' => false
    );

    /**
     * @MongoDB\EmbedMany(targetDocument="CreditCard")
     */
    protected $storedCards;

    /**
     * Verifies if given user equals the current user
     *
     * @param mixed $user
     * @return Boolean
     */
    public function equals (UserInterface $user)
    {
        return ($this->getPublicId() === $user->getPublicId());
    }

    public function getPicture($dim=180){
        return $this->pictureExt ? 'Users/'.$this->publicId. '/images/' . $this->pictureName . '-' . $dim. '.' . $this->pictureExt : 'Users/default.png';
    }

    public function getPictureExt()
    {
        return $this->pictureExt;
    }

    public function setPictureExt($ext)
    {
        $this->pictureExt = $ext;
    }

    public function getPictureName()
    {
        return $this->pictureName;
    }

    public function setPictureName($name)
    {
        $this->pictureName = $name;
    }

    public function getSubmissions()
    {
        return $this->submissions;
    }

    public function __construct()
    {
        parent::__construct();
        $this->publicId = uniqid( crc32(mt_rand()), false);
        $this->roles = array('ROLE_USER');
    }

    /**
     * Get id
     *
     * @return id $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Runs through a list of users and checks if this user is in it
     *
     * @return Boolean $inGroup
     */
    public function inGroup($group)
    {
        foreach ($group as $user) {
            if($user->getId() === $this->id)
                return true;
        }
        return false;
    }

    /**
     * Set facebook_id
     *
     * @param string $facebook_id
     * @return self
     */
    public function setFacebookId($facebook_id)
    {
        $this->facebook_id = $facebook_id;
        return $this;
    }

    /**
     * Get facebook_id
     *
     * @return string $facebook_id
     */
    public function getFacebookId()
    {
        return $this->facebook_id;
    }

    /**
     * Set facebook_access_token
     *
     * @param string $facebook_access_token
     * @return self
     */
    public function setFacebookAccessToken($facebook_access_token)
    {
        $this->facebook_access_token = $facebook_access_token;
        return $this;
    }

    /**
     * Get facebook_access_token
     *
     * @return string $facebook_access_token
     */
    public function getFacebookAccessToken()
    {
        return $this->facebook_access_token;
    }

    /**
     * Set google_id
     *
     * @param string $google_id
     * @return self
     */
    public function setGoogleId($google_id)
    {
        $this->google_id = $google_id;
        return $this;
    }

    /**
     * Get google_id
     *
     * @return string $google_id
     */
    public function getGoogleId()
    {
        return $this->google_id;
    }

    /**
     * Set google_access_token
     *
     * @param string $google_access_token
     * @return self
     */
    public function setGoogleAccessToken($google_access_token)
    {
        $this->google_access_token = $google_access_token;
        return $this;
    }

    /**
     * Get google_access_token
     *
     * @return string $google_access_token
     */
    public function getGoogleAccessToken()
    {
        return $this->google_access_token;
    }

    /**
     * Set fullname
     *
     * @param string $fullname
     * @return self
     */
    public function setFullname($fullname)
    {
        $this->fullname = $fullname;
        return $this;
    }

    /**
     * Get fullname
     *
     * @return string $fullname
     */
    public function getFullname($first = false)
    {
        if($first){
            $name = explode(" ", $this->fullname);
            unset($name[sizeof($name)-1]);
            return implode(" ", $name);
        }
        return $this->fullname;
    }

    /**
     * Add ownedProject
     *
     * @param Soound\AppBundle\Document\Project $ownedProject
     */
    public function addOwnedProject(\Soound\AppBundle\Document\Project $ownedProject)
    {
        $this->ownedProjects[] = $ownedProject;
    }

    /**
     * Remove ownedProject
     *
     * @param Soound\AppBundle\Document\Project $ownedProject
     */
    public function removeOwnedProject(\Soound\AppBundle\Document\Project $ownedProject)
    {
        $this->ownedProjects->removeElement($ownedProject);
    }

    /**
     * Get ownedProjects
     *
     * @return Doctrine\Common\Collections\Collection $ownedProjects
     */
    public function getOwnedProjects()
    {
        return $this->ownedProjects;
    }

    /**
     * Add followingProject
     *
     * @param Soound\AppBundle\Document\Project $followingProject
     */
    public function addFollowingProject(\Soound\AppBundle\Document\Project $followingProject)
    {
        $this->followingProjects[] = $followingProject;
    }

    /**
     * Remove followingProject
     *
     * @param Soound\AppBundle\Document\Project $followingProject
     */
    public function removeFollowingProject(\Soound\AppBundle\Document\Project $followingProject)
    {
        $this->followingProjects->removeElement($followingProject);
    }

    /**
     * Get followingProjects
     *
     * @return Doctrine\Common\Collections\Collection $followingProjects
     */
    public function getFollowingProjects()
    {
        return $this->followingProjects;
    }

    /**
     * Add memberOfProject
     *
     * @param Soound\AppBundle\Document\Project $memberOfProject
     */
    public function addMemberOfProject(\Soound\AppBundle\Document\Project $memberOfProject)
    {
        $this->memberOfProjects[] = $memberOfProject;
    }

    /**
     * Remove memberOfProject
     *
     * @param Soound\AppBundle\Document\Project $memberOfProject
     */
    public function removeMemberOfProject(\Soound\AppBundle\Document\Project $memberOfProject)
    {
        $this->memberOfProjects->removeElement($memberOfProject);
    }

    /**
     * Get memberOfProjects
     *
     * @return Doctrine\Common\Collections\Collection $memberOfProjects
     */
    public function getMemberOfProjects()
    {
        return $this->memberOfProjects;
    }

    /**
     * @return Boolean $owns
     */
    public function ownsProject($projectID)
    {
        foreach ($this->ownedProjects as $project) {
            if( $project->getProjectId() === $projectID )
                return true;
        }
        return false;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return self
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean $isActive
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Add submission
     *
     * @param Soound\AppBundle\Document\Submission $submission
     */
    public function addSubmission(\Soound\AppBundle\Document\Submission $submission)
    {
        $this->submissions[] = $submission;
    }

    /**
     * Remove submission
     *
     * @param Soound\AppBundle\Document\Submission $submission
     */
    public function removeSubmission(\Soound\AppBundle\Document\Submission $submission)
    {
        $this->submissions->removeElement($submission);
    }

    /**
     * Add rating
     *
     * @param Soound\AppBundle\Document\Rating $rating
     */
    public function addRating(\Soound\AppBundle\Document\Rating $rating)
    {
        $this->ratings[] = $rating;
    }

    /**
     * Remove rating
     *
     * @param Soound\AppBundle\Document\Rating $rating
     */
    public function removeRating(\Soound\AppBundle\Document\Rating $rating)
    {
        $this->ratings->removeElement($rating);
    }

    /**
     * Get ratings
     *
     * @return Doctrine\Common\Collections\Collection $ratings
     */
    public function getRatings()
    {
        return $this->ratings;
    }

    /**
     * Set location
     *
     * @param string $location
     * @return self
     */
    public function setLocation($location)
    {
        $this->location = $location;
        return $this;
    }

    /**
     * Get location
     *
     * @return string $location
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set userTypes
     *
     * @param collection $userTypes
     * @return self
     */
    public function setUserTypes($userTypes)
    {
        $this->userTypes = $userTypes;
        return $this;
    }

    /**
     * Get userTypes
     *
     * @return collection $userTypes
     */
    public function getUserTypes()
    {
        return $this->userTypes;
    }

    /**
     * Add uploadedCredit
     *
     * @param Soound\AppBundle\Document\Credit $uploadedCredit
     */
    public function addUploadedCredit(\Soound\AppBundle\Document\Credit $uploadedCredit)
    {
        $this->uploadedCredits[] = $uploadedCredit;
    }

    /**
     * Remove uploadedCredit
     *
     * @param Soound\AppBundle\Document\Credit $uploadedCredit
     */
    public function removeUploadedCredit(\Soound\AppBundle\Document\Credit $uploadedCredit)
    {
        $this->uploadedCredits->removeElement($uploadedCredit);
    }

    /**
     * Get uploadedCredits
     *
     * @return Doctrine\Common\Collections\Collection $uploadedCredits
     */
    public function getUploadedCredits()
    {
        return $this->uploadedCredits;
    }

    /**
     * Add sooundCredit
     *
     * @param Soound\AppBundle\Document\Credit $sooundCredit
     */
    public function addSooundCredit(\Soound\AppBundle\Document\Credit $sooundCredit)
    {
        $this->sooundCredits[] = $sooundCredit;
    }

    /**
     * Remove sooundCredit
     *
     * @param Soound\AppBundle\Document\Credit $sooundCredit
     */
    public function removeSooundCredit(\Soound\AppBundle\Document\Credit $sooundCredit)
    {
        $this->sooundCredits->removeElement($sooundCredit);
    }

    /**
     * Get sooundCredits
     *
     * @return Doctrine\Common\Collections\Collection $sooundCredits
     */
    public function getSooundCredits()
    {
        return $this->sooundCredits;
    }

    /**
     * Add won
     *
     * @param Soound\AppBundle\Document\Project $won
     */
    public function addWon(\Soound\AppBundle\Document\Project $won)
    {
        $this->won[] = $won;
    }

    /**
     * Remove won
     *
     * @param Soound\AppBundle\Document\Project $won
     */
    public function removeWon(\Soound\AppBundle\Document\Project $won)
    {
        $this->won->removeElement($won);
    }

    /**
     * Get won
     *
     * @return Doctrine\Common\Collections\Collection $won
     */
    public function getWon()
    {
        return $this->won;
    }

    /**
     * Add outgoingTransaction
     *
     * @param Soound\AppBundle\Document\Transaction $outgoingTransaction
     */
    public function addOutgoingTransaction(\Soound\AppBundle\Document\Transaction $outgoingTransaction)
    {
        $this->outgoingTransactions[] = $outgoingTransaction;
    }

    /**
     * Remove outgoingTransaction
     *
     * @param Soound\AppBundle\Document\Transaction $outgoingTransaction
     */
    public function removeOutgoingTransaction(\Soound\AppBundle\Document\Transaction $outgoingTransaction)
    {
        $this->outgoingTransactions->removeElement($outgoingTransaction);
    }

    /**
     * Get outgoingTransactions
     *
     * @return Doctrine\Common\Collections\Collection $outgoingTransactions
     */
    public function getOutgoingTransactions()
    {
        return $this->outgoingTransactions;
    }

    /**
     * Add incomingTransaction
     *
     * @param Soound\AppBundle\Document\Transaction $incomingTransaction
     */
    public function addIncomingTransaction(\Soound\AppBundle\Document\Transaction $incomingTransaction)
    {
        $this->incomingTransactions[] = $incomingTransaction;
    }

    /**
     * Remove incomingTransaction
     *
     * @param Soound\AppBundle\Document\Transaction $incomingTransaction
     */
    public function removeIncomingTransaction(\Soound\AppBundle\Document\Transaction $incomingTransaction)
    {
        $this->incomingTransactions->removeElement($incomingTransaction);
    }

    /**
     * Get incomingTransactions
     *
     * @return Doctrine\Common\Collections\Collection $incomingTransactions
     */
    public function getIncomingTransactions()
    {
        return $this->incomingTransactions;
    }

    /**
     * Add allTransaction
     *
     * @param Soound\AppBundle\Document\Transaction $allTransaction
     */
    public function addAllTransaction(\Soound\AppBundle\Document\Transaction $allTransaction)
    {
        $this->allTransactions[] = $allTransaction;
    }

    /**
     * Remove allTransaction
     *
     * @param Soound\AppBundle\Document\Transaction $allTransaction
     */
    public function removeAllTransaction(\Soound\AppBundle\Document\Transaction $allTransaction)
    {
        $this->allTransactions->removeElement($allTransaction);
    }

    /**
     * Get allTransactions
     *
     * @return Doctrine\Common\Collections\Collection $allTransactions
     */
    public function getAllTransactions()
    {
        return $this->allTransactions;
    }

    /**
     * Set totalEarned
     *
     * @param float $totalEarned
     * @return self
     */
    public function setTotalEarned($totalEarned)
    {
        $this->totalEarned = $totalEarned;
        return $this;
    }

    /**
     * Get totalEarned
     *
     * @return float $totalEarned
     */
    public function getTotalEarned()
    {
        return $this->totalEarned;
    }

    /**
     * Add to totalEarned
     */
    public function addTotalEarned($amount)
    {
        $this->totalEarned += $amount;
    }

    /**
     * Set totalSpent
     *
     * @param float $totalSpent
     * @return self
     */
    public function setTotalSpent($totalSpent)
    {
        $this->totalSpent = $totalSpent;
        return $this;
    }

    /**
     * Get totalSpent
     *
     * @return float $totalSpent
     */
    public function getTotalSpent()
    {
        return $this->totalSpent;
    }

    /**
     * Add to totalSpent
     */
    public function addTotalSpent($amount)
    {
        $this->totalSpent += $amount;
    }

    public function getUserPic(){
        return $this->userPic;
    }

    public function setUserPic($userPic){
        $this->userPic = $userPic;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return self
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * Get username
     *
     * @return string $username
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Add activity
     *
     * @param Soound\AppBundle\Document\Activity $activity
     */
    public function addActivity(\Soound\AppBundle\Document\Activity $activity)
    {
        $this->activity[] = $activity;
    }

    /**
     * Remove activity
     *
     * @param Soound\AppBundle\Document\Activity $activity
     */
    public function removeActivity(\Soound\AppBundle\Document\Activity $activity)
    {
        $this->activity->removeElement($activity);
    }

    /**
     * Get activity
     *
     * @return Doctrine\Common\Collections\Collection $activity
     */
    public function getActivity()
    {
        return $this->activity;
    }

    /**
     * Set publicId
     *
     * @param string $publicId
     * @return self
     */
    public function setPublicId($publicId)
    {
        $this->publicId = $publicId;
        return $this;
    }

    /**
     * Get publicId
     *
     * @return string $publicId
     */
    public function getPublicId()
    {
        return $this->publicId;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return self
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Get email
     *
     * @return string $email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set paypalEmail
     *
     * @param string $paypalEmail
     * @return self
     */
    public function setPaypalEmail($paypalEmail)
    {
        $this->paypalEmail = $paypalEmail;
        return $this;
    }

    /**
     * Get paypalEmail
     *
     * @return string $paypalEmail
     */
    public function getPaypalEmail()
    {
        return $this->paypalEmail;
    }


    /**
     * Set paypalFirstName
     *
     * @param string $paypalFirstName
     * @return self
     */
    public function setPaypalFirstName($paypalFirstName)
    {
        $this->paypalFirstName = $paypalFirstName;
        return $this;
    }

    /**
     * Get paypalFirstName
     *
     * @return string $paypalFirstName
     */
    public function getPaypalFirstName()
    {
        return $this->paypalFirstName;
    }

    /**
     * Set paypalLastName
     *
     * @param string $paypalLastName
     * @return self
     */
    public function setPaypalLastName($paypalLastName)
    {
        $this->paypalLastName = $paypalLastName;
        return $this;
    }

    /**
     * Get paypalLastName
     *
     * @return string $paypalLastName
     */
    public function getPaypalLastName()
    {
        return $this->paypalLastName;
    }

    /**
     * Set paypalVerified
     *
     * @param boolean $paypalVerified
     * @return self
     */
    public function setPaypalVerified($paypalVerified)
    {
        $this->paypalVerified = $paypalVerified;
        return $this;
    }

    /**
     * Get paypalVerified
     *
     * @return boolean $paypalVerified
     */
    public function getPaypalVerified()
    {
        return $this->paypalVerified;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return self
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * Get url
     *
     * @return string $url
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set notificationPreferences
     *
     * @param collection $notificationPreferences
     * @return self
     */
    public function setNotificationPreferences($notificationPreferences)
    {
        $this->notificationPreferences = $notificationPreferences;
        return $this;
    }

    /**
     * Get notificationPreferences
     *
     * @return collection $notificationPreferences
     */
    public function getNotificationPreferences()
    {
        return $this->notificationPreferences;
    }

    /**
     * Add unreadNotification
     *
     * @param Soound\AppBundle\Document\Activity $unreadNotification
     */
    public function addUnreadNotification(\Soound\AppBundle\Document\Activity $unreadNotification)
    {
        $this->unreadNotifications[] = $unreadNotification;
    }

    /**
     * Remove unreadNotification
     *
     * @param Soound\AppBundle\Document\Activity $unreadNotification
     */
    public function removeUnreadNotification(\Soound\AppBundle\Document\Activity $unreadNotification)
    {
        $this->unreadNotifications->removeElement($unreadNotification);
    }

    /**
     * Get unreadNotifications
     *
     * @return Doctrine\Common\Collections\Collection $unreadNotifications
     */
    public function getUnreadNotifications()
    {
        return $this->unreadNotifications;
    }

    /**
     * Add unreadActivity
     *
     * @param Soound\AppBundle\Document\Activity $unreadActivity
     */
    public function addUnreadActivity(\Soound\AppBundle\Document\Activity $unreadActivity)
    {
        $this->unreadActivity[] = $unreadActivity;
    }

    /**
     * Remove unreadActivity
     *
     * @param Soound\AppBundle\Document\Activity $unreadActivity
     */
    public function removeUnreadActivity(\Soound\AppBundle\Document\Activity $unreadActivity)
    {
        $this->unreadActivity->removeElement($unreadActivity);
    }

    /**
     * Get unreadActivity
     *
     * @return Doctrine\Common\Collections\Collection $unreadActivity
     */
    public function getUnreadActivity()
    {
        return $this->unreadActivity;
    }

    /**
     * Add teamMemberOfProject
     *
     * @param Soound\AppBundle\Document\Project $teamMemberOfProject
     */
    public function addTeamMemberOfProject(\Soound\AppBundle\Document\Project $teamMemberOfProject)
    {
        $this->teamMemberOfProjects[] = $teamMemberOfProject;
    }

    /**
     * Remove teamMemberOfProject
     *
     * @param Soound\AppBundle\Document\Project $teamMemberOfProject
     */
    public function removeTeamMemberOfProject(\Soound\AppBundle\Document\Project $teamMemberOfProject)
    {
        $this->teamMemberOfProjects->removeElement($teamMemberOfProject);
    }

    /**
     * Get teamMemberOfProjects
     *
     * @return Doctrine\Common\Collections\Collection $teamMemberOfProjects
     */
    public function getTeamMemberOfProjects()
    {
        return $this->teamMemberOfProjects;
    }

    /**
     * Set subMerchantApproved
     *
     * @param boolean $subMerchantApproved
     * @return self
     */
    public function setSubMerchantApproved($subMerchantApproved)
    {
        $this->subMerchantApproved = $subMerchantApproved;
        return $this;
    }

    /**
     * Get subMerchantApproved
     *
     * @return boolean $subMerchantApproved
     */
    public function getSubMerchantApproved()
    {
        return $this->subMerchantApproved;
    }

    /**
     * Add storedCard
     *
     * @param Soound\AppBundle\Document\CreditCard $storedCard
     */
    public function addStoredCard(\Soound\AppBundle\Document\CreditCard $storedCard)
    {
        $this->storedCards[] = $storedCard;
    }

    /**
     * Remove storedCard
     *
     * @param Soound\AppBundle\Document\CreditCard $storedCard
     */
    public function removeStoredCard(\Soound\AppBundle\Document\CreditCard $storedCard)
    {
        $this->storedCards->removeElement($storedCard);
    }

    /**
     * Get storedCard by id 
     * 
     * @param string $id, or false on failure
     * @return Soound\AppBundle\Document\CreditCard $storedCard
     */
    public function getStoredCard($id)
    {  
        foreach ($this->storedCards as $card) {
            if( $card->getId() === $id )
                return $card;
        }
        return false;
    }

    /**
     * Get storedCards
     *
     * @return Doctrine\Common\Collections\Collection $storedCards
     */
    public function getStoredCards()
    {
        return $this->storedCards;
    }

    /**
     * Set onBraintree
     *
     * @param boolean $onBraintree
     * @return self
     */
    public function setOnBraintree($onBraintree)
    {
        $this->onBraintree = $onBraintree;
        return $this;
    }

    /**
     * Get onBraintree
     *
     * @return boolean $onBraintree
     */
    public function getOnBraintree()
    {
        return $this->onBraintree;
    }

    /**
     * Set confirmed
     *
     * @param boolean $confirmed
     * @return self
     */
    public function setConfirmed($confirmed)
    {
        $this->confirmed = $confirmed;
        return $this;
    }

    /**
     * Get confirmed
     *
     * @return boolean $confirmed
     */
    public function getConfirmed()
    {
        return $this->confirmed;
    }

    /**
     * Inverts the requested notificaiton preference
     *
     * @param string $activityType
     */
    public function changeNotificationPreference($activityType)
    {
        if( array_key_exists($activityType, $this->notificationPreferences)){
            if($this->notificationPreferences[$activityType])
                $this->notificationPreferences[$activityType] = false;
            else
                $this->notificationPreferences[$activityType] = true;
            //$this->notificationPreferences[$activityType] = !$this->notificationPreferences[$activityType];
        }
    }


    /**
     * Does the user want to be sent an email for this kind of activity
     *
     * @param string $activityType
     * @return Boolean $preference
     */
    public function wantsEmail($activityType)
    {
        if( array_key_exists($activityType, $this->notificationPreferences))
            return $this->notificationPreferences[$activityType];
        else
            return false;
    }

    /**
     * Add recentActivity
     *
     * @param Soound\AppBundle\Document\Activity $recentActivity
     */
    public function addRecentActivity(\Soound\AppBundle\Document\Activity $recentActivity)
    {
        $this->recentActivity[] = $recentActivity;
    }

    /**
     * Remove recentActivity
     *
     * @param Soound\AppBundle\Document\Activity $recentActivity
     */
    public function removeRecentActivity(\Soound\AppBundle\Document\Activity $recentActivity)
    {
        $this->recentActivity->removeElement($recentActivity);
    }

    /**
     * Get recentActivity
     *
     * @return Doctrine\Common\Collections\Collection $recentActivity
     */
    public function getRecentActivity()
    {
        return $this->recentActivity;
    }

    /**
     * Has the user seen the walkthrough for this page?
     *
     * @param string $page
     * @return Boolean $hasSeen
     */
    public function hasSeenWalkthrough($page)
    {
        if( array_key_exists($page, $this->walkthroughs))
            return $this->walkthroughs[$page];
        else
            return true;
    }
}
