<?php

namespace Soound\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
class UserController extends Controller
{
    public function indexAction()
    {
        return $this;
    }

    public function userHomeAction()
    {
        // if its logged
        $user = $this->get('security.context')->getToken()->getUser();

        if ($user != 'anon.') {
            $text = "Welcome User";
            return $this->render('SooundAppBundle:User:dashboard.html.twig', array(
                'text' => $text,
                'user' => $user->getUsername()
            ));
        }else{
            // get off
            return new RedirectResponse("/");
        }
    }

    public function sendEmailsAction($emails){
        if ($this->container->get('request')->isXmlHttpRequest()){
            $arrayEmails = explode(',', $emails);
            $name = $this->get('request')->request->get('name');
            $username = $this->get('request')->request->get('username');

            foreach($arrayEmails as $key=>$email):
                $message = \Swift_Message::newInstance()
                    ->setSubject('Hey look!, '.$name.' send you a message')
                    ->setFrom('admin@soound.com')
                    ->setTo($email)
                    ->setBody("Hello, this is my new project in soound");
                $this->get('mailer')->send($message);

            endforeach;
            return new Response("yes");
        }
    }

    public function sendTemplateEmailsAction(){
        $securityContext =$this->get('security.context');
        $user = $securityContext->getToken()->getUser();
        if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $userEmail = $user->getEmail();
        }

        if ($this->container->get('request')->isXmlHttpRequest()){
            $arrayEmails = $this->get('request')->request->get('arrayMails');
            $userName = $this->get('request')->request->get('username');

            $dispatcher = $this->get('hip_mandrill.dispatcher');
            $message = new Message();
            $message
                ->setFromEmail($userEmail)
                ->setFromName($user->getFullname())
                ->setSubject($user->getFullname().' invited you to collaborate on Soound')
                ->setHtml($this->renderView(
                    'SooundAppBundle:Mail:sooundEmail.html.twig',
                    array(
                        'name' => $user->getFullname()
                )));

            foreach ($arrayEmails as $email) {
                $message->addTo($email);
            }

            $result = $dispatcher->send($message);
            /*
            foreach($arrayEmails as $email):
                $message = \Swift_Message::newInstance()
                    ->setSubject('Hey look!, '.$userName.' send you a message')
                    ->setFrom($userEmail)
                    ->setTo($email)
                    ->setBody($this->renderView(
                        'SooundAppBundle:Mail:sooundEmail.html.twig',
                        array(
                            'name' => $user->getUsername()
                        )
                    ),'text/html');
                $this->get('mailer')->send($message);
            endforeach;
            */
            return new Response(print_r($result, true));
        }
    }

    public function verifyEmailAction(){
        $email = $_POST['email'];
        if ($this->container->get('request')->isXmlHttpRequest()){
            $userManager = $this->get('fos_user.user_manager');
            $user = $userManager->findUserBy(array('email' => $email));
            if (null === $user) {
                return new Response(0);
            }
            else{
                return new Response(1);
            }
        }
    }

    public function activityUserEmail($eventName,$username, $emailText,$email){
            $message = \Swift_Message::newInstance()
                ->setSubject('Hey'.$username.', check out your last activity status')
                ->setFrom('admin@soound.com')
                ->setTo($email)
                ->setBody($this->renderView(
                    'SooundAppBundle:Mail:sooundUserActivity.html.twig',
                    array(
                        'eventName' => $eventName,
                        'username' => $username,
                        'emailText' => $emailText
                    )
                ),'text/html');
            $this->get('mailer')->send($message);
    }

    public function startNewUserAction(Request $request){
        if ($request->getMethod() == 'POST') {
            $user= $this->get('security.context')->getToken()->getUser();

            $name = $request->request->get('name');
            $lastName = $request->request->get('lastName');

            if(!empty($name) && !empty($lastName)){
                $user->setFullname($name.' '.$lastName);

                $mailer = $this->container->get('fos_user.mailer.custom');
                $token = sha1(uniqid(mt_rand(), true));
                $user->setConfirmationToken($token);
                $mailer->sendConfirmationEmailMessage($user);

                $userManager = $this->container->get('fos_user.user_manager');
                $userManager->updateUser($user);

                return $this->redirect(
                    $this->generateUrl('userProfile', array('publicId' => $user->getPublicId())));

            }else{
                return $this->redirect($this->generateUrl('fos_user_registration_confirmed', array('error'=>'check')));
            }
        }
    }

    public function closeNotConfirmedAction(){
        $session = $this->getRequest()->getSession();
        $session->set('closeNotConfirmed', true);

        return new Response(json_encode(array("msg" => "success")));
    }
}