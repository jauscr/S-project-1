<?php

namespace Soound\AppBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\UserBundle\Controller\RegistrationController as BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RegistrationController extends BaseController
{
    public function confirmAction(Request $request, $token)
    {
        $userManager = $this->container->get('fos_user.user_manager');

        $user = $userManager->findUserByConfirmationToken($token);

        if (null === $user) {
            return new RedirectResponse( $this->container->get('router')->generate('index_display') );
        }
        else{
            $user->setConfirmed(true);
            $userManager->updateUser($user);

            // Token found. Letting the FOSUserBundle's action handle the confirmation 
            return new RedirectResponse( $this->container->get('router')->generate('index_display') );  
            //return parent::confirmAction($request, $token);
        }
    }

    public function confirmedAction(){
        $user = $this->container->get('security.context')->getToken()->getUser();
        //var_dump($user->getConfirmed());exit;
        if($user->getConfirmed()){
            return $response = new RedirectResponse(
                $router->generate('userProfile', array(
                    "publicId" => $user->getPublicId()
                ))
            );
        }
        return parent::confirmedAction();
    }

    public function resendAction(){
        $userManager = $this->container->get('fos_user.user_manager');
        $user = $this->container->get('security.context')->getToken()->getUser();
        $mailer = $this->container->get('fos_user.mailer.custom');
        $token = sha1(uniqid(mt_rand(), true));
        $user->setConfirmationToken($token);
        $mailer->sendConfirmationEmailMessage($user);
        $userManager->updateUser($user);
        
        return new RedirectResponse( $this->container->get('router')->generate('index_display') );        
    }
}