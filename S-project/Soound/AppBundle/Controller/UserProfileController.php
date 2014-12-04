<?php

namespace Soound\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Soound\AppBundle\Document\Project;
use Soound\AppBundle\Document\Submission;
use Soound\AppBundle\Document\Revision;
use Soound\AppBundle\Document\Rating;
use Soound\AppBundle\Document\Score;
use Soound\AppBundle\Document\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use PayPal\Service\AdaptiveAccountsService;
use PayPal\Types\AA\AccountIdentifierType;
use PayPal\Types\AA\GetVerifiedStatusRequest;

class UserProfileController extends Controller
{

	public function indexAction($publicId){
        $session = $this->getRequest()->getSession();

        if($session->has('redirectTo')){
            $redirectTo = $session->get('redirectTo');
            $session->remove('redirectTo');
            return $this->redirect($redirectTo);
        }else{

        	$dm = $this->get('doctrine_mongodb')->getManager();
	        $s3 = $this->get('aws_s3');
	        $user = $dm->getRepository('SooundAppBundle:User')->findOneBy(array('publicId' => $publicId));

	        //Manually sort open and closed projects
            $owned = $user->getOwnedProjects();
            $open = array();
            $closed = array();
            $now = date_create();
            foreach ($owned as $project) {
                if($project->getEndingOn() > $now && $project->getPayed()===false)
                    array_push( $open, $project );
                else
                    array_push( $closed, $project );
            }
            /*
            $walkthrough = true;
            if($user->hasSeenWalkthrough('profile')){
            	$walkthrough = false;
            }
            */
            $walkthrough = false;

            return $this->render(
                "SooundAppBundle:Html:user-profile.html.twig", array(
                    "fullName" => $user->getFullName(),
                    "publicId" => $user->getPublicId(),
                    "userPic" => $s3->get_object($this->container->getParameter('s3_bucket'),$user->getPicture(),array("preauth"=>strtotime("+1 hour"))),
                    "userLocation" => $user->getLocation(),
                    "userTypes" => implode( ", ", $user->getUserTypes() ),
                    "numCredits" => count( $user->getUploadedCredits() ),
                    "numWinner" => count( $user->getSooundCredits() ),
                    "numOpen" => count( $open ),
                    "numClosed" => count( $closed ),
                    "numMemberOf" => count( $user->getMemberOfProjects() ),
                    "numFollowing" => count( $user->getFollowingProjects() ),
                    "walkthrough" => $walkthrough
                )
            );
        }

	}

	private function user(){
		$user = $this->get('security.context')->getToken()->getUser();
		return $user;
	}

	public function goToProjectAction(){
		$projectID = $_POST['projectID'];

		$session = $this->getRequest()->getSession();

		$session->set('project', $projectID);

		$dm = $this->get('doctrine_mongodb')->getManager();

		if( $this->user()->ownsProject($projectID) )
			return new Response(json_encode([ "url"=>$this->generateUrl('projectSubmissions', array("publicId" => $_POST["projectID"])) ]));
		else
			return new Response(json_encode([ "url"=>$this->generateUrl('submit', array("publicId" => $_POST["projectID"])) ]));
	}

	private function projectInfo($project){
		$s3 = $this->get('aws_s3');
		return array(
			'id' => $project->getPublicId(),
			'title' => $project->getProjectName(),
			'description' => $project->getProjectDetails(),
			'genre' => $project->getProjectGenre(),
			'type' => $project->getProjectType(),
			'numEntries' => count( $project->getSubmissions() ),
			'followers' => count( $project->getFollowers() ),
			'budget' => $project->getBudget(),
			'endingOn' => $project->getDueDate(),
			'daysLeft' => $project->getDueDate()->diff( date_create() )->format('%a'),
			'picture' => $project->getProjectExt() != "" ? ($s3->get_object($this->container->getParameter('s3_bucket'),"Projects/".$project->getPublicId()."/image/".$project->getPublicId()."-300.".$project->getProjectExt(),array("preauth"=>strtotime("+1 hour")))) : "/uploads/projectPics/project_pic_small.png"
		);
	}

