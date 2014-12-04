<?php

namespace Soound\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Soound\AppBundle\Document\Project;
use Soound\AppBundle\Document\ProjectReference;
use Soound\AppBundle\Document\ProjectFile;
use Soound\AppBundle\Document\Transaction;
use Soound\AppBundle\Document\Submission;
use Soound\AppBundle\Document\Revision;
use Soound\AppBundle\Document\HQFile;
use Soound\AppBundle\Document\Rating;
use Soound\AppBundle\Document\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Hip\MandrillBundle\Message;

class ProjectSubmissionsController extends Controller
{
	/**
	 * [indexAction description]
	 * @param  [type] $filename [description]
	 * @return [type]           [description]
	 */
	public function indexAction($publicId){

		$session = $this->getRequest()->getSession();

		$dm = $this->get('doctrine_mongodb')->getManager();
		$user = $this->user();
		$project = $dm->getRepository('SooundAppBundle:Project')->findOneBy(array('publicId' => $publicId));

		//Only team or project poster have access to this page
		if( !( $user->getId() === $project->getUser()->getId() || $user->inGroup( $project->getTeam() ) ) ){
			return $this->redirect(
				$this->generateUrl(
					'submit',
					array(
						'publicId' => $publicId
					)
				)
			);
		}

		$subId = $this->getRequest()->query->get('sub');
		$personId = $this->getRequest()->query->get('user');
		$tab = $this->getRequest()->query->get('tab');
		$comment = $this->getRequest()->query->get('comment');
		if($subId && !$tab){ //SubId without a tab specified
			//Find out which tab this submission is in
			foreach ($project->getSubmissions() as $submission) {
				if($subId == $submission->getPublicId()){
					if($submission->hasRated($user)) //Counts as rated
						$tab = "rated";
					else if($submission->hasListened($user)) //Counts as not-rated
						$tab = "not-rated";
					else //Otherwise counts as new
						$tab = "new";
					break;
				}
			}
			if(!$tab){
				return $this->redirect(
					$this->generateUrl(
						'projectSubmissions',
						array(
							'publicId' => $publicId
						)
					)
				);
			}
			else {
				return $this->redirect(
					$this->generateUrl(
						'projectSubmissions',
						array(
							'publicId' => $publicId,
							'tab' => $tab,
							'sub' => $subId,
							'comment' => $comment
						)
					)
				);
			}
		}
		else if($personId && !$tab){
			$person = $dm->getRepository('SooundAppBundle:User')->findOneBy(array('publicId' => $personId));
			if($person->inGroup($project->getTeam()))
				$tab = "team";
			else if($person->inGroup($project->getMembers()))
				$tab = "members";
			else if($person->inGroup($project->getWatchers()))
				$tab = "watchers";

			if(!$tab){
				return $this->redirect(
					$this->generateUrl(
						'projectSubmissions',
						array(
							'publicId' => $publicId
						)
					)
				);
			}
			else {
				return $this->redirect(
					$this->generateUrl(
						'projectSubmissions',
						array(
							'publicId' => $publicId,
							'tab' => $tab,
							'user' => $personId
						)
					)
				);
			}
		}
		
		$session->set('project', $project->getProjectId());
		$session->set('project_files', null);
		$s3 = $this->get('aws_s3');

		$projectTwig = array(
			"userPic" => $s3->get_object($this->container->getParameter('s3_bucket'),$user->getPicture(),array("preauth"=>strtotime("+1 hour"))),
			"isOwner" => $user->ownsProject($project->getProjectId()),
			"projectPublicId" => $project->getPublicId(),
			"projectTitle" => $project->getProjectname(),
			"projectDesc" => $project->getProjectdetails(),
			"budget" => $project->getBudget(),
			'picture' => $project->getProjectExt() != "" ? ($s3->get_object($this->container->getParameter('s3_bucket'),"Projects/".$project->getPublicId()."/image/".$project->getPublicId()."-300.".$project->getProjectExt(),array("preauth"=>strtotime("+1 hour")))) : "/uploads/projectPics/project_pic_small.png",
			"deadline" => $project->getDueDate()->diff( date_create() )->format('%a'),
			"submissions" => count( $project->getSubmissions() ),
			"members" => count( $project->getMembers() ),
			"watchers" => count( $project->getFollowers() ),
			"winner" => $project->getWinner(),
			"projectDetails" => $this->getProjectDetails($project)
		);

		if( $projectTwig["winner"] ){
			if( $project->hasHQFiles() ){
				$projectTwig["acceptBy"] = $project->getHQFilesMinDate()->add(new \DateInterval('P3D'))->format('m/d');
				if( !$project->getComplaintResolved() ){
					$projectTwig["complaint"] = $project->getComplaint();
				}
				$projectTwig["payed"] = $project->getPayed();
			}
		}
		else {
			$projectTwig["daysLeft"] = $project->getDueDate()->diff(date_create())->format('%a');
			$projectTwig["daysTotal"] = $project->getDueDate()->diff($project->getPublishDate())->format('%a');
			$projectTwig["daysPercent"] = round((($projectTwig["daysTotal"]-$projectTwig["daysLeft"])/$projectTwig["daysTotal"])*100);

		}

		return $this->render(
			"SooundAppBundle:Html:project-owner-submissions.html.twig",
			$projectTwig
		);

	}

