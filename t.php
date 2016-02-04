<?php

use PhpAmqpLib\Connection\AMQPStreamConnection;


require __DIR__ . "/vendor/autoload.php";


// conection
$connection = new AMQPStreamConnection('necktie.docker', 5672, 'guest', 'guest');
$channel = $connection->channel();

list($queue_name, ,) = $channel->queue_declare("", false, false, true, false);
$channel->queue_bind($queue_name, 'test');

$callback = function($msg) {
    echo " [x] Received ", $msg->body, "\n";
};

$channel->basic_consume($queue_name, '', false, true, false, false, $callback);

while(count($channel->callbacks)) {
    $channel->wait();
}