	public function getCreditsAction(){
		$howMany = $_POST['howMany'];
		$offset = $_POST['offset'];

		$session = $this->getRequest()->getSession();
		$dm = $this->get('doctrine_mongodb')->getManager();
		$s3 = $this->get('aws_s3');

        $user = $dm->getRepository('SooundAppBundle:User')->findOneBy(array('publicId' => $_POST['publicId']));


		$credits = $user->getUploadedCredits();
		$results = array();

		foreach ($credits as $credit) {
			array_push( $results, array(
				"id" => $credit->getCreditID(),
				"waveURL" => file_get_contents($s3->get_object($this->container->getParameter('s3_bucket'),"Users/".$user->getPublicId()."/images/waveform/".$credit->getCreditID().'.svg', array("preauth"=>strtotime("+1 hour")))),
				"audioURL" => $s3->get_object($this->container->getParameter('s3_bucket'),"Users/".$user->getPublicId()."/audio/".$credit->getCreditID().'.'.$credit->getExtension(),array("preauth"=>strtotime("+1 hour"))),
				"title" => $credit->getTitle(),
				"duration" => $credit->getDuration(),
				"description" => $credit->getDescription(),
				"roles" => $credit->getRoles()
			) );
		}

		//Return the results
		$response = array(
			"msg" => "ok",
			"content" => $results
		);

		return new Response( json_encode($response) );
	}

	public function getWinnerProjectsAction(){
		$howMany = $_POST['howMany'];
		$offset = $_POST['offset'];

		$session = $this->getRequest()->getSession();
		$dm = $this->get('doctrine_mongodb')->getManager();

        $user = $dm->getRepository('SooundAppBundle:User')->findOneBy(array('publicId' => $_POST['publicId']));

		$won = $user->getWon();
		$results = array();

		foreach ($won as $project) {
			array_push( $results, $this->projectInfo($project) );
		}

		//Return the results
		$response = array(
			"msg" => "ok",
			"content" => $results
		);

		return new Response( json_encode($response) );
	}

	public function getOpenProjectsAction(){
		$howMany = $_POST['howMany'];
		$offset = $_POST['offset'];

		$session = $this->getRequest()->getSession();
		$dm = $this->get('doctrine_mongodb')->getManager();

        $user = $dm->getRepository('SooundAppBundle:User')->findOneBy(array('publicId' => $_POST['publicId']));

		//Add in skip and howMany....
		$owned = $user->getOwnedProjects();
        $results = array();
        $now = date_create();
        foreach ($owned as $project) {
        	if($project->getEndingOn() > $now)
        		array_push( $results, $this->projectInfo($project) );
        }

		//Return the results
		$response = array(
			"msg" => "ok",
			"content" => $results
		);

		return new Response( json_encode($response) );
	}

	public function getClosedProjectsAction(){
		$howMany = $_POST['howMany'];
		$offset = $_POST['offset'];

		$session = $this->getRequest()->getSession();
		$dm = $this->get('doctrine_mongodb')->getManager();

        $user = $dm->getRepository('SooundAppBundle:User')->findOneBy(array('publicId' => $_POST['publicId']));

		//Add in skip and howMany....
		$owned = $user->getOwnedProjects();
        $results = array();
        $now = date_create();
        foreach ($owned as $project) {
        	if($project->getEndingOn() <= $now)
        		array_push( $results, $this->projectInfo($project) );
        }

		//Return the results
		$response = array(
			"msg" => "ok",
			"content" => $results
		);

		return new Response( json_encode($response) );
	}

	public function getFollowingProjectsAction(){
		$howMany = $_POST['howMany'];
		$offset = $_POST['offset'];

		//$session = $this->getRequest()->getSession();
		$dm = $this->get('doctrine_mongodb')->getManager();

        $user = $dm->getRepository('SooundAppBundle:User')->findOneBy(array('publicId' => $_POST['publicId']));
		$following = $user->getFollowingProjects();

		$results = array();
		foreach ($following as $project) {
			array_push( $results, $this->projectInfo($project) );
		}

		//Return the results
		$response = array(
			"msg" => "ok",
			"content" => $results
		);

		return new Response( json_encode($response) );
	}

