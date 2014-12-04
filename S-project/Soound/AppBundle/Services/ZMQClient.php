<?php
namespace Soound\AppBundle\Services;

use \ZMQ,
    \ZMQSocket,
    \ZMQContext,
    \ZMQPoll;

class ZMQClient{
    protected $socket;
    protected $dsn;
    protected $port;

    public function __construct($dsn, $port)
    {
        // This is our new stuff
        $context = new \ZMQContext();
        $this->socket = $context->getSocket(\ZMQ::SOCKET_PUSH, 'my pusher');
        $this->socket->connect("tcp://".$dsn.":".$port);
    }

    public function send($message){
        $this->socket->send(json_encode($message));
/*
        $this->client->send($message);
        $reply = $this->client->recv();
        return json_decode($reply);

*/
    }
}