	private function getProjectDetails($project) {
        $s3 = $this->get('aws_s3');
		$details = array();
		$type = strtolower($project->getProjectType());
		switch ($type) {
			case 'completesongs':
				$details['type'] = 'Complete Song';

				$details['technical']['tempo'] = $project->getProjecttempo();
				$details['technical']['key'] = $project->getKeysong();
				$details['technical']['mood'] = $project->getMoodsong();
				$details['technical']['style'] = $project->getProjectstyle();

				$details['instrumental']['drumsPreference'] = $project->getDrumspref();
				$details['instrumental']['instrumentPreference'] = $project->getInstrumentRef();
				$details['instrumental']['dominantSounds'] = $project->getDominantsound();

				$details['theme']['topic'] = $project->getSongTopic();
				$details['theme']['mention'] = $project->getSongMention();
				$details['section_keys'] = array('technical', 'instrumental', 'theme');
				break;
			
			case 'production':
				$details['type'] = 'Production';

				$details['technical']['tempo'] = $project->getProjecttempo();
				$details['technical']['key'] = $project->getKeysong();
				$details['technical']['mood'] = $project->getMoodsong();
				$details['technical']['style'] = $project->getProjectstyle();

				$details['instrumental']['drumsPreference'] = $project->getDrumspref();
				$details['instrumental']['instrumentPreference'] = $project->getInstrumentRef();
				$details['instrumental']['dominantSounds'] = $project->getDominantsound();
				$details['section_keys'] = array('technical', 'instrumental');
				break;

			case 'songwriting':
				$details['type'] = 'Song Writing';

				$details['theme']['topic'] = $project->getSongTopic();
				$details['theme']['mention'] = $project->getSongMention();

				$details['section_keys'] = array('theme');

				break;

			case 'musician':
				$details['type'] = 'Musician';

				$details['musician']['musicianType'] = $project->getMusicianType();
				$details['musician']['musicianTech'] = $project->getMusicianTech();
				$details['section_keys'] = array('musician');
				break;

			case 'vocal':
				$details['type'] = 'Vocal';

				$details['vocal']['vocalGender'] = $project->getGenderVocal();
				$details['vocal']['vocalRange'] = $project->getVocalRange();
				$details['vocal']['vocalLanguages'] = $project->getVocalLan();
				$details['section_keys'] = array('vocal');
				break;

			case 'engineering':
				$details['type'] = 'Engineering';

				$details['technical']['tempo'] = $project->getEngTempo();
				$details['technical']['key'] = $project->getKeysong();
				$details['section_keys'] = array('technical');
				
				break;
		}
		$references = array();
		foreach ($project->getReferences() as $ref) {
			$reference=array("link"=>$ref->getLink(),"description"=>$ref->getDescription(),"isAudio"=>$ref->isAudio(),"extension"=>$ref->getExtension(),"id"=>$ref->getReferenceId(),"title"=>$ref->getTitle(),"duration"=>$ref->getDuration());
			if($reference['isAudio']){
				$reference["audioURL"] = $s3->get_object($this->container->getParameter('s3_bucket'),"Projects/".$project->getPublicId()."/reference/audio/".$ref->getReferenceId().'.'.$ref->getExtension(),array("preauth"=>strtotime("+1 hour")));
				$reference["waveURL"] = file_get_contents($s3->get_object($this->container->getParameter('s3_bucket'),"Projects/".$project->getPublicId()."/reference/image/".$ref->getReferenceId().'.svg', array("preauth"=>strtotime("+1 hour"))));
			}
			$references[]=$reference;
		}

		$files = array();
		foreach ($project->getFiles() as $fil) {
			if($fil->getDescription()){
				$file=array("link"=>$fil->getLink(),"description"=>$fil->getDescription(),"isAudio"=>$fil->isAudio(),"extension"=>$fil->getExtension(),"id"=>$fil->getFileId(),"title"=>$fil->getTitle(),"duration"=>$fil->getDuration());
				if($file['isAudio']){
					$file["audioURL"] = $s3->get_object($this->container->getParameter('s3_bucket'),"Projects/".$project->getPublicId()."/file/audio/".$ref->getFileId().'.'.$ref->getExtension(),array("preauth"=>strtotime("+1 hour")));
					$file["waveURL"] = file_get_contents($s3->get_object($this->container->getParameter('s3_bucket'),"Projects/".$project->getPublicId()."/file/image/".$ref->getFileId().'.svg', array("preauth"=>strtotime("+1 hour"))));
					$file["download"] = $s3->get_object($this->container->getParameter('s3_bucket'),"Projects/".$project->getPublicId()."/file/".$ref->getFileId().'.'.$ref->getExtension(),array("preauth"=>strtotime("+1 hour")));

				}
				else if($file['extension'])
					$file["download"] = $s3->get_object($this->container->getParameter('s3_bucket'),"Projects/".$project->getPublicId()."/file/".$ref->getFileId().'.'.$ref->getExtension(),array("preauth"=>strtotime("+1 hour")));

				$files[]=$file;
			}

		}
		$details['references']=$references;
		$details['files']=$files;
		$details["genres"]=$project->getProjectgenre();
		return $details;
	}

	public function uploadPicAction(){
		$session = $this->getRequest()->getSession();
		$dm = $this->get('doctrine_mongodb')->getManager();
		$project = $dm->find('SooundAppBundle:Project', $session->get('project'));
		if(isset($_FILES['file'])){
            $name = $_FILES['file']['name'];
            $fileSize = $_FILES['file']['size'];
            $uploadedFile = $_FILES['file']['tmp_name'];
        }

        $maxSize = 5;//In MB
        $allowedExts = array("jpg", "jpeg", "png", "gif");
        $temp = explode(".", $name);
        $extension = end($temp);

        $allowed = false;

        //Check file type and size
        if( in_array($extension, $allowedExts)){
            if( $fileSize < $maxSize*1000000){
                $allowed = true;
            }
        }

        if($allowed){
            //$dimensions = $this->container->getParameter('picture_dimensions')['project'];
            $dimensions = array(120, 200, 300);
			$s3 = $this->get('aws_s3');

            $pictureId = $project->getPublicId();
            $base = $this->get('kernel')->getRootDir() . '/../web/uploads/';
            $picturePath = $base.'projectPics/'.$pictureId.'.'.$extension;

            if($project->getProjectPic() != ""){
                foreach ($dimensions as $dim) {
                    unlink($base.'projectPics/'.$project->getProjectPic().'-'.$dim.'.'.$project->getProjectExt());
                }
            }

            //Store the file in uploaded pictures
            
            foreach ($dimensions as $dim) {
                $this->square_crop($uploadedFile, $base.'projectPics/'.$pictureId.'-'.$dim.'.'.$extension, $dim, 100);
                $s3->create_object($this->container->getParameter('s3_bucket'),"Projects/".$pictureId."/image/".$pictureId."-".$dim.".".$extension,array('fileUpload' => $base.'projectPics/'.$pictureId.'-'.$dim.'.'.$extension));
                unlink($base.'projectPics/'.$pictureId.'-'.$dim.'.'.$extension);
            }

            $project->setProjectExt($extension);

            $this->get('doctrine_mongodb')->getManager()->flush(); //Save changes


            $response = array(
                "msg" => "ok",
				'picture' => $s3->get_object($this->container->getParameter('s3_bucket'),"Projects/".$pictureId."/image/".$pictureId."-120.".$extension,array("preauth"=>strtotime("+1 hour")))
            );
        }
        else {
            $response = array(
                "msg" => "error"
            );
        }

        return new Response( json_encode($response));
	}

