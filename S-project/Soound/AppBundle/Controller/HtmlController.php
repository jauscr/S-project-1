<?php

namespace Soound\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Soound\AppBundle\Document\Project;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\Response;



class HtmlController extends Controller
{
	private function isInList($val, $listPath){
		$handle = fopen($listPath, 'r');
		$isValid = false;
		while( ($buffer = fgets($handle)) !== false ){
			if(strpos($buffer, $val) !== false){
				$isValid = true;
				break;
			}
		}
		fclose($handle);
		return $isValid;
	}

	public function betaInviteAction(){

		$email = $_POST['email'];
		$email = filter_var($email, FILTER_VALIDATE_EMAIL);
		$response = 'Invalid Email';
		if( $email ){ //Add the email to the interest list
			$base = $this->get('kernel')->getRootDir() . '/../web/uploads/';
			if(!$this->isInList($email, $base.'interestList.txt')){
				file_put_contents($base.'interestList.txt', $email."\r\n" , FILE_APPEND);
				$response = 'Success';
			}
			else {
				$response = 'This email exists already';
			}
		}

		return new Response($response);
	}

	public function betaInviteCheckAction(Request $request){
		$base = $this->get('kernel')->getRootDir() . '/../web/uploads/';
		if($this->isInList( $_POST['email'], $base.'inviteList.txt')){
			$session = $request->getSession();
			$session->set('beta', 'BETA_ALLOWED');
			return $this->redirect(
				$this->generateUrl('index_display')
			);
		}
		else {
			return $this->redirect(
				$this->generateUrl(
					'beta'
				)
			);
		}
	}

	private function betaInviteFromList(){
		$base = $this->get('kernel')->getRootDir() . '/../web/uploads/';
		$emails = file($base.'inviteList2.txt', FILE_IGNORE_NEW_LINES);
		$userManager = $this->get('fos_user.user_manager');
		$encoderFactory = $this->get('security.encoder_factory');
		$length = count($emails);
		for($i=0; $i<$length; $i++){
			//For each email, create a user with username set to email, password set to something random
			$user = $userManager->createUser();
			//$password = substr( md5(uniqid(mt_rand(), true)), 0, 4);
			$password = "beta";
			$user->setEnabled(true);
			$user->setEmail($emails[$i]);
            $user->setFullname('New User');

            $encoder = $encoderFactory->getEncoder($user);
            $encodedPass = $encoder->encodePassword($password, $user->getSalt());
            $user->setPassword($encodedPass);

            $userManager->updateUser($user);

            //Send an email with this user's info
            $this->get('activity_mail_user')->sendBetaInvite($emails[$i], $password);
		}
	}

	public function launchBetaAction(){
		$user = $this->get('security.context')->getToken()->getUser();
		if($user->getEmail() === 'patrogizmo@gmail.com' || $user->getEmail() === 'fehimtabak@gmail.com'){ //I'm the only one allowed to do this...
			$this->betaInviteFromList();
		}
		return $this->redirect(
				$this->generateUrl(
					'beta'
				)
			);
		
	}

    public function displayIndexAction(){
        $session = $this->getRequest()->getSession();
        if($session->has('redirectTo')){
            $redirectTo = $session->get('redirectTo');
            $session->remove('redirectTo');
            return $this->redirect($redirectTo);
        }else{
			$startDate = date_create()->sub(new \DateInterval("P1M"));
			$endDate = date_create();

            return $this->render(
                "SooundAppBundle:Html:index.html.twig",
				array(
					"activities" => $this->getPublicActivityBetween($startDate, $endDate),
					"start" => date_format($startDate, "n / j / Y"),
					"end" => date_format($endDate, "n / j / Y")
				)
            );
        }
    }

	public function displayAction($page) {

		$currentUrl = $this->getRequest()->getUri();
		if ($page == "step1")
		{
			$this->check_stepOne();
		}
		return $this->render(
			"SooundAppBundle:Html:$page.html.twig"
		);
	}

	public function getPublicActivityBetween($startDate, $endDate){
		$dm = $this->get('doctrine_mongodb')->getManager();

		$in_range = array('$gte' => $startDate, '$lt' => $endDate);
        $activities_data = $dm->getRepository('SooundAppBundle:Activity')->findBy(array('date' => $in_range, 'private' => false), array("date"=>"desc"), 8);
        $activities = array();
		foreach ($activities_data as $activity) {
			$content = array();

			foreach ($activity->getContent() as $con) {
				$content[] = array(
					"ref" => $con->hasRef() ? $con->getRefDetails() : false,
					"text" => $con->hasText() ? $con->getText() : false
				);
			}

			$activity = array(
				"date" => date_format( $activity->getDate(), "m/d/Y" ),
				"type" => $activity->getType(),
				"read" => $activity->getRead(),
				"content" => $content
			);

			array_push($activities, $activity );
		}

        return $activities;
	}
}
