<?php
namespace Soound\AppBundle\Services;

use Hip\MandrillBundle\Message;
use Hip\MandrillBundle\Dispatcher;

class ActivityUserEmail{
    protected $mailer;

    public function __construct($mailer , \Symfony\Bundle\FrameworkBundle\Templating\EngineInterface $templating, $securityContext)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->securityContext = $securityContext;
    }

    public function activityUserEmail($eventName, $user, $emailText, $email){
        $message = new Message();
        $message
            ->addTo($email)
            ->setSubject('Hey '.$user->getFullname().', check out your latest activity status')
            ->setHtml($this->templating->render(
                'SooundAppBundle:Mail:sooundUserActivity.html.twig',
                array(
                    'eventName' => $eventName,
                    'username' => $user->getFullname(),
                    'emailText' => $emailText
            )));
        return $this->mailer->send($message);
    }

    public function sendEmail($to, $type, $data){
        $message = new Message();
        $currEmail = $this->securityContext->getToken()->getUser()->getEmail();

        if(is_array($to) && isset($to['users'])){
            foreach ($to['users'] as $user){
                if($user->wantsEmail($type) && $currEmail!=$user->getEmail()){
                    $message
                        ->addTo($user->getEmail())
                        ->addMergeVars($user->getEmail(),$data['merge_vars']);
                }
            }
        }
        elseif( $to->wantsEmail($type) && $currEmail!=$to->getEmail()){
            $message
                ->addTo($to->getEmail())
                ->addMergeVars($to->getEmail(),$data['merge_vars']);
        }

        if(sizeof($message->getTo())>0){
            return $this->mailer->send($message,$data['template']);
        }
        return false;
    }

    public function sendBetaInvite($to, $pass){
        $message = new Message();
        $message
            ->addTo( $to )
            ->addGlobalMergeVar('emailAddress', $to)
            ->addGlobalMergeVar('password', $pass)
            ->addGlobalMergeVar("copyrightyear", date("Y") );
            
        return $this->mailer->send($message, 'betainvite');
    }

    public function sendReceipt($to, $from, $amount, $project){
        $message = new Message();
        $message
            ->addTo($to->getEmail())
            ->addTo($from->getEmail())
            ->setSubject('Receipt for '.$project->getProjectName())
            ->addGlobalMergeVar("owner", $from->getFullname())
            ->addGlobalMergeVar("winner", $to->getFullname())
            ->addGlobalMergeVar("project", $project->getProjectname())
            ->addGlobalMergeVar("amount", $project->getBudget())
            ->addGlobalMergeVar("submission", $project->getWinner()->getLastRevision()->getTitle())
            ->addGlobalMergeVar("copyrightyear", date("Y") );

        return $this->mailer->send($message, 'projectreceipt');
    }
}