<?php
namespace Soound\AppBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @MongoDB\Document(collection="projects")
 */
class Project
{

    /**
     * @MongoDB\Id(strategy="AUTO")
     */
    private $projectId;

    /**
     * @MongoDB\String
     */
    private $publicId;

    /**
     * @MongoDB\String
     */
    private $transactionId;

    /**
     * @MongoDB\Boolean
     */
    private $payed = false;

    /**
     * @MongoDB\String
     */
    private $teamInviteKey;

    /**
     * @MongoDB\ReferenceOne(targetDocument="User", simple="true")
     */
    protected $user;

    /**
     * @MongoDB\ReferenceOne(targetDocument="Submission")
     */
    protected $winner;

    /**
     * @MongoDB\String
     */
    protected $size;

    /**
     * @MongoDB\String
     */
    protected $projectname;

    /**
     * @MongoDB\Collection
     */
    protected $projectgenre = array();

    /**
     * @MongoDB\String
     */
    protected $projectdetails;

    /**
     * @MongoDB\String
     */
    protected $projectchecktype;

    /**
     * @MongoDB\Date
     */
    protected $publishedOn;

    /**
     * @MongoDB\Date
     */
    protected $dueDate;

    /**
     * @MongoDB\Date
     */
    protected $endingOn;

    /**
     * @MongoDB\ReferenceMany(targetDocument="User", inversedBy="followingProjects")
     */
    protected $followers;

    /**
     * @MongoDB\ReferenceMany(targetDocument="User", inversedBy="memberOfProjects")
     */
    protected $members;

    /**
     * @MongoDB\ReferenceMany(targetDocument="User", inversedBy="teamMemberOfProjects")
     */
    protected $team = array();

    /**
     * @MongoDB\Collection
     */
    protected $pendingTeam = array();

    /**
     * @MongoDB\Int
     */
    protected $followersCount = 0;

    /**
     * @MongoDB\String
     */
    protected $isFeatured;

    /**
     * @MongoDB\Int
     */
    protected $budget;

    /**
     * @MongoDB\ReferenceMany(targetDocument="Submission", mappedBy="project")
     */
    protected $submissions;

    /**
     * @MongoDB\String
     */
    protected $projectType;

    /**
     * @MongoDB\EmbedMany(targetDocument="ProjectReference")
     */
    protected $references;

    /**
     * @MongoDB\ReferenceMany(targetDocument="ProjectFile", mappedBy="project")
     */
    protected $files;

    // Pending to clean code
    // New variables

    /**
     * @MongoDB\String
     */
    protected $songTopic;

    /**
     * @MongoDB\String
     */
    protected $songMention;

    /**
     * @MongoDB\String
     */
    protected $musicianType;

    /**
     * @MongoDB\String
     */
    protected $musicianTech;

    /**
     * @MongoDB\String
     */
    protected $genderVocal;

    /**
     * @MongoDB\String
     */
    protected $vocalRange;

    /**
     * @MongoDB\Collection
     */
    protected $vocalLan;

    /**
     * @MongoDB\Float
     */
    protected $engTempo;

    /**
     * @MongoDB\String
     */
    protected $projecttempo;

    /**
     * @MongoDB\String
     */
    protected $keysong;

    /**
     * @MongoDB\String
     */
    protected $moodsong;

    /**
     * @MongoDB\String
     */
    protected $projectstyle;

    /**
     * @MongoDB\String
     */
    protected $drumspref;

    /**
     * @MongoDB\String
     */
    protected $instrumentRef;

    /**
     * @MongoDB\Collection
     */
    protected $dominantsound = array();

    /**
     * @MongoDB\Date
     */
    protected $deadLine;

    /**
     * @MongoDB\String
     */
    protected $payMethod;

    /**
     * @MongoDB\String
     */
    protected $projectPic;

    /**
     * @MongoDB\String
     */
    protected $projectExt;

    /**
     * @MongoDB\ReferenceMany(targetDocument="HQFile", mappedBy="project", sort={"date"="desc"})
     */
    protected $HQFiles;

    /**
     * @MongoDB\String
     */
    protected $complaint;

    /**
     * @MongoDB\Boolean
     */
    protected $complaintResolved = true;

    /**
     * @return Id $projectId
     */
    public function getId()
    {
        return $this->projectId;
    }

