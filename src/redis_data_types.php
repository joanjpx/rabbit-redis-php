<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Predis\Client;

try {
    $client = new Client([
        'scheme' => 'tcp',
        'host'   => 'redis',
        'port'   => 6379,
    ]);

    echo "--- 1. STRINGS ---\n";
    $client->set('app:name', 'Rabbit Redis PHP Demo');
    echo "Value: " . $client->get('app:name') . "\n\n";

    echo "--- 2. SETS (Unordered Unique Items) ---\n";
    $client->sadd('tags', ['php', 'docker', 'redis', 'rabbitmq']);
    $client->sadd('tags', 'php'); // Duplicate, won't be added
    $tags = $client->smembers('tags');
    echo "Tags: " . implode(', ', $tags) . "\n\n";

    echo "--- 3. LISTS (Ordered Items) ---\n";
    $client->del('tasks'); // Clear first
    $client->rpush('tasks', 'Initialize project');
    $client->rpush('tasks', 'Setup Docker');
    $client->rpush('tasks', 'Write code');
    $tasks = $client->lrange('tasks', 0, -1);
    echo "Tasks:\n";
    foreach ($tasks as $index => $task) {
        echo "  " . ($index + 1) . ". $task\n";
    }
    echo "\n";

    echo "--- 4. HASHES (Objects/Dictionaries) ---\n";
    $userKey = 'user:101';
    $client->hmset($userKey, [
        'id'    => 101,
        'name'  => 'Joan Perez',
        'email' => 'joan@example.com',
        'role'  => 'developer'
    ]);
    $userData = $client->hgetall($userKey);
    echo "User Data:\n";
    foreach ($userData as $field => $value) {
        echo "  $field: $value\n";
    }
    echo "\n";

    echo "--- 5. EXPIRATION (Volatile Key) ---\n";
    $client->setex('temp_token', 60, 'ABC-123-XYZ');
    echo "Temp Token: " . $client->get('temp_token') . "\n";
    echo "TTL: " . $client->ttl('temp_token') . " seconds\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
