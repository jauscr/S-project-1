<?php
namespace Soound\AppBundle\Services;

use Hip\MandrillBundle\Message;
use Hip\MandrillBundle\Dispatcher;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Mailer\MailerInterface;

class FosCustomMailer implements MailerInterface
{
    protected $mailer;
    protected $templating;
    protected $router;

    public function __construct($mailer , \Symfony\Bundle\FrameworkBundle\Templating\EngineInterface $templating, \Symfony\Bundle\FrameworkBundle\Routing\Router $router)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->router = $router;
    }

    public function sendConfirmationEmailMessage(UserInterface $user)
    {
        $url = $this->router->generate(
            'fos_user_registration_confirm', 
            array(
                'token' => $user->getConfirmationToken()
            ), true);

        $message = new Message();

        $merge_vars = array(
            'copyrightyear' => date("Y"),
            'activateaccountlink' => $url
        );

        $message
            ->addTo($user->getEmail())
            ->setSubject('Soound Email Confirmation')
            ->addMergeVars($user->getEmail(),$merge_vars);

        $this->mailer->send($message,'AccountActivation');
    }

    public function sendResettingEmailMessage(UserInterface $user)
    {

        $url = $this->router->generate(
            'fos_user_resetting_reset', 
            array(
                'token' => $user->getConfirmationToken()
            ), true);

        $message = new Message();

        $merge_vars = array(
            'copyrightyear' => date("Y"),
            'useremailaddress' => $user->getEmail(),
            'passwordresetlink' => $url
        );

        $message
            ->addTo($user->getEmail())
            ->setSubject('Soound Password Reset')
            ->addMergeVars($user->getEmail(),$merge_vars);

        $this->mailer->send($message,'PasswordReset');
    }
}