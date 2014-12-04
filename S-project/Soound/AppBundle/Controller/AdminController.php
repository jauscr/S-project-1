<?php

namespace Soound\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Soound\AppBundle\Document\Activity;
use Soound\AppBundle\Document\ActivityContent;
use Soound\AppBundle\Document\Comment;
use Soound\AppBundle\Document\Credit;
use Soound\AppBundle\Document\CreditCard;
use Soound\AppBundle\Document\HQFile;
use Soound\AppBundle\Document\Project;
use Soound\AppBundle\Document\ProjectReference;
use Soound\AppBundle\Document\Rating;
use Soound\AppBundle\Document\Revision;
use Soound\AppBundle\Document\Score;
use Soound\AppBundle\Document\Submission;
use Soound\AppBundle\Document\Thread;
use Soound\AppBundle\Document\Transaction;
use Soound\AppBundle\Document\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;



class AdminController extends Controller
{
	public function indexAction(){

		$twigArgs = array();
		$dm = $this->dm();

		$twigArgs["numUsers"] = count( $dm->getRepository('SooundAppBundle:User')->findAll() );
		$twigArgs["numProjects"] = count( $dm->getRepository('SooundAppBundle:Project')->findAll() );

		return $this->render( "SooundAppBundle:Admin:index.html.twig", $twigArgs );
	}

	private function dm(){
		return $dm = $this->get('doctrine_mongodb')->getManager();
	}

	public function getProjectsAction(){

		$query = $this->dm()->getRepository('SooundAppBundle:Project')->findAll();
		$projects = array();

		foreach ($query as $project) {

			$projects[] = array(
				"id" => $project->getProjectId(),
				"title" => $project->getProjectname(),
				"budget" => $project->getBudget(),
				"type" => $project->getProjectType(),
				"start" => $project->getPublishedOn()->format("m/d/Y"),
				"end" => $project->getEndingOn()->format("m/d/Y")
			);

		}

		return new Response( json_encode($projects) );
	}

	public function getUsersAction(){

		$query = $this->dm()->getRepository('SooundAppBundle:User')->findAll();
		$users = array();

		foreach ($query as $user) {

			$users[] = array(
				"id" => $user->getId(),
				"name" => $user->getFullname(),
				"email" => $user->getEmail()
			);

		}

		return new Response( json_encode($users) );
	}

	private function applyParams($q, $params){
		foreach ($params as $p) {
			//$q = $q->field($p['field']);
			$str = '$q->field('.$p['field'].')';
			if($p['comparison'] != 'range'){
				//$q = eval( '$q->'.$p["comparison"].'('.$p["val"].')' );
				$str .= '->'.$p["comparison"].'('.$p["val"].')';
			}
			else {
				$q = $q->range($p['val']['start'], $p['val']['end']);
			}
		}
		return $q;
	}

	public function queryAction(Request $request){
		$docType = $request->get('docType'); // $_POST['docType']; 
		$fields = $request->get('fields'); // $_POST['fields'];
		$queryParams = $request->get('query'); // $_POST['query'];
		return new Response(json_encode($queryParams));
		if( isset($queryParams) && count($queryParams) > 0){
			$query = $this->applyParams($this->dm()->createQueryBuilder('SooundAppBundle:'.$docType), $queryParams);

			//return new Response(1);
		}
		else {
			$query = $this->dm()->createQueryBuilder('SooundAppBundle:'.$docType);
		}

		if( isset($fields) && count($fields) > 0){
			$query->select( implode(",", $fields) );
		}
		/*
		$cursor = $query->eagerCursor(true)->getQuery()->execute();
		//$cursor = $query->getQuery()->execute();
		
		$results = array();
		foreach ($cursor as $document) {
			$results[] = $document->toArray();
		}
		
		return new Response( json_encode($results) );
		*/

		$documents = $query->hydrate(false)->getQuery()->toArray();
		return new Response( json_encode($documents) );
		/*
		$results = array();
		foreach ($documents as $id => $document) {
			$document = array();
			foreach ($document as $field => $val) {
				$document[$field] = $val;
			}
			$results[$id] = $document;
		}

		return new Response( json_encode($results) );
		*/
	}
}