    /**
     * @param String $ext
     */
    public function setProjectPicExtension($ext)
    {
        $this->projectPicExt = $ext;
    }

    /**
     * @return String $projectPicExt
     */
    public function getProjectPicExtension()
    {
        return $this->projectPicExt;
    }

    /**
     * @param String $genre
     */
    public function removeProjectgenre($genre)
    {
        //$this->projectgenre->removeElement($genre);

        if( in_array($genre, $this->projectgenre) )
            array_splice( $this->projectgenre, array_search($genre, $this->projectgenre), 1);

    }

    /**
     * Get size
     *
     * @return string $size
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set Size
     *
     * @param string $Size
     */
    public function setSize($size)
    {
        $this->size = $size;
    }




    /**
     * Publish project (creates timestamp)
     *
     * @return boolean $success
     */
    public function publishProject()
    {
        if( $this->publishedOn ) //If already published
        return false;

        $this->publishedOn = new \DateTime(); //MongoDate
        return true;
    }

    /**
     * @return MongoDate $publishedOn
     */
    public function getPublishDate()
    {
        return $this->publishedOn;
    }

    /**
     * Set project due date (only if due date is within allowed date range)
     *
     * @param string $newDate
     * @return boolean $success
     */
    public function setDueDate($days)
    {
        if (is_object($days)) {
            return $this->endingOn = $days;
        }

        if($days > 30 || $days < 7)
            return false;

        $temp = clone $this->publishedOn;

        $this->endingOn = date_add($temp, new \DateInterval('P'.$days.'D') );
        return true;
    }

    /**
     * @return Date $endingOn
     */
    public function getDueDate()
    {
        return $this->endingOn;
    }

    /**
     * @return boolean $isFeatured
     */
    public function isFeatured()
    {
        return $this->isFeatured;
    }

    /**
     * @param boolean $featured
     */
    public function setFeatured($featured)
    {
        $this->isFeatured = $featured;
    }

    /**
     * @param int $budget
     */
    public function setBudget($budget)
    {
        if($budget >= 0) //Projects cannot have negative budget
        $this->budget = $budget;
    }

    /**
     * @return int budget
     */
    public function getBudget()
    {
        //If budget not yet set, return 0 as the budget
        return $this->budget ? $this->budget : 0;
    }

    /**
     * @return String $projectType
     */
    public function getProjectType($frontEnd = false)
    {
        if(!$frontEnd)
            return $this->projectType;
        else{
            $projectType = "";
            switch ($this->projectType) {
                case 'completesongs':
                    $projectType = 'Complete Song';
                    break;
                default:
                    $projectType = ucfirst($this->projectType);
                    break;
            }
            return $projectType;
        }
    }

    /**
     * @param string $projectType
     */
    public function setProjectType($projectType)
    {
        $this->projectType = $projectType;
    }

    /**
     * @return Int $entriesCount
     */
    public function getNumEntries()
    {
        return $this->numEntries;
    }

    /**
     * @param Int $numEntries
     */
    public function setNumEntries($numEntries)
    {
        if($numEntries >= 0) //Projects cannot have negative entries
        $this->numEntries = $numEntries;
    }

    /**
     * Add reference
     *
     * @param Soound\AppBundle\Document\ProjectReference $reference
     */
    public function addReference(\Soound\AppBundle\Document\ProjectReference $reference)
    {
        $this->references[] = $reference;
    }

    /**
     * Remove reference
     *
     * @param Soound\AppBundle\Document\ProjectReference $reference
     */
    public function removeReference(\Soound\AppBundle\Document\ProjectReference $reference)
    {
        $this->references->removeElement($reference);
    }

    /**
     * Get references
     *
     * @return Doctrine\Common\Collections\Collection $references
     */
    public function getReferences()
    {
        return $this->references;
    }

    /**
     * Add file
     *
     * @param Soound\AppBundle\Document\ProjectFile $file
     */
    public function addFile(\Soound\AppBundle\Document\ProjectFile $file)
    {
        $this->files[] = $file;
    }

    /**
     * Remove file
     *
     * @param Soound\AppBundle\Document\ProjectFile $file
     */
    public function removeFile(\Soound\AppBundle\Document\ProjectFile $file)
    {
        $this->files->removeElement($file);
    }

