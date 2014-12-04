<?php

namespace Soound\AppBundle\Services;

use Soound\AppBundle\Document\Activity;
use Soound\AppBundle\Document\ActivityContent;

class ActivityService
{
	private $dm;
	private $zmq;

    public function __construct($doctrine, $zmq){
        $this->dm = $doctrine->getManager();
        $this->zmq = $zmq;
    }

    private function addContent($activity, $raw){
    	$send = array();

    	$content = new ActivityContent();
    	if(isset($raw['ref']) ){
    		$content->setRef($raw['ref']);
    		$send['ref'] = $content->getRefDetails();
    	}
    	if(isset($raw['text']) ){
    		$content->setText($raw['text']);
    		$send['text'] = $raw['text'];
    	}
    	$activity->addContent($content);
    	$this->dm->persist($content);

    	return $send;
    }

    private function store($raw){
    	$send = array(
    		'type' => $raw['type'],
            'date' => $raw['date'],
    		'content' => array()
    	);

    	$activity = new Activity();
        if( isset($raw['to']) ){
            $activity->setUser($raw['to']);
            $raw['to']->addActivity($activity);
        }
    	if( isset($raw['private']) )
    		$activity->setPrivate($raw['private']);
    	$activity->setType($raw['type']);
    	for ($i=0; $i<count($raw['content']); $i++) {
    		$send['content'][] = $this->addContent($activity, $raw['content'][$i]);
    	}
    	
    	$this->dm->persist($activity);
    	$this->dm->flush();

    	return $send;
    }

    public function send($raw){
        $content = $this->store($raw);
        $this->zmq->send(array(
                "type" => "notification",
                "to" => isset($raw['to']) ? $raw['to']->getId() : 'public',
                "private" => isset($raw['private']) ? $raw['private'] : false,
                "content" => $content
        ));
    }

}