	public function getMemberOfProjectsAction(){
		$howMany = $_POST['howMany'];
		$offset = $_POST['offset'];

		$session = $this->getRequest()->getSession();
		$dm = $this->get('doctrine_mongodb')->getManager();

        $user = $dm->getRepository('SooundAppBundle:User')->findOneBy(array('publicId' => $_POST['publicId']));
		$memberOf = $user->getMemberOfProjects();

		$results = array();
		foreach ($memberOf as $project) {
			array_push( $results, $this->projectInfo($project) );
		}

		//Return the results
		$response = array(
			"msg" => "ok",
			"content" => $results
		);

		return new Response( json_encode($response) );
	}
	public function accountSettingsAction(){
		$user = $this->user();
		/*
		$notifications = array();

		foreach ($user->getNotificationPreferences() as $type => $val) {
			$notifications[] = array(
				"type" => $type,
				"val" => $val
			);
		}
		*/
		return $this->render(
			"SooundAppBundle:Html:account-settings.html.twig",
			array(
				"notifications" => $user->getNotificationPreferences(),
				"startBirthYear" => date('Y')-10,
				"endBirthYear" => date('Y')-80,
				"notificationPreferences" => $user->getNotificationPreferences(),
				"fullName" => $user->getFullname(),
				"location" => $user->getLocation(),
				"paypalVerified"=> $user->getPaypalVerified(),
				"paypalEmail" => $user->getPaypalEmail(),
				"paypalFirstName" => $user->getPaypalFirstName(),
				"paypalLastName" => $user->getPaypalLastName(),
				"url" => $user->getUrl()
			)
		);

	}
	public function saveBasicAccountSettingsAction(){
		$user = $this->user();
		$url = "";

		if($user->getFullname()=='New User' && $_POST['fullName']!=='New User')
			$url = $this->container->get('router')->generate('browse');

		if(!empty($_POST['location']))
			$user->setLocation($_POST['location']);
		if(!empty($_POST['fullName']))
			$user->setFullname($_POST['fullName']);
		if(!empty($_POST['url']))
			$user->setUrl($_POST['url']);

        $userManager = $this->container->get('fos_user.user_manager');
        $userManager->updateUser($user);

		$activity = array(
			'to' => $user,
			'type' => 'envelope',
			'date' => date_format( date_create(), "m-d-Y" ),
			'private' => true, //Optional, defaults to false
			'content' => array(
				0 => array(
					'text' => 'Basic information updated'
				)
			)
		);
		$this->get('soound_app.activity')->send($activity);

		$response = array(
			"msg" => "ok",
			"url" => $url
		);

		return new Response(json_encode($response));
	}
	public function changePasswordAction(){
		if(!empty($_POST['newPassword']) && $_POST['newPassword']==$_POST['newPasswordAgain']){
			$user = $this->user();
			$encoder = $this->get('security.encoder_factory')->getEncoder($user);
			$encodedPass = $encoder->encodePassword($_POST['currentPassword'], $user->getSalt());
			if($encodedPass == $user->getPassword()){
				$newPassword = $encoder->encodePassword($_POST['newPassword'], $user->getSalt());
				$user->setPassword($newPassword);

				$userManager = $this->container->get('fos_user.user_manager');
		        $userManager->updateUser($user);
				$response = "ok";

				$activity = array(
					'to' => $user,
					'type' => 'envelope',
					'date' => date_format( date_create(), "m-d-Y" ),
					'private' => true, //Optional, defaults to false
					'content' => array(
						0 => array(
							'text' => 'Password updated'
						)
					)
				);
				$this->get('soound_app.activity')->send($activity);
			}
			else 
				$response = "That's not your current password.";
		}
		else
			$response = "Password not changed.";

		return new Response($response);

	}
	public function saveAvatarAction(){

		if (isset($_FILES['file'])){
		    $name = $_FILES['file']['name'];
		    $fileSize = $_FILES['file']['size'];
		    $uploadedFile = $_FILES['file']['tmp_name'];
		}

		$maxSize = 5;//In MB
		$allowedExts = array("jpg", "jpeg", "png", "gif");
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
			$dimensions = array( 50, 180 );
			$user = $this->user();
			$session = $this->getRequest()->getSession();

			$base = $this->get('kernel')->getRootDir() . '/../web/uploads/';
			$picturePath = $base . 'userPics/' . $user->getPublicId() . '.' . $extension;
            $s3 = $this->get('aws_s3');
            $s3->create_object($this->container->getParameter('s3_bucket'),"Users/".$user->getPublicId()."/images/".$name,array('fileUpload' => $_FILES['file']['tmp_name']));
            
            foreach ($dimensions as $dim) {
                $this->square_crop($uploadedFile, $base.'userPics/'.$user->getPublicId().'-'.$dim.'.'.$extension, $dim, 100);
	            $s3->create_object($this->container->getParameter('s3_bucket'),"Users/".$user->getPublicId()."/images/".$filename.'-'.$dim.'.'.$extension,array('fileUpload' => $base.'userPics/'.$user->getPublicId().'-'.$dim.'.'.$extension));
	            unlink($base.'userPics/'.$user->getPublicId().'-'.$dim.'.'.$extension);
            }


            $user->setPictureName($filename);
			$user->setPictureExt($extension);
	        $userManager = $this->container->get('fos_user.user_manager');
	        $userManager->updateUser($user);

            $response = array(
            	"msg" => "ok",
            	"url" => $s3->get_object($this->container->getParameter('s3_bucket'),$user->getPicture(50),array("preauth"=>strtotime("+1 month")))
            	//we are adding time to force refresh the picture
            );

            $session->set('userPicture', $response['url']);

			$activity = array(
				'to' => $user,
				'type' => 'envelope',
				'date' => date_format( date_create(), "m-d-Y" ),
				'private' => true, //Optional, defaults to false
				'content' => array(
					0 => array(
						'text' => 'Account picture updated'
					)
				)
			);
			$this->get('soound_app.activity')->send($activity);
			
		}
		else {
			$response = array(
				"msg" => "error"
			);
		}
		return new Response(json_encode($response));
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

