<?php

namespace Soound\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Soound\AppBundle\Document\Project;
use Soound\AppBundle\Document\Activity;
use Soound\AppBundle\Document\ActivityContent;
use Soound\AppBundle\Document\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 *
 */
class ActivityController extends Controller
{
	/**
	 * [indexAction description]
	 * @param  [type] $filename [description]
	 * @return [type]           [description]
	 */
	public function indexAction(){
		$startDate = date_create()->sub(new \DateInterval("P1M"));
		$endDate = date_create();
		$user = $this->user();

		//$this->testZMQ();

		//$this->createTestActivity();

		return $this->render(
			"SooundAppBundle:Html:activity.html.twig",
			array(
				"days" => $this->getActivityBetween($startDate, $endDate),
				"start" => date_format($startDate, "n / j / Y"),
				"end" => date_format($endDate, "n / j / Y")
			)
		);

	}

	private function user(){
		$user = $this->get('security.context')->getToken()->getUser();
		return $user;
	}

	private function createTestActivity(){
		$dm = $this->get('doctrine_mongodb')->getManager();

		$read = false;

		$user = $this->user();

		$activity = new Activity();
		$activity->setRead($read);
		$activity->setUser($user);
		$activity->setType("envelope");

		$con = new ActivityContent();
		$con->setRef( $dm->find('SooundAppBundle:Project', '524f52df359035f81a000012') );
		$con->setText(': ');
		$activity->addContent($con);
		$dm->persist($con);

		$con = new ActivityContent();
		$con->setRef( $user );
		$con->setText(' invited you to his private project.');
		$activity->addContent($con);
		$dm->persist($con);

		$this->user()->addActivity($activity);

		$dm->persist($activity);
		$dm->flush();
	}

	private function getActivityBetween( $startDate, $endDate ){

		$dm = $this->get('doctrine_mongodb')->getManager();
		$user = $this->user();

		$query = $user->getActivity();

		//Now ensure activity falls between date range
		$accepted = array();

		foreach ($query as $activity) {
			if( $activity->getDate() >= $startDate && $activity->getDate() <= $endDate){
				$accepted[] = $activity;
			}
		}

		$count = 0;
		$days = array();
		$currDay = array("date" => "0", "activities" => array());

		foreach ($accepted as $activity) {

			$date = date_format( $activity->getDate(), "F jS, 'y" );

			$content = array();

			foreach ($activity->getContent() as $con) {
				$content[] = array(
					"ref" => $con->hasRef() ? $con->getRefDetails() : false,
					"text" => $con->hasText() ? $con->getText() : false
				);
			}

			$activity = array(
				"time" => date_format( $activity->getDate(), "g:ha" ),
				"icon" => $activity->getType(),
				"read" => $activity->getRead(),
				"content" => $content
			);

			if( $date != $currDay["date"] ){
				if( $count != 0 ){
					array_push( $days, $currDay );
					$count = 0;
				}
				$currDay = array( "date" => $date, "activities" => array() );
			}
			array_push( $currDay["activities"], $activity );
			$count++;
		}

		if( $count > 0 )
			array_push( $days, $currDay );

		return $days;
	}

	public function getActivityAction(){
		$startDate = date_create( $_POST['startDate'] );
		$endDate = date_create( $_POST['endDate'].' 23:59:59' );
		$activity = $this->getActivityBetween( $startDate, $endDate );

		return new Response( json_encode($activity) );
	}

	public function dismissActivityAction(){
		//Dismiss all unread activity
		
		$user = $this->user();
		foreach ($user->getUnreadActivity() as $activity) {
			$activity->setRead(true);
		}
		$this->get('doctrine_mongodb')->getManager()->flush();
		
		return new Response( json_encode( array("msg" => "ok") ) );
	}

	private function testZMQ(){

		//This is an example notification being sent:
		$activity = array(
			'to' => $this->user(),
			'type' => 'envelope',
			'date' => date_format( date_create(), "m-d-Y" ),
			'private' => false, //Optional, defaults to false
			'content' => array(
				0 => array(
					'ref' => $this->user(),
					'text' => 'invited you to this private project.'
				)
			)
		);

		$this->get('soound_app.activity')->send($activity);
	}

}
