<?php
namespace Soound\AppBundle\Services;

use Ratchet\ConnectionInterface;
use Ratchet\Wamp\WampServerInterface;
//use Symfony\Component\HttpFoundation\Session\Session;
//use Soound\AppBundle\Document\User;
//use Soound\AppBundle\Document\Comment;
//use Soound\AppBundle\Document\Thread;
//use Soound\AppBundle\Document\Activity;
//use Soound\AppBundle\Document\ActivityContent;
//use Soound\Topic;

class Pusher implements WampServerInterface{

    protected $subscribedTopics = array();
    protected $conn;
    
    public function onSubscribe(ConnectionInterface $conn, $topic) {
        // When a visitor subscribes to a topic link the Topic object in a  lookup array
        echo "{$conn->resourceId} subscribed to: {$topic->getId()}\n";
        if (!array_key_exists($topic->getId(), $this->subscribedTopics)) {
            $this->subscribedTopics[$topic->getId()] = $topic;
        }
    }
    public function onMessage($data){

        $message = json_decode($data, true);
        //echo "New Message!\n";

        switch ($message['type']) {
            case 'notification':

                if(!array_key_exists('notification/'.$message['to'], $this->subscribedTopics)){
                    return;
                }

                if(!$message['private']){ //Public Activity Feed
                    if(array_key_exists('notification/public', $this->subscribedTopics)){
                        $this->subscribedTopics['notification/public']->broadcast($message['content']);
                    }
                }
                else {
                    $topic = $this->subscribedTopics[$message['type'].'/'.$message['to']];
                    $topic->broadcast($message['content']); 
                }

                break;
            case 'comment':
                if(!array_key_exists('comment/'.$message['subType'].'/'.$message['content']['to'], $this->subscribedTopics)){
                    return;
                }

                $topic = $this->subscribedTopics['comment/'.$message['subType'].'/'.$message['content']['to']];
                $topic->broadcast($message['content']);
                break;
            case 'submission':
                echo "New submission to submission/".$message['project']."\n";
                if(!array_key_exists('submission/'.$message['project'], $this->subscribedTopics)){
                    return;
                }

                $topic = $this->subscribedTopics['submission/'.$message['project']];
                $topic->broadcast($message['content']);
                break;
            case 'revision':
                echo "New Revision to revision/".$message['submission']."\n";
                if(!array_key_exists('revision/'.$message['submission'], $this->subscribedTopics)){
                    return;
                }

                $topic = $this->subscribedTopics['revision/'.$message['submission']];
                $topic->broadcast($message['content']);
                break;
        }
    }
    public function onUnSubscribe(ConnectionInterface $conn, $topic) {
        unset($this->subscribedTopics[$topic->getId()]);
    }
    public function onOpen(ConnectionInterface $conn) {
        //echo "New connection! ({$conn->resourceId})\n";
    }
    public function onClose(ConnectionInterface $conn) {
        //echo "Connection {$conn->resourceId} has disconnected\n";
    }
    public function onCall(ConnectionInterface $conn, $id, $topic, array $params) {
        // In this application if clients send data it's because the user hacked around in console
        $conn->callError($id, $topic, 'You are not allowed to make calls')->close();
    }
    public function onPublish(ConnectionInterface $conn, $topic, $event, array $exclude, array $eligible) {
        // In this application if clients send data it's because the user hacked around in console
        //$conn->close();
    }
    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
}