	public function saveEmailAction(){
		$user = $this->user();
		$encoder = $this->get('security.encoder_factory')->getEncoder($user);
		$encodedPass = $encoder->encodePassword($_POST['currentPassword'], $user->getSalt());
		if($encodedPass == $user->getPassword()){
	        $userManager = $this->container->get('fos_user.user_manager');
	        $user->setEmail($_POST['email']);
	        $userManager->updateUser($user);

		    $response = array(
		    	"msg" => "ok"
		    );

			$activity = array(
				'to' => $user,
				'type' => 'envelope',
				'date' => date_format( date_create(), "m-d-Y" ),
				'private' => true, //Optional, defaults to false
				'content' => array(
					0 => array(
						'text' => 'Email updated'
					)
				)
			);
			$this->get('soound_app.activity')->send($activity);
		}
		else{
		    $response = array(
		    	"msg" => "error"
		    );
		    /*
			$activity = array(
				'to' => $user,
				'type' => 'envelope',
				'date' => date_format( date_create(), "m-d-Y" ),
				'private' => true, //Optional, defaults to false
				'content' => array(
					0 => array(
						'ref' => '',
						'text' => "You've entered wrong password"
					)
				)
			);
			*/
		}
		

		return new Response(json_encode($response));
	}

	public function saveNotificationPreferencesAction(){
		$user = $this->user();
		if(isset($_POST['type'])){
			$user->changeNotificationPreference($_POST['type']);
			$this->get('doctrine_mongodb')->getManager()->flush();
		    $response = array(
		    	"msg" => "ok"
		    );
		    /*
		    //It's a little much to update the user everytime they check or uncheck a box
			$activity = array(
				'to' => $user,
				'type' => 'envelope',
				'date' => date_format( date_create(), "m-d-Y" ),
				'private' => true, //Optional, defaults to false
				'content' => array(
					0 => array(
						'text' => 'Notification preferences updated'
					)
				)
			);
			$this->get('soound_app.activity')->send($activity);
			*/
		}
		else{
		    $response = array(
		    	"msg" => "error"
		    );
		}
		return new Response(json_encode($response));
	}

