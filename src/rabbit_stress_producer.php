<?php

require_once __DIR__ . '/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$numMessages = 10000;
$queueName = 'stress_test_queue';

try {
    echo "Connecting to RabbitMQ...\n";
    $connection = new AMQPStreamConnection('rabbitmq', 5672, 'guest', 'guest');
    $channel = $connection->channel();

    $channel->queue_declare($queueName, false, false, false, false);

    echo "Starting stress test: Sending $numMessages messages...\n";
    
    $start = microtime(true);

    for ($i = 1; $i <= $numMessages; $i++) {
        $data = json_encode([
            'id' => $i,
            'timestamp' => microtime(true),
            'payload' => str_repeat('A', 128) // 128 bytes of payload
        ]);
        
        $msg = new AMQPMessage($data);
        $channel->basic_publish($msg, '', $queueName);

        if ($i % 1000 === 0) {
            echo "Sent $i messages...\n";
        }
    }

    $end = microtime(true);
    $time = $end - $start;

    echo "\nFinished!\n";
    echo "Total time: " . round($time, 4) . " seconds\n";
    echo "Throughput: " . round($numMessages / $time, 2) . " msgs/sec\n";

    $channel->close();
    $connection->close();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
