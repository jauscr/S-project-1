<?php

namespace Soound\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Soound\AppBundle\Document\User;
use Soound\AppBundle\Document\Credit;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * 
 */
class UploadCreditsController extends Controller
{

	public function indexAction(){

		$session = $this->getRequest()->getSession();

		$dm = $this->get('doctrine_mongodb')->getManager();

        $user = $this->user();

		return $this->render(
			"SooundAppBundle:Html:upload-credits.html.twig",
			array(
				"numCredits" => count( $user->getUploadedCredits() )
			)
		);
		
	}

	private function user(){
		$user = $this->get('security.context')->getToken()->getUser();
		return $user;
	}

	public function uploadAudioAction(){

		$session = $this->getRequest()->getSession();

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
			$dm = $this->get('doctrine_mongodb')->getManager();
			$s3 = $this->get('aws_s3');

			$credit = new Credit();
			$user = $this->user();
			$credit->setUser( $user );
			$credit->setDate( date_create() );
			$user->addUploadedCredit($credit);
			$dm->persist($credit);
			$dm->flush();

			$base = $this->get('kernel')->getRootDir() . '/../web/uploads/';
			$audioPath = $base.'audio/credits/'.$credit->getCreditID().'.'.$extension;

			//Store the file in uploaded songs
			move_uploaded_file($uploadedFile, $audioPath);

			//Return the waveform and the id of the song created
			$ffmpeg = $this->get('soound_app.ffmpeg');

			$metadata = $ffmpeg->getMetadata($audioPath);
			$waveFormPath = $base.'waveforms/credits/'.$credit->getCreditID().'.svg';
			$s3Path = "Users/".$user->getPublicId()."/images/waveform/".$credit->getCreditID();
			$ffmpeg->saveWaveform($audioPath, $waveFormPath, $s3Path, false);
			//last parameter is for not creating small version of svg

			$s3->create_object($this->container->getParameter('s3_bucket'),"Users/".$user->getPublicId()."/audio/".$credit->getCreditID().".".$extension,array('fileUpload' => $audioPath));
			//remove the audio file after we are done with it
			@unlink($audioPath);

			$credit->setDuration($metadata['duration']);
			$credit->setTitle($metadata['title']);
			$credit->setExtension($extension);
			
			$dm->flush();

			$session->set( 'credit', $credit->getCreditID() );

			//Prepare the response
			$response = array(
				"msg" => "ok", 
				"content" => array(
					'id' => $credit->getCreditID(),
					'title' => $metadata['title'],
					'duration' => $metadata['duration'],
				"waveURL" => file_get_contents($s3->get_object($this->container->getParameter('s3_bucket'),"Users/".$user->getPublicId()."/images/waveform/".$credit->getCreditID().'.svg', array("preauth"=>strtotime("+1 hour")))),
				"audioURL" => $s3->get_object($this->container->getParameter('s3_bucket'),"Users/".$user->getPublicId()."/audio/".$credit->getCreditID().'.'.$credit->getExtension(),array("preauth"=>strtotime("+1 hour"))),
				)
			);

            //Activity Email to user
            //$this->get('activity_mail_user')->activityUserEmail('Upload Credit',$user->getUsername(), 'Thanks, your upload was complete!',$user->getEmail());

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

	/**
	 * Deletes the requested credit (as posted by creditID) or the most recently 
	 * uploaded credit (as stored in session)
	 */
	public function deleteCreditAction(){
		$postedID = isset($_POST['creditID']) ? $_POST['creditID'] : false;
		$session = $this->getRequest()->getSession();

		if($postedID || $session->has('credit')){

			$dm = $this->get('doctrine_mongodb')->getManager();
			if($postedID){
				$credit = $dm->find('SooundAppBundle:Credit', $postedID);
			}
			else {
				$credit = $dm->find('SooundAppBundle:Credit', $session->get('credit'));
				$session->remove('credit');
			}

			$base = $this->get('kernel')->getRootDir().'/../web/uploads/';
			$audio = 'audio/credits/'.$credit->getCreditID().'.'.$credit->getExtension();
			$wave = 'waveforms/credits/'.$credit->getCreditID().'.svg';
			//Remove the audio and waveform files
			unlink($base.$audio);
			unlink($base.$wave);
			//Remove the database entry for the selected credit
			$dm->remove($credit);
			$dm->flush();

			return new Response( json_encode(["msg"=>true]));
		}
		else
			return new Response( json_encode(["msg"=>false]));
	}

	public function createCreditAction(){
		$description = $_POST['description'];
		$roles = $_POST['roles'];
		$session = $this->getRequest()->getSession();

		if($session->has('credit')){
			$dm = $this->get('doctrine_mongodb')->getManager();

			$credit = $dm->find('SooundAppBundle:Credit', $session->get('credit'));

			$credit->setDescription($description);
			$credit->setRoles($roles);
			$dm->flush($credit);

			return new Response( json_encode(["msg"=>true]));
		}
		else
			return new Response( json_encode(["msg"=>false]));
	}

	public function updateCreditAction(){
		$description = $_POST['description'];
		$roles = $_POST['roles'];

		$dm = $this->get('doctrine_mongodb')->getManager();

		$credit = $dm->find('SooundAppBundle:Credit', $_POST['creditID']);

		$credit->setDescription($description);
		$credit->setRoles($roles);
		$dm->flush($credit);

		return new Response( json_encode(["msg"=>true]));
	}

	public function pastCreditsAction(){
		$session = $this->getRequest()->getSession();
		$user = $this->user();
		$s3 = $this->get('aws_s3');
		$credits = $user->getUploadedCredits();

		$results = array();
		foreach ($credits as $credit) {
			array_push($results, array(
				"id" => $credit->getCreditID(),
				"waveURL" => file_get_contents($s3->get_object($this->container->getParameter('s3_bucket'),"Users/".$user->getPublicId()."/images/waveform/".$credit->getCreditID().'.svg', array("preauth"=>strtotime("+1 hour")))),
				"audioURL" => $s3->get_object($this->container->getParameter('s3_bucket'),"Users/".$user->getPublicId()."/audio/".$credit->getCreditID().'.'.$credit->getExtension(),array("preauth"=>strtotime("+1 hour"))),
				"title" => $credit->getTitle(),
				"duration" => $credit->getDuration(),
				"description" => $credit->getDescription(),
				"roles" => $credit->getRoles()
			));
		}

		$response = array(
			"msg" => "ok",
			"content" => $results
		);

		return new Response( json_encode($response) );
	}
}
