<?php

namespace Soound\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Soound\AppBundle\Document\Project;
use Soound\AppBundle\Document\Submission;
use Soound\AppBundle\Document\Revision;
use Soound\AppBundle\Document\HQFile;
use Soound\AppBundle\Document\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class SubmitController extends Controller
{

	public function indexAction($publicId){
		if(!$this->get('security.context')->isGranted('ROLE_USER')){
			return $this->redirect(
				$this->generateUrl(
					'browsePublicProject',
					array(
						'publicId' => $publicId
					)
				)
			);
		}
		$session = $this->getRequest()->getSession();
		$session->set('userID', $this->user()->getId());

		$dm = $this->get('doctrine_mongodb')->getManager();

		$project = $dm->getRepository('SooundAppBundle:Project')->findOneBy(array('publicId' => $publicId));
		$session->set('project', $project->getProjectId());

		$poster = $project->getUser();

		$user = $this->user();

		//Project Poster and Team may not submit to their own project
		if( $user->getId() === $poster->getId() || $user->inGroup($project->getTeam()) ){
			return $this->redirect(
				$this->generateUrl(
					'projectSubmissions',
					array(
						'publicId' => $publicId
					)
				)
			);
		} //Non-members of a project cannot submit to it
		else if( !($user->inGroup($project->getMembers()) ) ){
			return $this->redirect(
				$this->generateUrl(
					'browsePublicProject',
					array(
						'publicId' => $publicId
					)
				)
			);
		}

		//$this->testRealtimeSubmission($project);
		//$this->testSubmission();
        $s3 = $this->get('aws_s3');

		$references = array();
		foreach ($project->getReferences() as $ref) {
			$reference=array("link"=>$ref->getLink(),"description"=>$ref->getDescription(),"isAudio"=>$ref->isAudio(),"extension"=>$ref->getExtension(),"id"=>$ref->getReferenceId(),"title"=>$ref->getTitle(),"duration"=>$ref->getDuration());
			if($reference['isAudio']){
				$reference["audioURL"] = $s3->get_object($this->container->getParameter('s3_bucket'),"Projects/".$project->getPublicId()."/reference/audio/".$ref->getReferenceId().'.'.$ref->getExtension(),array("preauth"=>strtotime("+1 hour")));
				$reference["waveURL"] = file_get_contents($s3->get_object($this->container->getParameter('s3_bucket'),"Projects/".$project->getPublicId()."/reference/image/".$ref->getReferenceId().'.svg', array("preauth"=>strtotime("+1 hour"))));
			}
			$references[]=$reference;
		}
		
		$projectTwig = array(
			'projectPicture' => $project->getProjectExt() != "" ? ($s3->get_object($this->container->getParameter('s3_bucket'),"Projects/".$project->getPublicId()."/image/".$project->getPublicId()."-300.".$project->getProjectExt(),array("preauth"=>strtotime("+1 hour")))) : "/uploads/projectPics/project_pic_small.png",
			"projectDescription" => $project->getProjectDetails(),
			"projectTitle" => $project->getProjectName(),
			"projectType" => $project->getProjectType(),
			"projectGenres" => implode(", ", $project->getProjectGenre() ),
			"projectBudget" => $project->getBudget(),
			"projectEntries" => count( $project->getSubmissions() ),
			"projectDeadline" => $project->getDueDate()->diff( date_create() )->format('%a'),
			"posterName" => $poster->getFullname(),
			"submissions" => count( $project->getSubmissions() ),
			"watchers" => count( $project->getFollowers() ),
			"members" => count( $project->getMembers() ),
			"hasWinner" => $project->hasWinner(),
			"isWinner" => $project->isWinner($user),
			"alreadySubmitted" => $project->inSubmissions($user),
			"references" => $references,
			"projectDetails" => $this->getProjectDetails($project)
		);

		if( $projectTwig['isWinner'] ){
			if( !$project->getComplaintResolved() ){
				$projectTwig["complaint"] = $project->getComplaint();
			}
		}
		else{
			$projectTwig["daysLeft"] = $project->getDueDate()->diff(date_create())->format('%a');
			$projectTwig["daysTotal"] = $project->getDueDate()->diff($project->getPublishDate())->format('%a');
			$projectTwig["daysPercent"] = round((($projectTwig["daysTotal"]-$projectTwig["daysLeft"])/$projectTwig["daysTotal"])*100);
		}
		/*
		if( $projectTwig["isWinner"] ){
			if( $project->hasHQFiles() ){
				$projectTwig["hqFiles"] = array();
				foreach ($project->getHQFiles() as $file) {
					$projectTwig["hqFiles"][] = array(
						"accepted" => $file->getApproved(),
						"name" => $file->getName(),
						"extension" => $file->getExtension(),
						"downloaded" => $file->getDownloadDate() ? $file->getDownloadDate()->format('m-d-Y') : false
					);
				}
			}
		}
		*/

		return $this->render(
			"SooundAppBundle:Html:submit.html.twig",
			$projectTwig
		);
	}

	private function testRealtimeSubmission($project){
		$rev = array(
			"id" => '1234567890',
			"duration" => '1000',
			"title" => 'Test Title',
			"artist" => 'Test Artist',
			"artistUrl" => '#',
			"avgRating" => 0,
			"userRating" => 0,
			"date" => '2014/2/19',
			"waveThreads" => array(),
			"teamComments" => array(),
			"waveURL" => '/uploads/waveforms/default.svg',
			"songURL" => '/uploads/audio/default.mp4'
        );

		//Send a realtime update
		$this->get('soound_app.realtime.submission')->sendSubmission(array(
			"project" => $project,
			"content" => array(
				"publicId" => '1234567890',
				"revision" => $rev
			)
		));

		sleep(3);
		$this->testRealtimeRevision('1234567890');
	}

	private function testRealtimeRevision($submissionId){
		$rev = array(
			"id" => '0987654321',
			"duration" => '1000',
			"title" => 'Test Revision Title',
			"artist" => 'Test Revision Artist',
			"artistUrl" => '#',
			"avgRating" => 0,
			"userRating" => 0,
			"date" => '2014/2/20',
			"waveThreads" => array(),
			"teamComments" => array(),
			"waveURL" => '/uploads/waveforms/default.svg',
			"songURL" => '/uploads/audio/default.mp4'
        );

        //Send a realtime revision
        $this->get('soound_app.realtime.submission')->sendRevision(array(
        	"submission" => $submissionId,
        	"revision" => $rev
        ));
	}

	private function testSubmission(){
		$session = $this->getRequest()->getSession();
		$dm = $this->get('doctrine_mongodb')->getManager();
		$project = $dm->find('SooundAppBundle:Project', $session->get('project'));

		$revision = new Revision();
		$revision->setArtist($this->user()->getFullName());
		$revision->setTitle("Test Revision");
		$revision->setDuration(30000);

		$submission = new Submission();
		$submission->addRevision($revision);
		$revision->setSubmission($submission);
		$submission->setUser( $this->user() );
		$submission->setProject( $project );

		$dm->persist($revision);
		$dm->persist($submission);
		$dm->flush();
	}

	private function user(){
		$user = $this->get('security.context')->getToken()->getUser();
		return $user;
	}

	private function getProjectDetails($project) {
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
		return $details;
	}
	public function getSubmissionsAction(){
		$session = $this->getRequest()->getSession();

		$dm = $this->get('doctrine_mongodb')->getManager();
		$s3 = $this->get('aws_s3');
		$user = $this->user();

        $query = $dm->createQueryBuilder('SooundAppBundle:Submission')
        			->field('user')->equals( $user->getId() )
        			->field('project')->equals( $session->get('project') )
        			->getQuery()
        			->execute();
		$project = $dm->find('SooundAppBundle:Project', $session->get('project'));

		$results = array();
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
								"from" => $reply->getUser()->getFullname(),
								"profileUrl" => $this->get('router')->generate('userProfile', array('publicId' => $reply->getUser()->getPublicId())),
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

				array_push( $revs, array(
					"id" => $revision->getRevisionID(),
					"duration" => $revision->getDuration(),
					"title" => $revision->getTitle(),
					"artist" => $user->getFullname(),
					"artistUrl" => $this->container->get('router')->generate('userProfile', array('publicId' => $user->getPublicId())),
					"avgRating" => $revision->getAvgRating(),
					"userRating" => $revision->getRating( $user )/10,
					"date" => date_format( $revision->getDate(), 'Y/m/d' ),
					"waveThreads" => $waveThreads,
					"songURL" => $songURL,
					"waveURL" => $waveURL
				));
			}

			array_push($results, array(
				'publicId' => $submission->getPublicID(),
				'role' => "song owner",
				'revisions' => array_reverse( $revs )
			));
		}

		$response = array(
			"msg" => "ok",
			"content" => $results
		);

		return new Response( json_encode($response) );
	}

	public function getWinningSubmissionAction(){
		$session = $this->getRequest()->getSession();
		$user = $this->user();
		$dm = $this->get('doctrine_mongodb')->getManager();
		$project = $dm->find('SooundAppBundle:Project', $session->get('project'));

		if($project->hasWinner() && $project->isWinner($this->user())){
			$winner = $project->getWinner();

			$revisions = array();
			$winnerRevisions = $winner->getRevisions();
			$len = sizeof($winnerRevisions);

			foreach ($winnerRevisions as $i => $rev) {
				$revisions[] = array(
					"id" => $rev->getRevisionID(),
					"duration" => $rev->getDuration(),
					"title" => $rev->getTitle(),
					"artist" => $user->getFullname(),
					"artistUrl" => $this->container->get('router')->generate('userProfile', array('publicId' => $user->getPublicId())),
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

	public function uploadSubmissionAction(Request $request){

		if (isset($_FILES['file'])){
		    $name = $_FILES['file']['name'];
		    $fileSize = $_FILES['file']['size'];
		    $uploadedFile = $_FILES['file']['tmp_name'];
		}

		$maxSize = 10;//In MB
		$allowedExts = array("wav", "m4a", "mp3", "aac");
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
			$session = $this->getRequest()->getSession();
			$dm = $this->get('doctrine_mongodb')->getManager();
			$s3 = $this->get('aws_s3');

			$revision = new Revision();
			//Set some song details here...
			$dm->persist($revision);
			$dm->flush();

			$base = $this->get('kernel')->getRootDir() . '/../web/uploads/';
			$audioPath = $base . 'audio/' . $revision->getRevisionID() . '.' . $extension;

			//Store the file in uploaded songs
			move_uploaded_file($uploadedFile, $audioPath);


			//Return the waveform and the id of the song created
			$ffmpeg = $this->get('soound_app.ffmpeg');
			$project = $dm->find('SooundAppBundle:Project', $session->get('project'));

			$metadata = $ffmpeg->getMetadata($audioPath);
			$waveFormPath = $base.'waveforms/'.$revision->getRevisionID().'.svg';
			$s3Path = "Projects/".$project->getPublicId()."/image/".$this->user()->getPublicId()."/".$revision->getRevisionID();
			$ffmpeg->saveWaveform($audioPath, $waveFormPath, $s3Path);

			$revision->setArtist($metadata['artist']);
			$revision->setTitle(substr($metadata['title'], 0, 25));
			$revision->setDuration($metadata['duration']);
			$revision->setExtension($extension);

			$s3->create_object($this->container->getParameter('s3_bucket'),"Projects/".$project->getPublicId()."/audio/".$this->user()->getPublicId()."/".$revision->getRevisionID().".".$extension,array('fileUpload' => $audioPath));
			//remove the audio file after we are done with it
			@unlink($audioPath);

			$submission = new Submission();
			$submission->addRevision($revision);
			$revision->setSubmission($submission);
			$submission->setUser( $this->user() );
			$submission->setProject( $project );
			$dm->persist($submission);

			$dm->flush();

            //Activity email
            //$email = $project->getEmailAddress();
            //$this->get('activity_mail_user')->activityUserEmail('Upload new Submission',$project->getUsername(), 'Thanks, you uoload Submission correctly!',$email);

            $rev = array(
				"id" => $revision->getRevisionID(),
				"duration" => $revision->getDuration(),
				"title" => $revision->getTitle(),
				"artist" => $this->user()->getFullname(),
				"artistUrl" => $this->container->get('router')->generate('userProfile', array('publicId' => $this->user()->getPublicId())),
				"avgRating" => 0,
				"userRating" => 0,
				"date" => date_format( $revision->getDate(), 'Y/m/d' ),
				"waveThreads" => array(),
				"songURL" => $s3->get_object($this->container->getParameter('s3_bucket'),"Projects/".$project->getPublicId()."/audio/".$this->user()->getPublicId()."/".$revision->getRevisionID().'.'.$revision->getExtension(),array("preauth"=>strtotime("+1 hour"))),
				"waveURL" => file_get_contents($s3->get_object($this->container->getParameter('s3_bucket'),"Projects/".$project->getPublicId()."/image/".$this->user()->getPublicId()."/".$revision->getRevisionID().'.svg', array("preauth"=>strtotime("+1 hour"))))
            );

			$activity = array(
				'to' => $project->getUser(),
				'type' => 'envelope',
				'date' => date_format( date_create(), "Y/m/d" ),
				'private' => ($project->getIsFeatured()=='private' ? true : false), //Optional, defaults to false
				'content' => array(
					0 => array(
						'ref' => $project,
						'text' => ' project'
					),
					1 => array(
						'text' => $revision->getTitle().' was submitted by ',
					),
					2 => array(
						'ref' => $this->user()
					)
				)
			);	

			$this->get('soound_app.activity')->send($activity);
			//Send a realtime update
			$this->get('soound_app.realtime.submission')->sendSubmission(array(
				"project" => $project,
				"content" => array(
					"publicId" => $submission->getPublicID(),
					"revision" => $rev
				)
			));


        	$url = $this->generateUrl('projectSubmissions', 
        				array('publicId' => $project->getPublicId()), true).'?sub='.$submission->getPublicID();
			$this->get('activity_mail_user')->sendEmail( 
				$project->getUser(),  //To
				'owner-new-submission-revision', //Type
				array(					//Data
					'merge_vars'=> array(
						'projectname'=>$project->getProjectname(),
						'submitter'=> $this->user()->getFullname(),
						'submissionorrevisionbody' => 'submission',
						'submissionorrevisionbutton' => 'SUBMISSION',
						'submissiondatetimestamp' => date_format( $revision->getDate(), "m/d/Y H:i:s" ),
						'copyrightyear' => date("Y"),
						'projectsubmissionlink' => $url
					),
					'template' => 'ProjectNotificationSubmissions'
				)
			);

            $response = array(
            	"msg" => "ok",
            	"content" => array(
            		//'submissionID' => $submission->getSubmissionID(),
					'publicId' => $submission->getPublicID(),
					'revision' => $rev
            	)
            );

			//JSON Encode response
			return new Response(json_encode($response));
		}
		else {
			//Return an appropriate error message
			//Prepare the response
			$response = array(
				"msg" => "error"
			);
			//JSON Encode response
			return new Response(json_encode($response));
		}

	}

	public function uploadRevisionAction(){
		$headers = getallheaders();
		$pubId = $headers["submission"];


		if (isset($_FILES['file'])){
		    $name = $_FILES['file']['name'];
		    $fileSize = $_FILES['file']['size'];
		    $uploadedFile = $_FILES['file']['tmp_name'];
		}

		$maxSize = 10;//In MB
		$allowedExts = array("wav", "m4a", "mp3", "aac");
		$temp = explode(".", $name);
		$extension = end($temp);

		$allowed = false;

		//Check file type and size
		if( in_array($extension, $allowedExts) && $pubId){
			if( $fileSize < $maxSize*1000000){
				$allowed = true;
			}
		}

		if($allowed){
			$dm = $this->get('doctrine_mongodb')->getManager();
			$s3 = $this->get('aws_s3');

			$revision = new Revision();
			//Set some song details here...
			$dm->persist($revision);
			$dm->flush();

			$base = $this->get('kernel')->getRootDir() . '/../web/uploads/';
			$audioPath = $base . 'audio/' . $revision->getRevisionID() . '.' . $extension;

			//Store the file in uploaded songs
			move_uploaded_file($uploadedFile, $audioPath);


			//Return the waveform and the id of the song created
			$ffmpeg = $this->get('soound_app.ffmpeg');
            $session = $this->getRequest()->getSession();
            $project = $dm->find('SooundAppBundle:Project', $session->get('project'));

			$metadata = $ffmpeg->getMetadata($audioPath);
			$waveFormPath = $base.'waveforms/'.$revision->getRevisionID().'.svg';
			$s3Path = "Projects/".$project->getPublicId()."/image/".$this->user()->getPublicId()."/".$revision->getRevisionID();
			$ffmpeg->saveWaveform($audioPath, $waveFormPath, $s3Path);
			$revision->setArtist($metadata['artist']);

			$metadata['title']=substr($metadata['title'], 0, 25);

			$submission = $dm->getRepository('SooundAppBundle:Submission')->findOneBy(array('publicID' => $pubId));

			$lastRevision = $submission->getLastRevision();
			$oldTitle = $lastRevision->getTitle();
			$title = "";
			if($oldTitle==$metadata['title']){
				$title = $metadata['title'].' v2';
			}
			else if(preg_match("/[v]\d\d/", substr($oldTitle, -3), $matches) && substr($oldTitle, 0, strlen($oldTitle)-4) == $metadata['title']){
				$title = $metadata['title'].' v'.(filter_var($matches[0], FILTER_SANITIZE_NUMBER_INT)+1);
			}
			else if(preg_match("/[v]\d/", substr($oldTitle, -2), $matches) && substr($oldTitle, 0, strlen($oldTitle)-3) == $metadata['title']){
				$title = $metadata['title'].' v'.(filter_var($matches[0], FILTER_SANITIZE_NUMBER_INT)+1);
			}
			else
				$title = $metadata['title'];

			$revision->setTitle($title);
			$revision->setDuration($metadata['duration']);
			$revision->setExtension($extension);
			//$song->setUserId();            //Set to the currently logged in user's ID

			//$submission = $dm->find('SooundAppBundle:Submission', $subID);
			$submission->addRevision($revision);
			$revision->setSubmission($submission);
			$submission->setLastRevision($revision);
			$dm->persist($revision);
			$dm->persist($submission);
			$dm->flush();

			$s3->create_object($this->container->getParameter('s3_bucket'),"Projects/".$project->getPublicId()."/audio/".$this->user()->getPublicId()."/".$revision->getRevisionID().".".$extension,array('fileUpload' => $audioPath));
			//remove the audio file after we are done with it
			@unlink($audioPath);

			$rev = array(
				"id" => $revision->getRevisionID(),
				"duration" => $revision->getDuration(),
				"title" => $revision->getTitle(),
				"artist" => $this->user()->getFullname(),
				"artistUrl" =>  $this->container->get('router')->generate('userProfile', array('publicId' => $this->user()->getPublicId())),
				"avgRating" => 0,
				"userRating" => 0,
				"date" => date_format( $revision->getDate(), 'Y/m/d' ),
				"waveThreads" => array(),
				"songURL" => $s3->get_object($this->container->getParameter('s3_bucket'),"Projects/".$project->getPublicId()."/audio/".$this->user()->getPublicId()."/".$revision->getRevisionID().'.'.$revision->getExtension(),array("preauth"=>strtotime("+1 hour"))),
				"waveURL" => file_get_contents($s3->get_object($this->container->getParameter('s3_bucket'),"Projects/".$project->getPublicId()."/image/".$this->user()->getPublicId()."/".$revision->getRevisionID().'.svg', array("preauth"=>strtotime("+1 hour"))))
            );
			/*
            //Activity email

            
            $email = $owner->getEmail();
            $this->get('activity_mail_user')->activityUserEmail('Upload new Submission',$owner->getUsername(), 'Thanks, you uoload Submission correctly!',$email);
			*/

			/************************/
			/*
			$revision = array(
				"id" => '1002032324424345',
				"duration" => 123456,
				"userRating" => 0,
				"avgRating" => 0,
				"waveURL" => '../uploads/waveforms/default.svg',
				"songURL" => '../uploads/audio/default.m4a',
				"title" => "New Revision",
				"artist" => "Same Artist",
				"waveThreads" => [],
				"teamComments" => [],
				"date" => "Just now"
			);
			*/

			$activity = array(
				'to' => $project->getUser(),
				'type' => 'envelope',
				'date' => date_format( date_create(), "Y/m/d" ),
				'private' => ($project->getIsFeatured()=='private' ? true : false), //Optional, defaults to false
				'content' => array(
					0 => array(
						'ref' => $project,
						'text' => ' project'
					),
					1 => array(
						'text' => $revision->getTitle().' was submitted by ',
					),
					2 => array(
						'ref' => $this->user()
					)
				)
			);	
			$this->get('soound_app.activity')->send($activity);

			//Send a realtime update
			$this->get('soound_app.realtime.submission')->sendRevision(array(
				"submission" => $submission,
				"revision" => $rev
			));

        	$url = $this->generateUrl('projectSubmissions', 
        				array('publicId' => $project->getPublicId()), true).'?sub='.$submission->getPublicID();
			$this->get('activity_mail_user')->sendEmail( 
				$project->getUser(),  //To
				'owner-new-submission-revision', //Type
				array(					//Data
					'merge_vars'=> array(
						'projectname'=>$project->getProjectname(),
						'submitter'=> $this->user()->getFullname(),
						'submissionorrevisionbody' => 'revision',
						'submissionorrevisionbutton' => 'REVISION',
						'submissiondatetimestamp' => date_format( $submission->getLastRevision()->getDate(), "m/d/Y H:i:s" ),
						'copyrightyear' => date("Y"),
						'projectsubmissionlink' => $url
					),
					'template' => 'ProjectNotificationSubmissions'
				)
			);

			$response = array(
				"msg" => "ok",
				"content" => $rev
			);

			return new Response( json_encode($response));
		}
		else {
			//Return an appropriate error message
			//Prepare the response
			$response = array(
				"msg" => "error"
			);
			//JSON Encode response
			return new Response(json_encode($response));
		}
	}

	public function removeSubmissionAction(){
		//Permanently delete submission and all associated files
		$user = $this->user();
		$dm = $this->get('doctrine_mongodb')->getManager();
		$session = $this->getRequest()->getSession();
		//$submission = $dm->find('SooundAppBundle:Submission', $_POST['submission']);
		$submission = $dm->getRepository('SooundAppBundle:Submission')->findOneBy(array('publicID' => $_POST['submission']));
		$project = $dm->find('SooundAppBundle:Project', $session->get('project') );
		$project->removeSubmission( $submission );
		$user->removeSubmission( $submission );

		$revisions = $submission->getRevisions();
		$base = $this->get('kernel')->getRootDir() . '/../web/uploads/';

		foreach ($revisions as $revision) {
			//Remove all files associated with revision and delete document
			unlink( $base.'audio/'. $revision->getRevisionID().'.'.$revision->getExtension() );
			unlink( $base.'waveforms/'.$revision->getRevisionID().'.svg');
			$dm->remove($revision);
		}

		$dm->remove($submission);
		$dm->flush();

		$activity = array(
			'to' => $project->getUser(),
			'type' => 'envelope',
			'date' => date_format( date_create(), "m-d-Y" ),
			'private' => false,//$project->getIsPrivate(), //Optional, defaults to false
			'content' => array(
				0 => array(
					'ref' => $project,
					'text' => ' project'
				),
				1 => array(
					'text' => $submission->getLastRevision()->getTitle().' was removed by ',
				),
				2 => array(
					'ref' => $this->user()
				)
			)
		);
		$this->get('soound_app.activity')->send($activity);

		$this->get('activity_mail_user')->sendEmail( 
				$project->getUser(),  //To
				'owner-removed-submission', //Type
				array(					//Data
					'from' => $this->user(),
					'project' => $project,
					'submission' => $revision
				)
			);

		$response = array(
			"msg" => "ok"
		);

		return new Response(json_encode($response));
	}

	public function uploadHQFileAction(){

		if (isset($_FILES['file'])){
		    $name = $_FILES['file']['name'];
		    $fileSize = $_FILES['file']['size'];
		    $uploadedFile = $_FILES['file']['tmp_name'];
		}

		$maxSize = 50;//In MB
		$allowedExts = array("wav", "m4a", "mp3", "aac", "flac");
		$temp = explode(".", $name);
		$extension = end($temp);

		$allowed = false;

		//Check file type and size
		if( in_array($extension, $allowedExts)){
			if( $fileSize < $maxSize*5000000){
				$allowed = true;
			}
		}

		if($allowed){
			$session = $this->getRequest()->getSession();
			$dm = $this->get('doctrine_mongodb')->getManager();
			$s3 = $this->get('aws_s3');
			$project = $dm->find('SooundAppBundle:Project', $session->get('project'));

			/*
			//For testing so I don't have to delete stuff...
			$response = array(
				"id" => 'identeatdadfadsfds',
            	"msg" => "ok",
        		"name" => $temp[0],
        		"extension" => $extension
            );

			//JSON Encode response
			return new Response(json_encode($response));
			*/
			//Create HQFile with some details about it
			$hqFile = new HQFile();
			$hqFile->setName($temp[0]);
			$hqFile->setExtension($extension);
			$hqFile->setUser( $this->user() );
			$hqFile->setProject( $project );
			$project->addHQFile($hqFile);
			$dm->persist($hqFile);
			$dm->flush();

			//Upload the hq file to the S3 bucket
			$s3->create_object($this->container->getParameter('s3_bucket'),"Projects/".$project->getPublicId()."/exchange/".$this->user()->getPublicId()."/".$hqFile->getHqFileID() . '.' . $extension,array('fileUpload' => $uploadedFile));

			$activity = array(
				'to' => $project->getUser(),
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
						'text' => ' submitted a HQ file for your approval.'
					)
				)
			);	

			$this->get('soound_app.activity')->send($activity);

			$this->get('activity_mail_user')->sendEmail( 
				$project->getUser(),  //To
				'owner-uploaded-hqFile', //Type
				array(					//Data
					'from' => $this->user(),
					'project' => $project,
					'hqFile' => $hqFile
				)
			);

            $response = array(
            	"msg" => "ok",
            	"id" => $hqFile->getHqFileID(),
        		"name" => $hqFile->getName(),
        		"extension" => $hqFile->getExtension()
            );

			//JSON Encode response
			return new Response(json_encode($response));
		}
		else {
			//Return an appropriate error message
			//Prepare the response
			$response = array(
				"msg" => "error"
			);
			//JSON Encode response
			return new Response(json_encode($response));
		}

	}

	public function getUploadedHqFilesAction(){
		$session = $this->getRequest()->getSession();
		$dm = $this->get('doctrine_mongodb')->getManager();
		$project = $dm->find('SooundAppBundle:Project', $session->get('project'));

		$files = array();

		foreach ($project->getHQFiles() as $file) {
			$files[] = array(
				"id" => $file->getHqFileID(),
				"name" => $file->getName(),
				"extension" => $file->getExtension(),
				"status" => $file->getStatus(),
			);
		}

		return new Response( json_encode($files) );
	}

	public function complaintFixedAction(){
		$session = $this->getRequest()->getSession();
		$dm = $this->get('doctrine_mongodb')->getManager();
		$project = $dm->find('SooundAppBundle:Project', $session->get('project'));

		$project->setComplaintResolved(true);
		$dm->flush();

		$activity = array(
			'to' => $project->getUser(),
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
					'text' => ' fixed an issue regard their HQ files.'
				)
			)
		);
		$this->get('soound_app.activity')->send($activity);

		$this->get('activity_mail_user')->sendEmail( 
				$project->getUser(),  //To
				'owner-complaint-fixed', //Type
				array(					//Data
					'from' => $this->user(),
					'project' => $project
				)
			);

		return new Response( json_encode(array("msg" => "ok")));
	}

}
