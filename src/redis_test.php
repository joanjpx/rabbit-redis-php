<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Predis\Client;

try {
    // Note: 'redis' is the service name in docker-compose.yml
    $client = new Client([
        'scheme' => 'tcp',
        'host'   => 'redis',
        'port'   => 6379,
    ]);

    echo "Connecting to Redis...\n";

    $client->set('test_key', 'Hello from PHP with Redis! ' . date('Y-m-d H:i:s'));
    $value = $client->get('test_key');

    echo "Value retrieved from Redis: $value\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
