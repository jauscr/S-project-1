<?php

namespace Soound\AppBundle\Topic;

//use JDare\ClankBundle\Topic\TopicInterface;
use Ratchet\ConnectionInterface as Conn;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Soound\AppBundle\Document\User;
use Soound\AppBundle\Document\Comment;
use Soound\AppBundle\Document\Thread;
use Symfony\Component\HttpFoundation\Session\Session;

class CommentTopic extends Controller
{
    private $doctrine;

    public function __construct($doctrine)
    {
        $this->doctrine = $doctrine;
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
        print($conn->resourceId." subscribied to ".$topic->getId().PHP_EOL);
        //print( "Session User ID: ".$conn->Session->get('userID').PHP_EOL );
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
        print($conn->resourceId." has left ".$topic->getId().PHP_EOL);
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
        print($conn->resourceId." published to ".$topic->getId().PHP_EOL);
        //print( "Session User ID: ".$conn->Session->get('userID').PHP_EOL );

        $userID = $conn->Session->get('userID');
        $dm = $this->doctrine->getManager();
        if($userID)
            $user = $dm->find('SooundAppBundle:User', $userID);
        else{
            print(PHP_EOL."ERROR: UserID not set in session.".PHP_EOL);
            return;
        }

        $channel = explode('/', $topic->getId() );
        if($channel[1] === "team"){

            if($event["comment"] != "new"){
                $reply = new Comment();
                $reply->setUser($user);
                $reply->setText($event["msg"]);
                $dm->persist($reply);

                $dm->find('SooundAppBundle:Revision', $channel[2])->addTeamReply($reply, $event["comment"]);
                $dm->flush();
                $event["id"] = $reply->getCommentID();

            } else {
                $comment = new Comment();
                $comment->setUser($user);
                $comment->setText($event["msg"]);
                $dm->persist($comment);

                $dm->find('SooundAppBundle:Revision', $channel[2])->addTeamComment($comment);
                $dm->flush();
                $event["id"] = $comment->getCommentID();
            }

            $event["sender"] = $user->getFullName();
            $event["picture"] = '../uploads/userPics/'.$user->getId().$user->getPictureExt();
            $topic->broadcast($event);
        }
        else if($channel[1] === "wave"){
            if( $event["thread"] === "new"){

                $comment = new Comment();
                $comment->setUser($user);
                $comment->setText($event["msg"]);
                $dm->persist($comment);

                $thread = new Thread();
                $thread->addComment($comment);
                $thread->setTime($event["time"]);
                $dm->persist($thread);

                $revision = $dm->find('SooundAppBundle:Revision', $channel[2]);
                $revision->addWaveThread($thread);
                $thread->setRevision($revision);

                $dm->flush();

                $event["commentId"] = $comment->getCommentID();
                $event["id"] = $thread->getThreadID();
            } else {
                //Add comment under $event["thread"], which is the threadID
                if($event["parent"]){ //Add as a reply
                    $reply = new Comment();
                    $reply->setUser($user);
                    $reply->setText($event["msg"]);
                    $dm->persist($reply);

                    $thread = $dm->find('SooundAppBundle:Thread', $event["thread"]);
                    $thread->addReply($reply, $event["parent"]);

                    $dm->flush();

                    $event["commentId"] = $reply->getCommentID();
                } else {
                    $comment = new Comment();
                    $comment->setUser($user);
                    $comment->setText($event["msg"]);
                    $dm->persist($comment);

                    $thread = $dm->find('SooundAppBundle:Thread', $event["thread"]);
                    $thread->addComment($comment);

                    $dm->flush();

                    $event["commentId"] = $comment->getCommentID();
                }

            }
            $event["sender"] = $user->getFullName();
            $event["picture"] = '../uploads/userPics/'.$user->getId().$user->getPictureExt();
            $topic->broadcast($event);
        }
    }

}