    /**
     * Get files
     *
     * @return Doctrine\Common\Collections\Collection $files
     */
    public function getfiles()
    {
        return $this->files;
    }
    // Get and set for new variables
    //*******************************************************
    /**
     * Set projecttempo
     *
     * @param string $projecttempo
     * @return self
     */
    public function setProjecttempo($projecttempo)
    {
        $this->projecttempo = $projecttempo;
        return $this;
    }

    /**
     * Get projecttempo
     *
     * @return string $projecttempo
     */
    public function getProjecttempo()
    {
        return $this->projecttempo;
    }

    /**
     * Set keysong
     *
     * @param string $keysong
     * @return self
     */
    public function setKeysong($keysong)
    {
        $this->keysong = $keysong;
        return $this;
    }

    /**
     * Get keysong
     *
     * @return string $keysong
     */
    public function getKeysong()
    {
        return $this->keysong;
    }

    /**
     * Set moodsong
     *
     * @param string $moodsong
     * @return self
     */
    public function setMoodsong($moodsong)
    {
        $this->moodsong = $moodsong;
        return $this;
    }

    /**
     * Get moodsong
     *
     * @return string $moodsong
     */
    public function getMoodsong()
    {
        return $this->moodsong;
    }

    /**
     * Set projectstyle
     *
     * @param string $projectstyle
     * @return self
     */
    public function setProjectstyle($projectstyle)
    {
        $this->projectstyle = $projectstyle;
        return $this;
    }

    /**
     * Get projectstyle
     *
     * @return string $projectstyle
     */
    public function getProjectstyle()
    {
        return $this->projectstyle;
    }

    /**
     * Set drumspref
     *
     * @param string $drumspref
     * @return self
     */
    public function setDrumspref($drumspref)
    {
        $this->drumspref = $drumspref;
        return $this;
    }

    /**
     * Get drumspref
     *
     * @return string $drumspref
     */
    public function getDrumspref()
    {
        return $this->drumspref;
    }

    /**
     * Set instrumentRef
     *
     * @param string $instrumentRef
     * @return self
     */
    public function setInstrumentRef($instrumentRef)
    {
        $this->instrumentRef = $instrumentRef;
        return $this;
    }

    /**
     * Get instrumentRef
     *
     * @return string $instrumentRef
     */
    public function getInstrumentRef()
    {
        return $this->instrumentRef;
    }

    /**
     * Set dominantsound
     *
     * @param collection $dominantsound
     * @return self
     */
    public function setDominantsound($dominantsound)
    {
        $this->dominantsound = $dominantsound;
        return $this;
    }

    /**
     * Get dominantsound
     *
     * @return collection $dominantsound
     */
    public function getDominantsound()
    {
        return $this->dominantsound;
    }

    public function getIsFeatured()
    {
        return $this->isFeatured;
    }

    /**
     * @param string $isFeatured
     */
    public function setIsFeatured($isFeatured)
    {
        $this->isFeatured = $isFeatured;
    }

    /**
     * Set payMethod
     *
     * @param string $payMethod
     * @return self
     */
    public function setPayMethod($payMethod)
    {
        $this->payMethod = $payMethod;
        return $this;
    }

    /**
     * Get payMethod
     *
     * @return string $payMethod
     */
    public function getPayMethod()
    {
        return $this->payMethod;
    }

    /**
     * Set project due date
     *
     * @param Date $deadLine
     * @return self
     */

    public function setDeadLine($deadLine)
    {
        $this->deadLine = $deadLine;
        return $this;
    }

    /**
     * @return Date $deadLine
     */
    public function getDeadLine()
    {
        return $this->deadLine;
    }

    /**
     * Set songTopic
     *
     * @param string $songTopic
     */
    public function setSongTopic($songTopic)
    {
        $this->songTopic = $songTopic;
    }

    /**
     * Get songTopic
     *
     * @return string $songTopic
     */
    public function getSongTopic()
    {
        return $this->songTopic;
    }

    /**
     * Set songMention
     *
     * @param string $songMention
     */
    public function setSongMention($songMention)
    {
        $this->songMention = $songMention;
    }