	private function square_crop($src_image, $dest_image, $thumb_size = 64, $jpg_quality = 90) {

        // Get dimensions of existing image
        $image = getimagesize($src_image);

        // Check for valid dimensions
        if( $image[0] <= 0 || $image[1] <= 0 ) return false;

        // Determine format from MIME-Type
        $image['format'] = strtolower(preg_replace('/^.*?\//', '', $image['mime']));

        // Import image
        switch( $image['format'] ) {
            case 'jpg':
            case 'jpeg':
                $image_data = imagecreatefromjpeg($src_image);
            break;
            case 'png':
                $image_data = imagecreatefrompng($src_image);
            break;
            case 'gif':
                $image_data = imagecreatefromgif($src_image);
            break;
            default:
                // Unsupported format
                return false;
            break;
        }

        // Verify import
        if( $image_data == false ) return false;

        // Calculate measurements
        if( $image[0] > $image[1] ) {
            // For landscape images
            $x_offset = ($image[0] - $image[1]) / 2;
            $y_offset = 0;
            $square_size = $image[0] - ($x_offset * 2);
        } else {
            // For portrait and square images
            $x_offset = 0;
            $y_offset = ($image[1] - $image[0]) / 2;
            $square_size = $image[1] - ($y_offset * 2);
        }

        // Resize and crop
        $canvas = imagecreatetruecolor($thumb_size, $thumb_size);
        if( imagecopyresampled(
            $canvas,
            $image_data,
            0,
            0,
            $x_offset,
            $y_offset,
            $thumb_size,
            $thumb_size,
            $square_size,
            $square_size
        )) {

            // Create thumbnail
            switch( strtolower(preg_replace('/^.*\./', '', $dest_image)) ) {
                case 'jpg':
                case 'jpeg':
                    return imagejpeg($canvas, $dest_image, $jpg_quality);
                break;
                case 'png':
                    return imagepng($canvas, $dest_image);
                break;
                case 'gif':
                    return imagegif($canvas, $dest_image);
                break;
                default:
                    // Unsupported format
                    return false;
                break;
            }

        } else {
            return false;
        }
    }
    public function updateProjectAction(){
    	$session = $this->getRequest()->getSession();
		$dm = $this->get('doctrine_mongodb')->getManager();
		$project = $dm->find('SooundAppBundle:Project', $session->get('project'));

		if(!empty($_POST['title']))
			$project->setProjectname($_POST['title']);

		if(!empty($_POST['desc']))
			$project->setProjectdetails($_POST['desc']);

		if(!empty($_POST['genre'])){
			$genres = $genreArray = explode(",", strtolower($_POST['genre']));
			$project->setProjectgenre($genres);
		}

		if(!empty($_POST['tempo'])){
			if($project->getProjectType()=='engineering')
				$project->setEngTempo($_POST['tempo']);
			else
				$project->setProjecttempo($_POST['tempo']);				
		}

		if(!empty($_POST['keysong']))
			$project->setKeysong($_POST['keysong']);

		if(!empty($_POST['moodsong']))
			$project->setMoodsong($_POST['moodsong']);

		if(!empty($_POST['style']))
			$project->setProjectstyle($_POST['style']);

		if(!empty($_POST['dominantsound']))
			$project->setDominantsound($_POST['dominantsound']);

		if(!empty($_POST['drumspref']))
			$project->setDrumspref($_POST['drumspref']);

		if(!empty($_POST['instrument']))
			$project->setInstrumentRef($_POST['instrument']);

		if(!empty($_POST['songTopic']))
			$project->setSongTopic($_POST['songTopic']);

		if(!empty($_POST['songMention']))
			$project->setSongMention($_POST['songMention']);

		if(!empty($_POST['musicianType']))
			$project->setMusicianType($_POST['musicianType']);

		if(!empty($_POST['musicianTech']))
			$project->setMusicianTech($_POST['musicianTech']);

		if(!empty($_POST['vocalGender']))
			$project->setGenderVocal($_POST['vocalGender']);

		if(!empty($_POST['vocalRange']))
			$project->setVocalRange($_POST['vocalRange']);

		if(!empty($_POST['vocalLan']))
			$project->setVocalLan($_POST['vocalLan']);

    	$session = $this->getRequest()->getSession();
		$files =  $session->get('project_files');
		$s3 = $this->get('aws_s3');

		if($_POST['project_reference']){
			$i=-1;
			foreach ($_POST['project_reference'] as $key => $reference) {
				if(strlen($reference['desc'])){
	                $projectRef = new ProjectReference();
	                $projectRef->setDescription($reference['desc']);

					if($reference['hasFile']=="1"){
						$file = $files['references'][$key+$i];
		                $projectRef->setIsAudio(true);
	    	            $projectRef->setExtension($file['extension']);
	    	            $projectRef->setDuration($file['duration']);
		                $projectRef->setTitle($file['title']);
					    $s3->create_object($this->container->getParameter('s3_bucket'),"Projects/".$project->getPublicId()."/reference/audio/".$projectRef->getReferenceId().'.'.$file['extension'],array('fileUpload' => $file['audio']));
					    $s3->create_object($this->container->getParameter('s3_bucket'),"Projects/".$project->getPublicId()."/reference/image/".$projectRef->getReferenceId().'.svg',array('fileUpload' => $file['waveform']));
					    unlink($file['audio']);
					    unlink($file['waveform']);
					}
					else{
					    $i--;
					    $projectRef->setLink($reference['link']);
					}
					$project->addReference($projectRef);
				}
			}
		}
		if($_POST['project_file']){
			$i=-1;
			foreach ($_POST['project_file'] as $key => $file) {
				if(strlen($file['desc'])){
	                $projectFile = new ProjectFile();
	                $projectFile->setDescription($file['desc']);

					if($file['hasFile']=="1"){
						$file = $files['files'][$key+$i];
						if($file['audio']){
		                	$projectFile->setIsAudio(true);
		    	            $projectFile->setDuration($file['duration']);
			                $projectFile->setTitle($file['title']);
					    	$s3->create_object($this->container->getParameter('s3_bucket'),"Projects/".$project->getPublicId()."/file/audio/".$projectFile->getFileId().'.'.$file['extension'],array('fileUpload' => $file['audio']));
					    	$s3->create_object($this->container->getParameter('s3_bucket'),"Projects/".$project->getPublicId()."/file/image/".$projectFile->getFileId().'.svg',array('fileUpload' => $file['waveform']));
						    unlink($file['audio']);
						    unlink($file['waveform']);
						}
	                    $s3->create_object($this->container->getParameter('s3_bucket'),"Projects/".$project->getPublicId()."/file/".$projectFile->getFileId().'.'.$file['extension'],array('fileUpload' => $file['file']));
	    	            $projectFile->setExtension($file['extension']);
					}
					else{
					    $i--;
					    $projectFile->setLink($file['link']);
					}
					$project->addFile($projectFile);
				}
			}
		}

		$dm->persist($project);
		$dm->flush();

		$activity = array(
			'to' => $this->user(),
			'type' => 'envelope',
			'date' => date_format( date_create(), "m-d-Y" ),
			'private' => true, //Optional, defaults to false
			'content' => array(
				0 => array(
					'text' => 'Project updated'
				)
			),
			'dontSave' => true
		);
		$this->get('soound_app.activity')->send($activity);


		$html = $this->render(
			"SooundAppBundle:Html:project-details.html.twig", array(
				"isOwner" => $this->user()->ownsProject($project->getProjectId()),
				"projectDetails" => $this->getProjectDetails($project)
			)
		);
		return new Response(json_encode(array(
			"html"=>$html->getContent(),
			"title" => $project->getProjectname(),
			"desc" => $project->getProjectdetails(),
			"msg"=>"ok"
		)));
    }
    public function saveFieldAction(){
    	$session = $this->getRequest()->getSession();
		$dm = $this->get('doctrine_mongodb')->getManager();
		$project = $dm->find('SooundAppBundle:Project', $session->get('project'));

		if($_POST['field'] === 'project-title'){
			$project->setProjectname($_POST['val']);
		} else if($_POST['field'] === 'project-des'){
			$project->setProjectdetails($_POST['val']);
		}
		$dm->flush();

		return new Response("ok");
    }

