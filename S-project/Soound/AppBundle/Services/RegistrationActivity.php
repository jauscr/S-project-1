<?php

namespace Soound\AppBundle\Services;

use Symfony\Component\Security\Core\SecurityContext;

/**
 * Custom registration listener
 */
class RegistrationActivity
{
	private $securityContext;
	private $activitySender;

	public function __construct(SecurityContext $securityContext, $activitySender){
		$this->securityContext = $securityContext;
		$this->activitySender = $activitySender;
	}

	public function onRegistrationConfirmed($event){
		//Now send a public notification about this user's signup
        echo $event->getUser();
        $activity = array(
            'to' => 'public',
            'type' => 'people',
            'date' => date_format( date_create(), "m-d-Y" ),
            'private' => false, //Optional, defaults to false
            'content' => array(
                0 => array(
                    'ref' => $event->getUser(),
                    'text' => ' joined Soound!'
                )
            )
        );
        $this->activitySender->send($activity);
	}
}