	public function deleteAccountAction(){
		$user = $this->user();
    	$dm = $this->get('doctrine_mongodb')->getManager();
		$projects = $dm->getRepository('SooundAppBundle:Project')->findBy(array('user'=>$user->getId()));

		foreach ($projects as $project)
			$dm->remove($project);

		$submissions = $dm->getRepository('SooundAppBundle:Submission')->findBy(array('user'=>$user->getId()));

		foreach ($submissions as $submission) {
			$revisions = $submission->getRevisions();
			$base = $this->get('kernel')->getRootDir() . '/../web/uploads/';

			foreach ($revisions as $revision) {
				//Remove all files associated with revision and delete document
				unlink( $base.'audio/'. $revision->getRevisionID().'.'.$revision->getExtension() );
				unlink( $base.'waveforms/'.$revision->getRevisionID().'.svg');
				$dm->remove($revision);
			}
			$dm->remove($submission);
		}

		$activities = $dm->getRepository('SooundAppBundle:Activity')->findBy(array('user.id'=>$user->getId()));
		foreach ($activities as $activity)
			$dm->remove($activity);

		$dm->flush();

		$userManager = $this->container->get('fos_user.user_manager');
		$userManager->deleteUser($user);

		return	$this->redirect($this->generateUrl('fos_user_security_logout'));
	}

	public function linkBankAccountAction(){
		/*
		return new Response(json_encode(array(
			'routing' => $_POST['routing'],
			'account' => $_POST['account'],
			'street' => $_POST['street'],
			'zip' => $_POST['zipCode'],
			'city' => $_POST['city'],
			'state' => $_POST['state'],
			'birthMonth' => $_POST['birthMonth'],
			'birthYear' => $_POST['birthYear'],
			'birthDay' => $_POST['birthDay']
		)));
		*/
		$dm = $this->get('doctrine_mongodb')->getManager();
		$user = $this->user();
		$factory = $this->get('comet_cult_braintree.factory');

        $merchantAccountFactory = $factory->get('merchantAccount');

        $merchantAccount = $merchantAccountFactory::create(array(
        	'applicantDetails' => array(
        		'firstName' => $user->getFirstName(),
        		'lastName' => $user->getLastName(),
        		'email' => $user->getEmail(),
        		'address' => array(
        			'streetAddress' => $_POST['street'],
        			'postalCode' => $_POST['zipCode'],
        			'locality' => $_POST['city'],
        			'region' => $_POST['state']
        		),
        		'dateOfBirth' => $_POST['birthYear'].'-'.$_POST['birthMonth'].'-'.$_POST['birthDay'],
        		'routingNumber' => $_POST['routing'],
        		'accountNumber' => $_POST['account']
        	),
        	'tosAccepted' => true,
        	'masterMerchantAccountId' => 'gss4qsqd74g9g7ck'//Whatever our master merchant account Id is....
        ));

        return new Response();
	}