	public function inviteTeamAction(){
		$user = $this->user();
		$emails = $this->get('request')->request->get('arrayMails');

		$session = $this->getRequest()->getSession();
		$dm = $this->get('doctrine_mongodb')->getManager();
		$project = $dm->find('SooundAppBundle:Project', $session->get('project'));
		$url = $this->generateUrl(
					'acceptTeamInvite',
					array(
						'publicId' => $project->getPublicId(),
						'key' => $project->getTeamInviteKey()
					), true);

		$dispatcher = $this->get('hip_mandrill.dispatcher');
		$message = new Message();
		$message
			->setFromEmail($user->getEmail())
			->setFromName($user->getFullname())
			->setSubject($user->getFullname().' invited you to collaborate on Soound')
			->setHtml($this->renderView(
				'SooundAppBundle:Mail:teamInvite.html.twig',
				array(
					'ownerName' => $user->getFullname(),
					'projectName' => $project->getProjectName(),
					'acceptURL' => $url
				)
			));
		foreach ($emails as $email){
			$message->addTo($email);
			$project->addPendingTeam($email);
		}
		$dm->flush();
		$result = $dispatcher->send($message);
		return new Response(json_encode(array("emails"=>$emails)));
	}

	public function removeTeamAction(){
		if($_POST['email'] && $_POST['type']){
			$session = $this->getRequest()->getSession();
			$dm = $this->get('doctrine_mongodb')->getManager();
			$project = $dm->find('SooundAppBundle:Project', $session->get('project'));
			if($_POST['type']=='pending'){
				$project->removePendingTeam($_POST['email']);
			}
			else{
				$team = $project->getTeam();
				foreach ($team as $member) {
					if($member->getEmail()==$_POST['email']){
						$project->removeTeam($member);
						$member->removeTeamMemberOfProject($project);
					}
				}				
			}
			$dm->flush();
			$response = array("msg" => "ok","publicId" => $project->getPublicId());
		}
		else
			$response = array("msg" => "error");


		//Return the results
		return new Response( json_encode($response) );
	}

	public function acceptTeamAction($publicId, $key){
		$dm = $this->get('doctrine_mongodb')->getManager();
		//!Check user login status, if not logged in, route to login first, then back here

		$user = $this->user();
		$project = $dm->getRepository('SooundAppBundle:Project')->findOneBy(array('publicId' => $publicId));

		if($key === $project->getTeamInviteKey()){
			if(!$user->inGroup($project->getTeam()) && $project->inPendingTeam($user->getEmail())){
				$project->addTeam($user);
				$project->removePendingTeam($user->getEmail());
				$user->addTeamMemberOfProject($project);
				$dm->flush();
			}
			return $this->redirect(
				$this->generateUrl(
					'projectSubmissions',
					array(
						'publicId' => $publicId
					)
				)
			);
		}
		else {
			return $this->redirect(
				$this->generateUrl(
					'browse'
				)
			);
		}

	}

