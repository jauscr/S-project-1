<?php

namespace Soound\AppBundle\Services;

class RealtimeSubmissionService
{
	private $zmq;

    public function __construct($zmq){
        $this->zmq = $zmq;
    }

    public function sendSubmission($raw){
        $this->zmq->send(array(
            "type" => "submission",
            "project" => $raw['project']->getPublicId(),
            "content" => $raw['content']
        ));
    }

    public function sendRevision($raw){
        $this->zmq->send(array(
            "type" => "revision",
            "submission" => $raw['submission']->getPublicID(),
            //"submission" => $raw['submission'],
            "content" => $raw['revision']
        ));
    }

}
