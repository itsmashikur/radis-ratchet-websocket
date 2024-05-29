<?php
// Sender Script
require 'vendor/autoload.php';

$redis = new Predis\Client('127.0.0.1:6379');

while (true) {
    $message = readline("Enter message (or 'exit' to quit): ");

    if ($message === 'exit') {
        break;
    }

    $redis->publish('messages', $message);

    echo "Message sent: $message\n";
    
}
