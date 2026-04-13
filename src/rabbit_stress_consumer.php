<?php

require_once __DIR__ . '/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;

$queueName = 'stress_test_queue';
$count = 0;
$startTime = null;

try {
    $connection = new AMQPStreamConnection('rabbitmq', 5672, 'guest', 'guest');
    $channel = $connection->channel();

    $channel->queue_declare($queueName, false, false, false, false);

    echo " [*] Stress Consumer waiting for messages. CTRL+C to exit.\n";

    $callback = function ($msg) use (&$count, &$startTime) {
        if ($startTime === null) {
            $startTime = microtime(true);
        }

        $count++;
        
        // Simular un pequeño trabajo (opcional)
        // usleep(1000); // 1ms por mensaje

        if ($count % 1000 === 0) {
            $elapsed = microtime(true) - $startTime;
            echo " [x] Processed $count messages... Avg speed: " . round($count / $elapsed, 2) . " msgs/sec\n";
        }
    };

    $channel->basic_consume($queueName, '', false, true, false, false, $callback);

    while ($channel->is_consuming()) {
        $channel->wait();
    }

    $channel->close();
    $connection->close();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