	private function user(){
		$user = $this->get('security.context')->getToken()->getUser();
		return $user;
	}

	public function topSubmissionsAction(){
		$session = $this->getRequest()->getSession();
		$dm = $this->get('doctrine_mongodb')->getManager();
		$s3 = $this->get('aws_s3');
        $user = $this->user();

        $project = $dm->find('SooundAppBundle:Project', $session->get('project'));

		$query = $dm->createQueryBuilder("SooundAppBundle:Submission")
					->field('project')->equals( $session->get('project') )
					->limit(3)->sort('avgRating', 'desc');

		$query = $query->getQuery()->execute();

		$topSubmissions = array();
		foreach ($query as $submission) {
			$lastRevision = $submission->getLastRevision();

			array_push( $topSubmissions, array(
				"id" => 'top'.$lastRevision->getRevisionID(),
				"duration" => $lastRevision->getDuration(),
				"title" => $lastRevision->getTitle(),
				"artist" => $submission->getUser()->getFullname(),
				"artistUrl" => $this->container->get('router')->generate('userProfile', array('publicId' => $submission->getUser()->getPublicId())),
				"avgRating" => $lastRevision->getAvgRating(),
				"userRating" => $lastRevision->getRating( $user ),
				"date" => date_format( $lastRevision->getDate(), 'Y/m/d'),
				"songURL" => $s3->get_object($this->container->getParameter('s3_bucket'),"Projects/".$project->getPublicId()."/audio/".$submission->getUser()->getPublicId()."/".$lastRevision->getRevisionID().'.'.$lastRevision->getExtension(),array("preauth"=>strtotime("+1 hour"))),
				"waveURL" => file_get_contents($s3->get_object($this->container->getParameter('s3_bucket'),"Projects/".$project->getPublicId()."/image/".$submission->getUser()->getPublicId()."/".$lastRevision->getRevisionID().'-small.svg', array("preauth"=>strtotime("+1 hour"))))
			));
		}

		//Return the results
		$response = array(
			"msg" => "ok",
			"content" => $topSubmissions
		);

		return new Response( json_encode($response) );
	}

	public function projectOwnerSubmissionsAction(){

		$session = $this->getRequest()->getSession();
		$dm = $this->get('doctrine_mongodb')->getManager();
        $user = $this->user();

		$type = $_POST['type'];

		$query = $dm->createQueryBuilder('SooundAppBundle:Submission');
		$query->field('project')->equals( $session->get('project') );
		$query->field('rejected')->equals(false);

		switch($type){
			//The most recently updated submissions
			case "new":
				$query->sort('lastUpdated', 'asc');
				break;
			//The most recently updated submissions that this user has rated
			case "rated":
				$query->sort('lastUpdated', 'asc')
					  ->field('ratings.userID')->equals( $user->getId() );
					  //->field('lastRevision.ratings.userID')->equals( $user->getId() );
				break;
			//The most recently update submission that this user has listened to for at least 15 seconds, but hasn't rated
			case "not rated":
				$query->sort('lastUpdated', 'asc')
					  ->field('ratings.userID')->notEqual( $user->getId() )
					  //->field('lastRevision.ratings.userID')->notEqual( $user->getId() )
					  ->field('listeners')->equals( $user->getId() );
				break;
		}

		$query = $query->getQuery()->execute();
		$results = array();

		$role = "project team";
		$project = $dm->find('SooundAppBundle:Project', $session->get('project'));
		if($project->getUser()->getId() === $user->getId() )
			$role = "project owner";

        $s3 = $this->get('aws_s3');

		foreach ($query as $submission) {
			$revisions = $submission->getRevisions();
			$revs = array();
			$len = sizeof($revisions);
			foreach ($revisions as $i => $revision) {
				if( $revision->getExtension() ){
					$waveURL = file_get_contents($s3->get_object($this->container->getParameter('s3_bucket'),"Projects/".$project->getPublicId()."/image/".$submission->getUser()->getPublicId()."/".$revision->getRevisionID().(($i == $len-1) ? '.svg' : '-small.svg'), array("preauth"=>strtotime("+1 hour"))));
					$songURL = $s3->get_object($this->container->getParameter('s3_bucket'),"Projects/".$project->getPublicId()."/audio/".$submission->getUser()->getPublicId()."/".$revision->getRevisionID().'.'.$revision->getExtension(),array("preauth"=>strtotime("+1 hour")));
				}
				else {
					$waveURL = '/uploads/waveforms/default.svg';
					$songURL = '/uploads/audio/default.m4a';
				}

				$waveThreads = array();
				foreach ($revision->getWaveThreads() as $thread) {
					$waveComments = array();
					$read = true;
					foreach ($thread->getComments() as $comment) {
						$replies = array();
						foreach ($comment->getReplies() as $reply) {
							$replies[] = array(
								"id" => $reply->getCommentID(),
								"msg" => $reply->getText(),
								"profileUrl" => $this->get('router')->generate('userProfile', array('publicId' => $reply->getUser()->getPublicId())),
								"from" => $reply->getUser()->getFullname(),
								"picture" => $s3->get_object($this->container->getParameter('s3_bucket'),$reply->getUser()->getPicture(),array("preauth"=>strtotime("+1 hour"))),
								"replies" => array(),
								"parent" => $comment->getCommentID()
							);
						}
						$waveComments[] = array(
							"id" => $comment->getCommentID(),
							"msg" => $comment->getText(),
							"profileUrl" => $this->get('router')->generate('userProfile', array('publicId' => $comment->getUser()->getPublicId())),
							"from" => $comment->getUser()->getFullname(),
							"picture" => $s3->get_object($this->container->getParameter('s3_bucket'),$comment->getUser()->getPicture(),array("preauth"=>strtotime("+1 hour"))),
							"replies" => $replies,
							"parent" => false
						);
						//if there are other users commented to this thread it should be unread
						if(!$user->equals($comment->getUser()))
							$read = false;

					}
					$waveThreads[] = array(
						"id" => $thread->getThreadID(),
						"comments" => $waveComments,
						"time" => $thread->getTime(),
						"read" => $read ? $read : $thread->getRead($user->getId())
					);
				}

				$teamComments = array();
				foreach ($revision->getTeamComments() as $comment) {
					$replies = array();
					foreach ($comment->getReplies() as $reply) {
						$replies[] = array(
							"id" => $reply->getCommentID(),
							"msg" => $reply->getText(),
							"profileUrl" => $this->container->get('router')->generate('userProfile', array('publicId' => $reply->getUser()->getPublicId())),
							"from" => $reply->getUser()->getFullname(),
							"picture" => $s3->get_object($this->container->getParameter('s3_bucket'),$reply->getUser()->getPicture(),array("preauth"=>strtotime("+1 hour")))
						);
					}

					$teamComments[] = array(
						"id" => $comment->getCommentID(),
						"msg" => $comment->getText(),
						"profileUrl" => $this->container->get('router')->generate('userProfile', array('publicId' => $comment->getUser()->getPublicId())),
						"from" => $comment->getUser()->getFullname(),
						"picture" => $s3->get_object($this->container->getParameter('s3_bucket'),$comment->getUser()->getPicture(),array("preauth"=>strtotime("+1 hour"))),
						"replies" => $replies
					);
				}

				array_push( $revs, array(
					"id" => $revision->getRevisionID(),
					"duration" => $revision->getDuration(),
					"title" => $revision->getTitle(),
					"artist" => $submission->getUser()->getFullname(),
					"artistUrl" => $this->container->get('router')->generate('userProfile', array('publicId' => $submission->getUser()->getPublicId())),
					"avgRating" => $revision->getAvgRating(),
					"userRating" => $revision->getRating( $user )/10,
					"date" => date_format( $revision->getDate(), 'Y/m/d' ),
					"waveThreads" => $waveThreads,
					"teamComments" => $teamComments,
					"songURL" => $songURL,
					"waveURL" => $waveURL
				));
			}

			array_push($results, array(
				//'submissionID' => $submission->getSubmissionID(),
				//'secureID' => md5($submission->getSubmissionID()),
				'publicId' => $submission->getPublicID(),
				'role' => $role,
				'revisions' => array_reverse( $revs )
			));
		}

		//Return the results
		$response = array(
			"msg" => "ok",
			"content" => $results
		);

		return new Response( json_encode($response) );
	}

