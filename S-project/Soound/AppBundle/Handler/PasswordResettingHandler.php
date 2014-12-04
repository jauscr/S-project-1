<?php
namespace Soound\AppBundle\Handler;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Listener responsible to change the redirection at the end of the password resetting
 */
class PasswordResettingHandler implements EventSubscriberInterface {

    public static function getSubscribedEvents() {
        return [
            FOSUserEvents::RESETTING_RESET_SUCCESS => 'onPasswordResettingCompleted',
        ];
    }

    public function onPasswordResettingCompleted($event) {
        $event->setResponse(
            new RedirectResponse('/')
        );
    }
}