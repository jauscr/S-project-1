<?php
namespace Soound\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Session\Storage\Handler;
use Soound\AppBundle\Services\Pusher as Pusher;
use Ratchet\Session\SessionProvider;
use Doctrine\MongoDB\Connection as Connection;
use ZMQ;
use React;
use Ratchet;

class ServerCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('push:server')
            ->setDescription('Starts push server')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $loop   = React\EventLoop\Factory::create();
        $container = $this->getContainer();

        $pusher = $container->get('soound_app.pusher');
        // Listen for the web server to make a ZeroMQ push after an ajax request
        $context = new React\ZMQ\Context($loop);
        $pull = $context->getSocket(ZMQ::SOCKET_PULL);
        $pull->bind('tcp://'.$container->getParameter('zmq_host').':'.$container->getParameter('zmq_port')); // Binding to 127.0.0.1 means the only client that can connect is itself
        $pull->on('message', array($pusher, 'onMessage'));

        $output->writeln('Bound ZMQ');

        // Set up our WebSocket server for clients wanting real-time updates
        $webSock = new React\Socket\Server($loop);
        $webSock->listen($container->getParameter('ratchet_port'), $container->getParameter('ratchet_private_host')); // Binding to 0.0.0.0 means remotes can connect

        $mongodb=$container->get('mongo');

        $webServer = new Ratchet\Server\IoServer(
            new Ratchet\Http\HttpServer(
                new Ratchet\WebSocket\WsServer(
                    new SessionProvider(
                        new Ratchet\Wamp\WampServer(
                            $pusher
                        ),
                        new Handler\MongoDbSessionHandler($mongodb,$container->getParameter('mongo.session.options'))
                    )
                )
            ),
            $webSock
        );

        $output->writeln('Connected to Ratchet');

        $loop->run();
    }
}