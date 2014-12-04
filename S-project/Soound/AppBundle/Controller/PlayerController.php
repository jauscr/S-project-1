<?php

namespace Soound\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Soound\AppBundle\Document\Rating;
use Soound\AppBundle\Document\Revision;
use Soound\AppBundle\Document\Comment;
use Soound\AppBundle\Document\Thread;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;


class PlayerController extends Controller
{
	/**
	 * [indexAction description]
	 * @param  [type] $filename [description]
	 * @return [type]           [description]
	 */
	public function indexAction(){
		return $this->render(
			"SooundAppBundle:Html:player.html.twig",
			array(
				"songsURL" => $this->get('kernel')->getRootDir() . '/../web/uploads/'
			)
		);
	}

	private function user(){
		$user = $this->get('security.context')->getToken()->getUser();
		return $user;
	}

	/**
	 * Return subset of songs defined by 'howMany' and 'offset'
	 * with projectID = 'projectID'
	 */
	public function getSongsByProjectAction(){

		$dm = $this->get('doctrine_mongodb')->getManager();
		$query = $dm->createQueryBuilder('SooundAppBundle:Revision');

		$results = $query->field('projectID')->equals($_POST['projectID'])
			->limit($_POST['howMany'])
			->skip($_POST['offset'])
			->getQuery()
			->toArray();

		$songs = $this->getSongInfo($results);
		
		$response = array("msg" => "ok", "content" => $songs );//Prepare the response
		return new Response(json_encode($response));//JSON Encode response
	}

	/**
	 * Rate song specified by songID via this user
	 */
	public function rateSongAction(){
		$revisionID = $_POST['revisionID'];
		$user = $this->user();
		$dm = $this->get('doctrine_mongodb')->getManager();
		$revision = $dm->find('SooundAppBundle:Revision', $revisionID);

		$rating = new Rating();
		$rating->setUser($user);
		$rating->setRevision( $revision );
		$rating->setScore( floatval($_POST['rating']) );
		
		$revision->addRating( $rating );
		$avgRating = $revision->getAvgRating();
		$dm->persist($rating);
		$dm->flush();

		$response = array("msg" => "ok", "avgRating" => $avgRating );//Prepare the response
		return new Response(json_encode($response));//JSON Encode response
	}

	/**
	 * Return details about a set of songs
	 */
	private function getSongInfo($results){
		$songInfo = array();
		foreach ($results as $result) {
			array_push($songInfo, array(
				'id' => $result->getId(),
				'extension' => $result->getExtension(),
				'title' => $result->getTitle(),
				'artist' => $result->getArtist(),
				'ownerComments' => $result->getOwnerComments(),
				'avgRating' => $result->getAvgRating(),
				'userRating' => $result->getUserRating( $this->userID() )
			));
		}
		return $songInfo;
	}

	public function commentAction(Request $request){
		$param = $request->request->all();
		$user = $this->user();
        $s3 = $this->get('aws_s3');

		$send = array();
		foreach ($param as $key => $value) {
			if($value=="false"){
				$send[$key] = false;
				$param[$key] = false;
			}
			else
				$send[$key] = $value;
		}
		$send['from'] = $user->getFullname();
		$send['picture'] = $s3->get_object($this->container->getParameter('s3_bucket'),$user->getPicture(),array("preauth"=>strtotime("+1 hour")));

		//STORE
		$zmq = $this->get('soound_app.zmq.client');
		$dm = $this->get('doctrine_mongodb')->getManager();

		switch ($param['type']) {
			case 'team':
				$comment = new Comment();
				$comment->setUser($user);
				$comment->setText($param['msg']);
				$dm->persist($comment);

				$revision = $dm->find('SooundAppBundle:Revision', $param['to']);
				$submission = $revision->getSubmission();
				$project = $submission->getProject();
				if($param['comment'] === 'new')
					$revision->addTeamComment($comment);
				else
					$revision->addTeamReply($comment, 'new');

				$send['id'] = $comment->getCommentID();
				$dm->flush();
				$zmq->send(array(
					'type' => 'comment',
					'subType' => $param['type'],
					'content' => $send
				));
				break;
			
			case 'wave':
				$comment = new Comment();
				$comment->setUser($user);
				$comment->setText($param['msg']);
				$dm->persist($comment);

				switch ($param['thread']) {
					case 'new':
						$thread = new Thread();
						$thread->addComment($comment);
						$thread->setTime($param['time']);
						$dm->persist($thread);

						$revision = $dm->find('SooundAppBundle:Revision', $param['to']);
						$revision->addWaveThread($thread);
						$thread->setRevision($revision);
						$submission = $revision->getSubmission();
						$project = $submission->getProject();

						$send['commentId'] = $comment->getCommentID();
						$send['id'] = $thread->getThreadID();
						$send['fromId'] = $user->getPublicId();
						break;
					
					default:
						$thread = $dm->find('SooundAppBundle:Thread', $param['thread']);
						$thread->cleanRead();
						$send['id'] = $param['thread'];
						$submission = $thread->getRevision()->getSubmission();
						$project = $submission->getProject();
						if($param['parent'])
							$thread->addReply($comment, $param['parent']);
						else 
							$thread->addComment($comment);

						$send['commentId'] = $comment->getCommentID();
						break;
				}

				$dm->flush();
				$zmq->send(array(
					'type' => 'comment',
					'subType' => $param['type'],
					'content' => $send
				));
				break;
		}
		if($user->equals($project->getUser()) && $param['type']=='wave'){
			$to = $submission->getUser();
			$url = 'submit';
		}
		else if($param['type']=='team'){
			$to['users'] = $project->getTeam();
			$url = 'projectSubmissions';
		}
		else{
			$to = $project->getUser();
			$url = 'projectSubmissions';
		}

			$this->get('activity_mail_user')->sendEmail(
				$to,  //To
				'owner-new-comment', //Type
				array(					//Data
					'merge_vars'=> array(
						'projectname'=>$project->getProjectname(),
						'userwholeftcomment'=> $user->getFullname(),
						'commentuserleft' => $param['msg'],
						'commentdatetimestamp' => date("m-d-Y H:i:s"),
						'copyrightyear' => date("Y"),						
						'submissioncommentlink' => $this->generateUrl($url, array('publicId' => $project->getPublicId()), true).'?sub='.$submission->getPublicID().'&comment='.$param['type'][0].$send['id'],
						//we need $param['type'][0] to decide whether its wave or team comment
						'projectlink' => $this->generateUrl($url, array( 'publicId' => $project->getPublicId() ) )
					),
					'template' => 'ProjectNotificationComments'
				)
			);

		return new Response(json_encode(array("msg" => "success")));

	}

	public function readThreadAction(Request $request){
		$param = $request->request->all();
		if($param['thread']){
			$dm = $this->get('doctrine_mongodb')->getManager();
			$thread = $dm->find('SooundAppBundle:Thread', $param['thread']);
			$user = $this->getUser();

			$thread->setRead($user->getId());
			$dm->persist($thread);
			$dm->flush();

			return new Response(json_encode(array("msg" => "success")));
		}
		else
			return new Response(json_encode(array("msg" => "error")));

	}
}