	public function projectOwnerPeopleAction(){
		$type = $_POST['type'];
		$session = $this->getRequest()->getSession();
		$dm = $this->get('doctrine_mongodb')->getManager();
		$project = $dm->find('SooundAppBundle:Project', $session->get('project') );

		//Depending on the type of people required; ie team, members, watchers
		//return different data

		if($type === 'creatives'){
			$users = $project->getMembers();
		}
		else if($type === "followers"){
			$users = $project->getFollowers();
		}
		else if($type === "team"){
			$users = $project->getTeam();
			$pendingTeam = $project->getPendingTeam();
		}

		$results = array();
        $s3 = $this->get('aws_s3');
		foreach ($users as $user) {
			array_push( $results, array(
				'publicId' => $user->getPublicId(),
				"picture" => $s3->get_object($this->container->getParameter('s3_bucket'),$user->getPicture(),array("preauth"=>strtotime("+1 hour"))),
				"name" => $user->getFullname(),
				"email" => $user->getEmail()
			));
		}

		$response = array(
			"msg" => "ok",
			"content" => $results
		);

		if($type === "team")
			$response['pendingTeam'] = $pendingTeam;

		return new Response( json_encode($response) );
	}

	public function rejectSubmissionAction(){
		$dm = $this->get('doctrine_mongodb')->getManager();
		$session = $this->getRequest()->getSession();
		$submission = $dm->getRepository('SooundAppBundle:Submission')->findOneBy(array('publicID' => $_POST['submission']));
		$submission->setRejected(true);

		$project = $dm->find('SooundAppBundle:Project', $session->get('project') );
		$project->removeSubmission( $submission );


		$dm->flush();

		$activity = array(
			'to' => $submission->getUser(),
			'type' => 'envelope',
			'date' => date_format( date_create(), "m-d-Y" ),
			'private' => true, //Optional, defaults to false
			'content' => array(
				0 => array(
					'ref' => $project,
					'text' => ':'
				),
				1 => array(
					'ref' => $this->user(),
					'text' => ' rejected your submission'
				)
			)
		);
		$this->get('soound_app.activity')->send($activity);

		$this->get('activity_mail_user')->sendEmail( 
				$submission->getUser(),  //To
				'creative-reject-submission', //Type
				array(					//Data
					'project' => $project,
					'submission' => $submission
				)
			);

		$response = array(
			"msg" => "ok"
		);

		return new Response(json_encode($response));
	}