    /**
     * Get songMention
     *
     * @return string $songMention
     */
    public function getSongMention()
    {
        return $this->songMention;
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
     * Set musicianType
     *
     * @param string $musicianType
     */
    public function setMusicianType($musicianType)
    {
        $this->musicianType = $musicianType;
    }

    /**
     * Get musicianType
     *
     * @return string $musicianType
     */
    public function getMusicianType()
    {
        return $this->musicianType;
    }

    /**
     * Set genderVocal
     *
     * @param string $genderVocal
     */
    public function setGenderVocal($genderVocal)
    {
        $this->genderVocal = $genderVocal;
    }

    /**
     * Get genderVocal
     *
     * @return string $genderVocal
     */
    public function getGenderVocal()
    {
        return $this->genderVocal;
    }

    /**
     * Set vocalRange
     *
     * @param string $vocalRange
     */
    public function setVocalRange($vocalRange)
    {
        $this->vocalRange = $vocalRange;
    }

    /**
     * Get vocalRange
     *
     * @return string $vocalRange
     */
    public function getVocalRange()
    {
        return $this->vocalRange;
    }

    /**
     * @return Array $vocalLan
     */
    public function getVocalLan()
    {
        return $this->vocalLan;
    }

    /**
     * Set vocalLan
     *
     * @param string $vocalLan
     */
    public function setVocalLan($vocalLan)
    {
        $this->vocalLan = $vocalLan;
    }

    /**
     * Set engTempo
     *
     * @param Float $engTempo
     */
    public function setEngTempo($engTempo)
    {
        $this->engTempo = $engTempo;
    }

    /**
     * Get engTempo
     *
     * @return Float $engTempo
     */
    public function getEngTempo()
    {
        return $this->engTempo;
    }

    public function __construct()
    {
        //Generate publicId, with is a 24 character long string, made of three crc32 hashes of $projectId
        $this->publicId = uniqid( crc32(mt_rand()), false);
        $this->followers = new \Doctrine\Common\Collections\ArrayCollection();
        $this->members = new \Doctrine\Common\Collections\ArrayCollection();
        $this->submissions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->references = new \Doctrine\Common\Collections\ArrayCollection();
        $this->files = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get projectId
     *
     * @return id $projectId
     */
    public function getProjectId()
    {
        return $this->projectId;
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
     * Set projectname
     *
     * @param string $projectname
     * @return self
     */
    public function setProjectname($projectname)
    {
        $this->projectname = $projectname;
        return $this;
    }

    /**
     * Get projectname
     *
     * @return string $projectname
     */
    public function getProjectname()
    {
        return $this->projectname;
    }

    /**
     * Set projectdetails
     *
     * @param string $projectdetails
     * @return self
     */
    public function setProjectdetails($projectdetails)
    {
        $this->projectdetails = $projectdetails;
        return $this;
    }

    /**
     * Get projectdetails
     *
     * @return string $projectdetails
     */
    public function getProjectdetails()
    {
        return $this->projectdetails;
    }

    /**
     * Set projectchecktype
     *
     * @param string $projectchecktype
     * @return self
     */
    public function setProjectchecktype($projectchecktype)
    {
        $this->projectchecktype = $projectchecktype;
        return $this;
    }

    /**
     * Get projectchecktype
     *
     * @return string $projectchecktype
     */
    public function getProjectchecktype()
    {
        return $this->projectchecktype;
    }

    /**
     * Set publishedOn
     *
     * @param date $publishedOn
     * @return self
     */
    public function setPublishedOn($publishedOn)
    {
        $this->publishedOn = $publishedOn;
        return $this;
    }

    /**
     * Get publishedOn
     *
     * @return date $publishedOn
     */
    public function getPublishedOn()
    {
        return $this->publishedOn;
    }

    /**
     * Set endingOn
     *
     * @param date $endingOn
     * @return self
     */
    public function setEndingOn($endingOn)
    {
        $this->endingOn = $endingOn;
        return $this;
    }

    /**
     * Get endingOn
     *
     * @return date $endingOn
     */
    public function getEndingOn()
    {
        return $this->endingOn;
    }

    /**
     * Add member
     *
     * @param Soound\AppBundle\Document\User $member
     */
    public function addMember(\Soound\AppBundle\Document\User $member)
    {
        $this->members[] = $member;
    }

    /**
     * Remove member
     *
     * @param Soound\AppBundle\Document\User $member
     */
    public function removeMember(\Soound\AppBundle\Document\User $member)
    {
        $this->members->removeElement($member);
    }

    /**
     * Get members
     *
     * @return Doctrine\Common\Collections\Collection $members
     */
    public function getMembers()
    {
        return $this->members;
    }

    /**
     * Set followersCount
     *
     * @param int $followersCount
     * @return self
     */
    public function setFollowersCount($followersCount)
    {
        $this->followersCount = $followersCount;
        return $this;
    }

    /**
     * Get followersCount
     *
     * @return int $followersCount
     */
    public function getFollowersCount()
    {
        return $this->followersCount;
    }

    /**
     * Set musicianTech
     *
     * @param string $musicianTech
     * @return self
     */
    public function setMusicianTech($musicianTech)
    {
        $this->musicianTech = $musicianTech;
        return $this;
    }

    /**
     * Get musicianTech
     *
     * @return string $musicianTech
     */
    public function getMusicianTech()
    {
        return $this->musicianTech;
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
     * Get submissions
     *
     * @return Doctrine\Common\Collections\Collection $submissions
     */
    public function getSubmissions()
    {
        return $this->submissions;
    }

    /**
     * @return mixed
     */
    public function getProjectgenre()
    {
        return $this->projectgenre;
    }

    /**
     * @param String $genre
     */
    public function addProjectGenre($genre)
    {
        if( !in_array($genre, $this->projectgenre) )
            array_push($this->projectgenre, $genre);
    }

    /**
     * Set winner
     *
     * @param Soound\AppBundle\Document\Submission $winner
     * @return self
     */
    public function setWinner(\Soound\AppBundle\Document\Submission $winner)
    {
        $this->winner = $winner;
        return $this;
    }

    /**
     * Get winner
     *
     * @return Soound\AppBundle\Document\Submission $winner
     */
    public function getWinner()
    {
        return $this->winner;
    }

    /**
     * Has winner
     *
     * @return Boolean $hasWinner
     */
    public function hasWinner()
    {
        return $this->winner ? true : false;
    }

    /**
     * Is winner
     *
     * @param Soound\AppBundle\Document\User $user
     * @return Boolean $isWinner
     */
    public function isWinner($user)
    {
        if( $this->winner ){
            return $this->winner->getUser()->getId() === $user->getId();
        }
        return false;
    }

    /**
     * Set projectgenre
     *
     * @param collection $projectgenre
     * @return self
     */
    public function setProjectgenre($projectgenre)
    {
        $this->projectgenre = $projectgenre;
        return $this;
    }

    /**
     * Add follower
     *
     * @param Soound\AppBundle\Document\User $follower
     */
    public function addFollower(\Soound\AppBundle\Document\User $follower)
    {
        $this->followers[] = $follower;
    }

    /**
     * Remove follower
     *
     * @param Soound\AppBundle\Document\User $follower
     */
    public function removeFollower(\Soound\AppBundle\Document\User $follower)
    {
        $this->followers->removeElement($follower);
    }

    /**
     * Get followers
     *
     * @return Doctrine\Common\Collections\Collection $followers
     */
    public function getFollowers()
    {
        return $this->followers;
    }

    /**
     * Set projectPic
     *
     * @param string $projectPic
     * @return self
     */
    public function setProjectPic($projectPic)
    {
        $this->projectPic = $projectPic;
        return $this;
    }

    /**
     * Get projectPic
     *
     * @return string $projectPic
     */
    public function getProjectPic()
    {
        return $this->projectPic;
    }

    /**
     * Set projectExt
     *
     * @param string $projectExt
     * @return self
     */
    public function setProjectExt($projectExt)
    {
        $this->projectExt = $projectExt;
        return $this;
    }

    /**
     * Get projectExt
     *
     * @return string $projectExt
     */
    public function getProjectExt()
    {
        return $this->projectExt;
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
     * Get teamInviteKey
     *
     * @return string $teamInviteKey
     */
    public function getTeamInviteKey()
    {
        if(!isset($this->teamInviteKey))
            $this->teamInviteKey = uniqid();
        return $this->teamInviteKey;
    }

    /**
     * Set teamInviteKey
     *
     * @param string $teamInviteKey
     * @return self
     */
    public function setTeamInviteKey($teamInviteKey)
    {
        $this->teamInviteKey = $teamInviteKey;
        return $this;
    }

    /**
     * Add team
     *
     * @param Soound\AppBundle\Document\User $team
     */
    public function addTeam(\Soound\AppBundle\Document\User $team)
    {
        $this->team[] = $team;
    }

    /**
     * Remove team
     *
     * @param Soound\AppBundle\Document\User $team
     */
    public function removeTeam(\Soound\AppBundle\Document\User $team)
    {
        $this->team->removeElement($team);
    }

    /**
     * Get team
     *
     * @return Doctrine\Common\Collections\Collection $team
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * @param String $email
     */
    public function addPendingTeam($email)
    {
        if( !in_array($email, $this->pendingTeam) )
            array_push($this->pendingTeam, $email);
    }

    /**
     * @param String $email
     */
    public function removePendingTeam($email)
    {
        if( in_array($email, $this->pendingTeam) )
            array_splice( $this->pendingTeam, array_search($email, $this->pendingTeam), 1);
    }

    /**
     * Checks if email is in the pending team invite list
     *
     * @return Boolean $inGroup
     */
    public function inPendingTeam($email)
    {
        if( in_array($email, $this->pendingTeam) )
            return true;
        
        return false;
    }


    /**
     * @return mixed
     */
    public function getPendingTeam()
    {
        return $this->pendingTeam;
    }


    /**
     * Set transactionId
     *
     * @param string $transactionId
     * @return self
     */
    public function setTransactionId($transactionId)
    {
        $this->transactionId = $transactionId;
        return $this;
    }

    /**
     * Get transactionId
     *
     * @return string $transactionId
     */
    public function getTransactionId()
    {
        return $this->transactionId;

    }
    /*
     * Runs through a list of users and checks if this user is in it
     *
     * @return Boolean $inSubmissions
     */
    public function inSubmissions($user)
    {
        foreach ($this->getSubmissions() as $submission) {
            if($submission->getUser()->getId()==$user->getId())
                return true;
        }
        return false;
    }

    /**
     * Has hQFiles
     *
     * @return Boolean $hasHQFiles
     */
    public function hasHQFiles()
    {
        return count($this->HQFiles) ? true : false;
    }

    /**
     * Add hQFile
     *
     * @param Soound\AppBundle\Document\HQFile $hQFile
     */
    public function addHQFile(\Soound\AppBundle\Document\HQFile $hQFile)
    {
        $this->HQFiles[] = $hQFile;
    }

    /**
     * Remove hQFile
     *
     * @param Soound\AppBundle\Document\HQFile $hQFile
     */
    public function removeHQFile(\Soound\AppBundle\Document\HQFile $hQFile)
    {
        $this->HQFiles->removeElement($hQFile);
    }

    /**
     * Get hQFiles
     *
     * @return Doctrine\Common\Collections\Collection $hQFiles
     */
    public function getHQFiles()
    {
        return $this->HQFiles;
    }

    /**
     * Get minDate
     *
     * @return Date $minDate
     */
    public function getHQFilesMinDate()
    {
        $minDate;

        foreach ($this->HQFiles as $file) {
            if(isset($minDate)){
                if( $file->getUploadDate() < $minDate){
                    $minDate = $file->getUploadDate();
                }
            }
            else
                $minDate = $file->getUploadDate();
            
        }

        return $minDate;
    }

    /**
     * Set complaint
     *
     * @param string $complaint
     * @return self
     */
    public function setComplaint($complaint)
    {
        $this->complaint = $complaint;
        return $this;
    }

    /**
     * Get complaint
     *
     * @return string $complaint
     */
    public function getComplaint()
    {
        return $this->complaint;
    }

    /**
     * Set complaintResolved
     *
     * @param boolean $complaintResolved
     * @return self
     */
    public function setComplaintResolved($complaintResolved)
    {
        $this->complaintResolved = $complaintResolved;
        return $this;
    }

    /**
     * Get complaintResolved
     *
     * @return boolean $complaintResolved
     */
    public function getComplaintResolved()
    {
        return $this->complaintResolved;
    }

    /**
     * Set $payed
     */
    public function setPayed($payed){
        $this->payed = $payed;
    }

    /**
     * Get $payed
     */
    public function getPayed(){
        return $this->payed;
    }
}
