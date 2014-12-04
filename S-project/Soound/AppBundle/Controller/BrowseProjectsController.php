<?php

namespace Soound\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Soound\AppBundle\Document\Project;
use Soound\AppBundle\Document\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;


class BrowseProjectsController extends Controller
{
	public function indexAction($publicId){
		return $this->render(
			"SooundAppBundle:Html:browse-projects.html.twig",array(
				"publicId"=>$publicId
			)
		);
	}

	/**
	 * Return the most recently added featured projects on the site
	 */
	public function browseFeaturedAction(){ 
		$howMany = $_POST['numProjects']; //how many projects we want to return
		$offset = $_POST['offset']; //how many projects in the results to skip
		$dm = $this->get('doctrine_mongodb')->getManager();

		$results = $this->applyProjectFilters($dm->createQueryBuilder('SooundAppBundle:Project'));

		$results = $results->field('isFeatured')->equals('featured')
			->field('winner')->exists(false)
			->field('endingOn')->gt(new \DateTime())
			->sort('publishedOn', 'desc')
			->limit($howMany)
			->skip($offset)
			->getQuery()
			->toArray();

		$featured = $this->getProjectInfo($results, 'featured');

		//Prepare the response
		$response = array("msg" => $this->nonEmptyResults($featured), "content" => $featured );
		//JSON Encode response
		return new Response(json_encode($response));
	}

	/**
	 * Return the projects closest to ending on the sites
	 */
	public function browseEndingAction(){
		$howMany = $_POST['numProjects']; //how many projects we want to return
		$offset = $_POST['offset']; //how many projects in the results to skip
		$dm = $this->get('doctrine_mongodb')->getManager();

		$results = $this->applyProjectFilters($dm->createQueryBuilder('SooundAppBundle:Project'));
		
		$results = $results->field('endingOn')->gt(new \DateTime())
			->field('winner')->exists(false)
			->limit($howMany)
			->skip($offset)
			->sort('endingOn', 'asc')
			->getQuery()
			->execute();

		$endingSoon = $this->getProjectInfo($results, 'ending');

		//Prepare the response
		$response = array("msg" => $this->nonEmptyResults($endingSoon), "content" => $endingSoon);
		//JSON Encode response
		return new Response(json_encode($response));
	}

	/**
	 * Return the most popular projects on the site
	 */
	public function browsePopularAction(){
		$howMany = $_POST['numProjects']; //how many projects we want to return
		$offset = $_POST['offset']; //how many projects in the results to skip
		$dm = $this->get('doctrine_mongodb')->getManager();

		$query = $this->applyProjectFilters($dm->createQueryBuilder('SooundAppBundle:Project'));

		$results = $query->field('endingOn')->gt(new \DateTime())
			->field('winner')->exists(false)
			->limit($howMany)
			->skip($offset)
			->sort('followersCount', 'desc')
			->getQuery()
			->execute();

		$popular = $this->getProjectInfo($results, 'popular');

		//Prepare the response
		$response = array("msg" => $this->nonEmptyResults($popular), "content" => $popular);
		//JSON Encode response
		return new Response(json_encode($response));
	}

	/**
	 * Return the projects on the site with the highest budget
	 */
	public function browseMoreAction(){
		$howMany = $_POST['numProjects']; //how many projects we want to return
		$offset = $_POST['offset']; //how many projects in the results to skip
		$dm = $this->get('doctrine_mongodb')->getManager();

		$query = $this->applyProjectFilters($dm->createQueryBuilder('SooundAppBundle:Project'));

		$results = $query->field('endingOn')->gt(new \DateTime())
			->field('winner')->exists(false)
			->limit($howMany)
			->skip($offset)
			->sort('budget', 'desc')
			->getQuery()
			->execute();

		$more = $this->getProjectInfo($results, 'more');

		//Prepare the response
		$response = array("msg" => $this->nonEmptyResults($more), "content" => $more);
		//JSON Encode response
		return new Response(json_encode($response));
	}

	/**
	 * Return an array of associated arrays containing project properties
	 */
	private function getProjectInfo($results, $type){
		$projectInfo = array();
		$s3 = $this->get('aws_s3');
		foreach ($results as $result) {

			array_push($projectInfo, array(
				'id' => $result->getPublicId(),
				'title' => $result->getProjectName(),
				'description' => $result->getProjectDetails(),
				'genre' => $result->getProjectGenre(),
				'type' => $result->getProjectType(true),
				'numEntries' => sizeof( $result->getSubmissions() ),
				'followers' => sizeof($result->getFollowers()),
				'budget' => $result->getBudget(),
				'startedOn' => $result->getPublishDate(),
				'endingOn' => $result->getDueDate(),
				'daysLeft' => intval($result->getDueDate()->diff( date_create() )->format('%D')),
				'picture' => $result->getProjectExt() != "" ? ($s3->get_object($this->container->getParameter('s3_bucket'),"Projects/".$result->getPublicId()."/image/".$result->getPublicId()."-300.".$result->getProjectExt(),array("preauth"=>strtotime("+1 hour")))) : "/uploads/projectPics/project_pic_small.png"
			));
		}
		$sizeof = sizeof($projectInfo);
		$html = $this->render(
			"SooundAppBundle:Html:projects.html.twig", array(
				"type" => $type,
				"projectInfo" => $projectInfo
			)
		);

		return array("html"=>$html->getContent(),"sizeof"=>$sizeof);
	}

