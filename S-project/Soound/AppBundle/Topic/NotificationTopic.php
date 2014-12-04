<?php

namespace Soound\AppBundle\Topic;

//use JDare\ClankBundle\Topic\TopicInterface;
use Ratchet\ConnectionInterface as Conn;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Soound\AppBundle\Document\User;
use Soound\AppBundle\Document\Notification;
use Symfony\Component\HttpFoundation\Session\Session;

class NotificationTopic extends Controller
{
    private $doctrine;

    public function __construct($doctrine)
    {
        $this->doctrine = $doctrine;
        //print("MADE NOTIFICATION TOPIC".PHP_EOL);
    }

    /**
     * This will receive any Subscription requests for this topic.
     *
     * @param \Ratchet\ConnectionInterface $conn
     * @param $topic
     * @return void
     */
    public function onSubscribe(Conn $conn, $topic)
    {
        //print($conn->resourceId." subscribied to notifications.".PHP_EOL);

        //this will broadcast the message to ALL subscribers of this topic.
        //$topic->broadcast($conn->resourceId . " has joined " . $topic->getId());
    }

    /**
     * This will receive any UnSubscription requests for this topic.
     *
     * @param \Ratchet\ConnectionInterface $conn
     * @param $topic
     * @return void
     */
    public function onUnSubscribe(Conn $conn, $topic)
    {
        //this will broadcast the message to ALL subscribers of this topic.
        //$topic->broadcast($conn->resourceId . " has left " . $topic->getId());
    }


    /**
     * This will receive any Publish requests for this topic.
     *
     * @param \Ratchet\ConnectionInterface $conn
     * @param $topic
     * @param $event
     * @param array $exclude
     * @param array $eligible
     * @return mixed|void
     */
    public function onPublish(Conn $conn, $topic, $event, array $exclude, array $eligible)
    {
        $userID = $conn->Session->get('userID');
        $dm = $this->doctrine->getManager();
        if($userID)
            $user = $dm->find('SooundAppBundle:User', $userID);
        else{
            print(PHP_EOL."ERROR: UserID not set in session.".PHP_EOL);
            return;
        }

        $channel = explode('/', $topic->getId() );
        $toUserId = $channel[ count($channel) - 1 ];

        $now = date_create();
        /* Not storing during testing

        $notification = new Notification();
        $notification->setDate( $now );
        $notification->setType( $event["type"] );
        $notification->setUser( $user ); //Set from user to this user
        $notification->setText( $event["msg"] );

        $dm->persist($notification);

        $toUser = $dm->find('SooundAppBundle:User', $toUserId);
        $toUser->addNotification( $notification );

        $dm->flush();
*/
        //Get the id of the user we're sending the notification to, and store
        //a new notification in their model
        $event["sender"] = $user->getFullName();
        $event["date"] = $now;
        $topic->broadcast($event);
    }

}
