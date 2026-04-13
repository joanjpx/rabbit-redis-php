<?php

require_once __DIR__ . '/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

try {
    // Note: 'rabbitmq' is the service name in docker-compose.yml
    $connection = new AMQPStreamConnection('rabbitmq', 5672, 'guest', 'guest');
    $channel = $connection->channel();

    $channel->queue_declare('hello', false, false, false, false);

    $data = "Hello RabbitMQ World! - " . date('H:i:s');
    $msg = new AMQPMessage($data);
    $channel->basic_publish($msg, '', 'hello');

    echo " [x] Sent '$data'\n";

    $channel->close();
    $connection->close();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