	/**
	 * Return more details on the requested project
	 */
	public function getViewProjectAction(){
		$session = $this->getRequest()->getSession();
		$dm = $this->get('doctrine_mongodb')->getManager();

		//$project = $dm->find('SooundAppBundle:Project', $_POST['publicId']);
		$project = $dm->getRepository('SooundAppBundle:Project')->findOneBy(array('publicId' => $_POST['publicId']));
		$session->set('project', $project->getId());
		$user = $this->get('security.context')->getToken()->getUser();
		$poster = $project->getUser();
        $s3 = $this->get('aws_s3');


		//Check what this user's role is with respect to this project
		$role = "none";
		if($user === 'anon.')
			$role = "anon";
		else if($poster === $user)
			$role = "team";
		else{
			$followers = $project->getFollowers();
			if($user->inGroup($followers)){
				$role = "follower";
			}else{
				$members = $project->getMembers();
				if( $user->inGroup($members) )
					$role = "member";
				else{
					$team = $project->getTeam();
					if( $user->inGroup($team) )
						$role = "team";
				}
			}
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

		//Prepare the response
		$response = array(
			"msg" => $this->nonEmptyResults($project), 
			"debug" => $poster,
			"content" => array(
				'id' => $project->getPublicId(),
				'role' => $role,
				'title' => $project->getProjectName(),
				'description' => $project->getProjectDetails(),
				'genre' => $project->getProjectGenre(),
				'picture' => $project->getProjectExt() != "" ? ($s3->get_object($this->container->getParameter('s3_bucket'),"Projects/".$project->getPublicId()."/image/".$project->getPublicId()."-200.".$project->getProjectExt(),array("preauth"=>strtotime("+1 hour")))) : "/uploads/projectPics/project_pic_small.png",
				'type' => $project->getProjectType(),
				'numEntries' => count( $project->getSubmissions() ),
				'budget' => $project->getBudget(),
				'daysLeft' => $project->getDueDate()->diff( date_create() )->format('%a'),
				'references' => $references,
				'posterName' => $poster->getFullname(),
				'posterLink' => $this->get('router')->generate('userProfile', array('publicId' => $poster->getPublicId())),
				'posterPic' => $s3->get_object($this->container->getParameter('s3_bucket'),$poster->getPicture(),array("preauth"=>strtotime("+1 hour")))
			)
		);
		//JSON Encode response
		return new Response(json_encode($response));
	}

	/**
	 * Return a doctrine query object that has been filtered for budget, type, and genre
	 */
	private function applyProjectFilters($query){
		$filters = array(
				'budget' => (isset($_POST['minBudget'])) ? intval($_POST['minBudget']) : 0,
				'types' => (isset($_POST['projectTypes'])) ? $_POST['projectTypes'] : array(),
				'genres' => (isset($_POST['projectGenres'])) ? $_POST['projectGenres'] : array()
			);

		$query->field('budget')->gte($filters['budget']);

		if( count( $filters['types'] ) > 0 )
			$query->field('projectType')->in($filters['types']);
		
		if( count( $filters['genres'] ) > 0 )
			$query->field('projectgenre')->all($filters['genres']);

		return $query;
	}

	/**
	 * Check whether $results is an empty array or not
	 */
	private function nonEmptyResults($results){
		if( count($results) > 0 )
			return "ok";
		else
			return "error";
	}

	/**
	 * Join the selected project and reroute to the submissions page
	 */
	public function joinProjectAction(){
		$session = $this->getRequest()->getSession();
		$user = $this->get('security.context')->getToken()->getUser();
		$dm = $this->get('doctrine_mongodb')->getManager();

		$project = $dm->find('SooundAppBundle:Project', $session->get('project'));

		$project->addMember($user);
		$user->addMemberOfProject($project);

		$dm->flush();

		return new Response( json_encode( array( 
			"url" => $this->generateUrl('submit', array( 'publicId' => $project->getPublicId() ) )
		) ) );
/*
		return $this->redirect(
			$this->generateUrl(
				'submit', 
				array(
					'publicId' => $project->getPublicId()
				)
			)
		);
*/
/*
		return new Response( json_encode( array( 
			"msg" => $user->getId()
		) ) );
*/
	}

	/**
	 * Follow the selected project
	 */
	public function followProjectAction(){
		$session = $this->getRequest()->getSession();
		$user = $this->get('security.context')->getToken()->getUser();
		$dm = $this->get('doctrine_mongodb')->getManager();
		$project = $dm->find('SooundAppBundle:Project', $session->get('project'));

		$project->addFollower($user);
		$user->addFollowingProject($project);

		$dm->flush();

		return new Response(1);
	}

	/**
	 * Unfollow the selected project
	 */
	public function unfollowProjectAction(){
		$session = $this->getRequest()->getSession();
		$user = $this->get('security.context')->getToken()->getUser();
		$dm = $this->get('doctrine_mongodb')->getManager();
		$project = $dm->find('SooundAppBundle:Project', $session->get('project'));

		$project->removeFollower($user);
		$user->removeFollowingProject($project);

		$dm->flush();

		return new Response(1);
	}
}