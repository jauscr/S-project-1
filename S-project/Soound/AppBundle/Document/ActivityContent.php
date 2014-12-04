<?php
namespace Soound\AppBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;

/**
 * @MongoDB\EmbeddedDocument
 */
class ActivityContent
{
 
    /**
     * @MongoDB\Id(strategy="AUTO")
     */
    private $activityContentID;

    /**
     * @MongoDB\ReferenceOne
     */
    private $ref;

    /**
     * @MongoDB\String
     */
    private $refClass;

    /**
     * @MongoDB\String
     */
    private $text;


    /**
     * Get activityContentID
     *
     * @return id $activityContentID
     */
    public function getActivityContentID()
    {
        return $this->activityContentID;
    }

    /**
     * Set ref
     *
     * @param $ref
     * @return self
     */
    public function setRef($ref)
    {
        //$refClass = get_class($ref);
        $this->ref = $ref;
        return $this;
    }

    /**
     * Get ref
     *
     * @return $ref
     */
    public function getRef()
    {
        return $this->ref;
    }

    /**
     * Determine if hasReference
     *
     * @return $boolean $hasRef
     */
    public function hasRef()
    {
        return $this->ref ? true : false;
    }

    /**
     * Get refName
     *
     * @return $string $ref
     */
    public function getRefDetails()
    {
        $name = "";
        $link = "";
        $picture = "";
        $desc = "";

        if( method_exists($this->ref, 'getProjectId') ){
            $name = $this->ref->getProjectname();
            $link = '/submit/'.$this->ref->getPublicId();
            $picture = '/uploads/projectPics/'.($this->ref->getProjectPic() != "" ? ($this->ref->getProjectPic().'-120.'.$this->ref->getProjectExt()) : "project_pic_small.png");
            $desc = (strlen($this->ref->getProjectdetails())>140 ? substr($this->ref->getProjectdetails(), 0, 140).'…' : $this->ref->getProjectdetails());
        }
        else if( method_exists($this->ref, 'getUsername') ){
            $picture = '/uploads/userPics/'.($this->ref->getPictureExt() ? $this->ref->getPicture(50) : 'default.png');
            $name = $this->ref->getFullname();
            $link = '/profile/'.$this->ref->getPublicId();
        }
        
        $refDetails = array(
            "name" => $name,
            "link" => $link
        );

        if($picture)
            $refDetails["picture"]=$picture;

        if($desc)
            $refDetails["desc"]=$desc;


        return $refDetails;
    }

    /**
     * Set text
     *
     * @param string $text
     * @return self
     */
    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    /**
     * Get text
     *
     * @return string $text
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Determine whether or not activityContent hasText
     *
     * @return boolean $hasText
     */
    public function hasText()
    {
        return $this->text ? true : false;
    }

    /**
     * Set refClass
     *
     * @param string $refClass
     * @return self
     */
    public function setRefClass($refClass)
    {
        $this->refClass = $refClass;
        return $this;
    }

    /**
     * Get refClass
     *
     * @return string $refClass
     */
    public function getRefClass()
    {
        return $this->refClass;
    }
}