	public function winnerSubmissionAction(){
		$dm = $this->get('doctrine_mongodb')->getManager();
		$session = $this->getRequest()->getSession();

		$submission = $dm->getRepository('SooundAppBundle:Submission')->findOneBy(array('publicID' => $_POST['submission']));
		//$submission = $dm->find('SooundAppBundle:Submission', $_POST['submission']);

		$project = $dm->find('SooundAppBundle:Project', $session->get('project') );
		//Close project and select this submission as winner
		$project->setWinner($submission);
/*
		//Create the braintree transaction, putting money into escrow
		$factory = $this->get('comet_cult_braintree.factory');
		$transactionFactory = $factory->get('transaction');

		$transaction = $transactionFactory::find($project->getTransactionId());
		$cc = $transaction->creditCardDetails->token;
		$amount = $transaction->amount;
		$result = $transactionFactory::void($project->getTransactionId());

		$result = $transactionFactory::sale(array(
			'merchantAccountId' => $project->getWinner()->getUser()->getId(),
			'amount' => $amount,
			'paymentMethodToken' => $cc,
            'customerId' => $project->getUser()->getId(),
			'serviceFeeAmount' => $project->getBudget() * .15, //15% service fee
			'options' => array(
				'submitForSettlement' => true,
				'holdInEscrow' => true
			)
		));


		$dm->flush();
*/
		$activity = array(
			'to' => $submission->getUser(),
			'type' => 'envelope',
			'date' => date_format( date_create(), "m-d-Y" ),
			'private' => true, //Optional, defaults to false
			'content' => array(
				0 => array(
					'ref' => $project,
					'text' => ':'
				),
				1 => array(
					'ref' => $this->user(),
					'text' => ' picked your submission as winner'
				)
			)
		);
		$this->get('soound_app.activity')->send($activity);
		$activity['content'][1]['text']=' picked a winner';
		foreach ($project->getFollowers() as $key => $user) {
			$activity['to']=$user->getId();
			$this->get('soound_app.activity')->send($activity);
		}

		$this->get('activity_mail_user')->sendEmail( 
			$submission->getUser(),  //To
			'creative-submission-accepted-rejected', //Type
			array(					//Data
				'merge_vars'=> array(
					'projectname'=>$project->getProjectname(),
					'userwhowonproject'=> $user->getFullname(),
					'totalnumberofsubmissions' => sizeof($project->getSubmissions()),
					'commentdatetimestamp' => date("m-d-Y H:i:s"),
					'copyrightyear' => date("Y"),
					'projectlink'=>$this->generateUrl('submit', array('publicId' => $project->getPublicId()))
				),
				'template' => 'ProjectNotificationWinner'
			)
		);

		$response = array(
			"msg" => "ok"
		);

		return new Response(json_encode($response));
	}

	public function winningSubmissionAction(){
		$session = $this->getRequest()->getSession();
		$dm = $this->get('doctrine_mongodb')->getManager();
		$project = $dm->find('SooundAppBundle:Project', $session->get('project'));

		if($project->hasWinner()){
			$winner = $project->getWinner();

			$revisions = array();
			$winnerRevisions = $winner->getRevisions();
			$len = sizeof($winnerRevisions);

			foreach ($winnerRevisions as $i=>$rev) {
				$revisions[] = array(
					"id" => 'w'.$rev->getRevisionID(),
					"duration" => $rev->getDuration(),
					"title" => $rev->getTitle(),
					"artist" => $winner->getUser()->getFullname(),
					"artistUrl" => $this->container->get('router')->generate('userProfile', array('publicId' => $winner->getUser()->getPublicId())),
					"avgRating" => $rev->getAvgRating(),
					"userRating" => $rev->getRating( $this->user() )/10,
					"date" => date_format( $rev->getDate(), 'Y/m/d' ),
					"waveURL" => file_get_contents($s3->get_object($this->container->getParameter('s3_bucket'),"Projects/".$project->getPublicId()."/image/".$winner->getUser()->getPublicId()."/".$rev->getRevisionID().(($i == $len-1) ? '.svg' : '-small.svg'), array("preauth"=>strtotime("+1 hour")))),
					"songURL" => $s3->get_object($this->container->getParameter('s3_bucket'),"Projects/".$project->getPublicId()."/audio/".$winner->getUser()->getPublicId()."/".$rev->getRevisionID().'.'.$rev->getExtension(),array("preauth"=>strtotime("+1 hour")))
				);
			}

			$response = array(
				"status" => "ok",
				"publicId" => $winner->getPublicID(),
				"revisions" => $revisions
			);

			return new Response( json_encode($response) );
		}
		else {
			return new Response( json_encode(array(
				"status" => "error"
			)));
		}
	}

	public function downloadHQFileAction($fileId){
		$session = $this->getRequest()->getSession();
		$dm = $this->get('doctrine_mongodb')->getManager();
		$project = $dm->find('SooundAppBundle:Project', $session->get('project'));

		if( $this->user()->ownsProject($session->get('project'))){

			$hqFile = $dm->find('SooundAppBundle:HQFile', $fileId);
			$hqFile->setDownloadDate( date_create() );
			$hqFile->setStatus("downloaded");
			$dm->flush();

			$content = file_get_contents($s3->get_object($this->container->getParameter('s3_bucket'),"Projects/".$project->getPublicId()."/exchange/".$submission->getUser()->getPublicId()."/".$hqFile->getHqFileID() . '.' . $hqFile->getExtension(), array("preauth"=>strtotime("+1 hour"))));

			$response = new Response();
			$response->headers->set('Content-Type', 'audio/'+$hqFile->getExtension());
			$response->headers->set('Content-Disposition', 'attachment; filename="'.$hqFile->getName().'.'.$hqFile->getExtension().'"');
			$response->setContent($content);

			return $response;

		} else {
			$response = array("msg" => "error");
			return new Response( json_encode($response) );
		}
	}

	public function sendComplaintAction(){
		$session = $this->getRequest()->getSession();
		$dm = $this->get('doctrine_mongodb')->getManager();
		$project = $dm->find('SooundAppBundle:Project', $session->get('project'));

		if( $this->user()->ownsProject($session->get('project'))){
			$project->setComplaint( $_POST['complaint'] );
			$project->setComplaintResolved(false);

			$dm->flush();

			$activity = array(
				'to' => $project->getWinner()->getUser(),
				'type' => 'envelope',
				'date' => date_format( date_create(), "m-d-Y" ),
				'private' => true, //Optional, defaults to false
				'content' => array(
					0 => array(
						'ref' => $project,
						'text' => ':'
					),
					1 => array(
						'ref' => $this->user(),
						'text' => ' has an issue with one of your files.'
					)
				)
			);
			$this->get('soound_app.activity')->send($activity);

			$this->get('activity_mail_user')->sendEmail( 
				$project->getWinner()->getUser(),  //To
				'creative-new-complaint', //Type
				array(					//Data
					'from' => $project->getUser(),
					'project' => $project,
					'submission' => $project->getWinner()
				)
			);

			$response = array("msg" => "ok");
			return new Response( json_encode($response) );
		} else {
			$response = array("msg" => "error");
			return new Response( json_encode($response) );
		}
	}