	public function linkPaypalAccountAction(){
		$user = $this->user();

		$getVerifiedStatus = new GetVerifiedStatusRequest();
		$accountIdentifier=new AccountIdentifierType();
		$accountIdentifier->emailAddress = trim($_POST['email']);
		$getVerifiedStatus->accountIdentifier=$accountIdentifier;
		$_POST['first']=trim($_POST['first']);
		$_POST['last']=trim($_POST['last']);
		$getVerifiedStatus->firstName = $_POST['first'];
		$getVerifiedStatus->lastName = $_POST['last'];
		$getVerifiedStatus->matchCriteria = 'NAME';


		$config = array(
				"mode" => "live",
				"acct1.UserName" => "payments_api1.soound.com",
				"acct1.Password" => "N8DR3SWPR72NUEAH",
				"acct1.Signature" => "AkjxMBwldvUy5r6vb8yEn.JqMIcLAcz2Siy.Kp1vuTfI0INhlXL2GBJq",
				"acct1.AppId" => "APP-28H66535UM604833A",				
		);
		$service  = new AdaptiveAccountsService($config);
		$user->setPaypalEmail($_POST['email']);
		$user->setPaypalFirstName($_POST['first']);
		$user->setPaypalLastName($_POST['last']);
		try {
			$response = $service->GetVerifiedStatus($getVerifiedStatus);
			if($response->error)
				echo "error";
			else{
				$user->setPaypalVerified(true);
				echo "success";
			}
		} catch(Exception $ex) {
			echo "error";
		} 
        $userManager = $this->container->get('fos_user.user_manager');
        $userManager->updateUser($user);
		exit;
	}

	public function subMerchantApprovedAction(){
		
		if(isset($_GET['bt_challenge'])){
			$factory = $this->get('comet_cult_braintree.factory');
			$webhookNotification = $factory->get('webhookNotification');
			$verify = $webhookNotification::verify( $_GET['bt_challenge'] );
			return new Response();
		}

		if(isset($_POST['bt_signature']) && isset($_POST['bt_payload'])){
			$btNotificaiton = $webhookNotification::parse(
				$_POST['bt_signature'],
				$_POST['bt_payload']
			);

			//Send a system notification to the user telling them that their bank
			//account has been approved
			$dm = $this->get('doctrine_mongodb')->getManager();
			$userId = $btNotification->merchantAccount->id;
			$user = $dm->find('SooundAppBundle:User', $userId);
			if($user){
				$user->setSubMerchantApproved(true);
				$activity = array(
					'to' => $user,
					'type' => 'money',
					'date' => date_format( date_create(), "m-d-Y" ),
					'private' => true, //Optional, defaults to false
					'content' => array(
						0 => array(
							'text' => 'Your bank account has been approved! You can now accept payments through Soound.'
						)
					)
				);
				$this->get('soound_app.activity')->send($activity);

				$this->get('activity_mail_user')->sendEmail( 
					$user,  //To
					'user-merchant-approved', //Type
					array(					//Data
					)
				);
			}
			
			return new Response();
		}
/*
		$response = new Response('Ok');
		$response->setStatusCode('200');
		
		return $response;
*/
	}

	public function subMerchantDeniedAction(){
		if(isset($_GET['bt_challenge'])){
			$factory = $this->get('comet_cult_braintree.factory');
			$webhookNotification = $factory->get('webhookNotification');
			$verify = $webhookNotification::verify( $_GET['bt_challenge'] );
			echo $verify;
			return new Response();
		}

		if(isset($_POST['bt_signature']) && isset($_POST['bt_payload'])){
			$notification = $webhookNotification::parse(
				$_POST['bt_signature'],
				$_POST['bt_payload']
			);

			//Send a system notification to the user telling the that their bank
			//account has been denied
			$dm = $this->get('doctrine_mongodb')->getManager();
			$userId = $btNotification->merchantAccount->id;
			$user = $dm->find('SooundAppBundle:User', $userId);
			if($user){
				$user->setSubMerchantApproved(false);
				$activity = array(
					'to' => $user,
					'type' => 'remove',
					'date' => date_format( date_create(), "m-d-Y" ),
					'private' => true, //Optional, defaults to false
					'content' => array(
						0 => array(
							'text' => "Your bank account was denied. Please review your account's payment settings, and try again."
						)
					)
				);
				$this->get('soound_app.activity')->send($activity);

				$this->get('activity_mail_user')->sendEmail( 
					$user,  //To
					'user-merchant-denied', //Type
					array(					//Data
					)
				);
			}

			return new Response();
		}
	}
}
