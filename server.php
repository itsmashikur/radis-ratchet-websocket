<?php

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use React\EventLoop\Factory as EventLoopFactory;
use Clue\React\Redis\Factory as RedisFactory;
use React\EventLoop\Loop;
use React\Socket\Server as SocketServer;


require 'vendor/autoload.php';

class WebSocketServer implements MessageComponentInterface
{
    protected $clients;
    protected $redis;
    protected $pubSubChannelName = 'message'; 

    public function __construct($loop)
    {
        $this->clients = new \SplObjectStorage();

        // set redis PubSub loop
        $this->registerRedisPubSubLoop($loop);
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);

        echo "New connection! ({$conn->resourceId})" . PHP_EOL;

        //send message to client
        $conn->send('Hello from server!');
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        // Handle incoming messages here
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} has disconnected" . PHP_EOL;
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}" . PHP_EOL;
        $conn->close();
    }

    public function registerRedisPubSubLoop($loop)
    {
        $channel = 'messages';
        
        $redis = new Clue\React\Redis\RedisClient('localhost:6379');
        
        $redis->subscribe($channel)->then(function () {
        
            echo 'Now subscribed to channel ' . PHP_EOL;
        
        }, function (Exception $e) use ($redis) {
        
            $redis->close();
            
            echo 'Unable to subscribe: ' . $e->getMessage() . PHP_EOL;
        });
        
        $redis->on('message', function (string $channel, string $message) {
            
            echo 'Message on test' . $channel . ': ' . $message . PHP_EOL;

            foreach ($this->clients as $client) {
                $client->send($message);
            }


        });
        
    }
}

$loop = EventLoopFactory::create();

$webSocket = new WebSocketServer($loop);

$socketServer = new SocketServer('0.0.0.0:8080', $loop);

$ioServer = new IoServer(new HttpServer(new WsServer($webSocket)), $socketServer, $loop);

$loop->run();