	public function acceptFilesAction(){
		$session = $this->getRequest()->getSession();
		$dm = $this->get('doctrine_mongodb')->getManager();
		$project = $dm->find('SooundAppBundle:Project', $session->get('project'));

		if( $this->user()->ownsProject($session->get('project'))){
			$project->setComplaintResolved(true); //Just incase it was false

			//Run the braintree transaction here
/*
			$factory = $this->get('comet_cult_braintree.factory');

			$transactionFactory = $factory->get('transaction');

			$result = $transactionFactory::releaseFromEscrow($project->getTransactionId());

			$project->setTransactionId( $result->id );
*/
			$project->setPayed(true);
			$transaction = new Transaction();
			$transaction->setProject($project);
			$transaction->setAmount($project->getBudget());
			$transaction->setDate( date_create() );
			//Set the to user
			$transaction->setToUser($project->getWinner()->getUser());
			$transaction->addUser($project->getWinner()->getUser());
			$project->getWinner()->getUser()->addTotalEarned( $project->getBudget() - ($project->getBudget() * .15) );
			//Set the from user
			$transaction->setFromUser($project->getUser());
			$transaction->addUser($project->getUser());
			$project->getUser()->addTotalSpent( floatval($transaction->getAmount()) );
			$dm->persist($transaction);

			$dm->flush();

			$activity = array(
				'to' => $project->getWinner()->getUser(),
				'type' => 'envelope',
				'date' => date_format( date_create(), "m-d-Y" ),
				'private' => true, //Optional, defaults to false
				'content' => array(
					0 => array(
						'ref' => $project,
						'text' => ':'
					),
					1 => array(
						'ref' => $this->user(),
						'text' => ' has an accepted your files!'
					)
				)
			);
			$this->get('soound_app.activity')->send($activity);
			//Send Receipt to both parties
			$this->get('activity_mail_user')->sendReceipt(
				$project->getWinner()->getUser(), 
				$project->getUser(),
				$transaction->getAmount(),
				$project
			);

			return new Response( "ok" );
		} else {
			return new Response( "error" );
		}
	}

	public function uploadReferenceAction(){
        if(isset($_FILES['file'])){
            $name = $_FILES['file']['name'];
            $fileSize = $_FILES['file']['size'];
            $uploadedFile = $_FILES['file']['tmp_name'];
        }

        $maxSize = 5;//In MB
        $allowedExts = array("wav", "m4a", "mp3", "aac");
        $path_parts = pathinfo($name);
        $extension = $path_parts['extension'];
        $filename = $path_parts['filename'];

        $allowed = false;

        //Check file type and size
        if( in_array($extension, $allowedExts)){
            if( $fileSize < $maxSize*1000000){
                $allowed = true;
            }
        }

        if($allowed){
            $session = $this->getRequest()->getSession();
            $files =  $session->get('project_files');

            $base = $this->get('kernel')->getRootDir() . '/../web/uploads/';
            $ffmpeg = $this->get('soound_app.ffmpeg');
            $audioPath = $base . 'references/audio/' . $filename . '.' . $extension;

            //Store the file in uploaded songs
            move_uploaded_file($uploadedFile, $audioPath);
            $metadata = $ffmpeg->getMetadata($audioPath);
            $waveFormPath = $base.'references/waveforms/'.$filename.'.svg';
            $ffmpeg->saveWaveform($audioPath, $waveFormPath, null);
            $files["references"][] = array("extension"=>$extension ,"audio"=>$audioPath, "waveform"=>$waveFormPath, "duration"=>$metadata['duration'], "title"=>$metadata['title']);

            $session->set('project_files', $files);

            $response = array(
                "msg" => "ok",
                "filename" => $name
            );
        }
        else {
            $response = array(
                "msg" => "error"
            );
        }

        return new Response( json_encode($response));
	}

	public function uploadFileAction(){
        if(isset($_FILES['file'])){
            $name = $_FILES['file']['name'];
            $fileSize = $_FILES['file']['size'];
            $uploadedFile = $_FILES['file']['tmp_name'];
        }

        $maxSize = 5;//In MB
        $allowedExts = array("wav", "m4a", "mp3", "aac");
        $path_parts = pathinfo($name);
        $extension = $path_parts['extension'];
        $filename = $path_parts['filename'];

        $allowed = false;

        //Check file type and size
        if( $fileSize < $maxSize*1000000){
            $allowed = true;
        }

        if($allowed){
            $session = $this->getRequest()->getSession();
            $files =  $session->get('project_files');

            $base = $this->get('kernel')->getRootDir() . '/../web/uploads/';
            $ffmpeg = $this->get('soound_app.ffmpeg');
            $filePath = $base . 'references/audio/' . $filename . '.' . $extension;
            move_uploaded_file($uploadedFile, $filePath);
			if( in_array($extension, $allowedExts)){
  	            $metadata = $ffmpeg->getMetadata($filePath);
	            $waveFormPath = $base.'references/waveforms/'.$filename.'.svg';
	            $ffmpeg->saveWaveform($filePath, $waveFormPath, null);
	            $files["files"][] = array("extension"=>$extension ,"audio"=>$filePath, "waveform"=>$waveFormPath, "duration"=>$metadata['duration'], "title"=>$metadata['title']);
			}
			else{
				$files["files"][] = array("extension"=>$extension, "file"=>$filePath);
			}

            $session->set('project_files', $files);

            $response = array(
                "msg" => "ok",
                "filename" => $name
            );
        }
        else {
            $response = array(
                "msg" => "error"
            );
        }

        return new Response( json_encode($response));
	}
}
