<?php
/**
 *
 * User: wangyongtao
 * Date: 2019/2/22
 * Time: 11:54 AM
 */

require_once __DIR__."/../conf.php";


use PhpAmqpLib\Connection\AMQPStreamConnection;
$connection = new AMQPStreamConnection('localhost', 5672, 'wyt', '123456');
$channel = $connection->channel();
$channel->queue_declare('hello', false, false, false, false);
echo " [*] Waiting for messages. To exit press CTRL+C\n";
$callback = function ($msg) {
    echo ' [x] Received ', $msg->body, "\n";
};
$channel->basic_consume('hello', '', false, true, false, false, $callback);
while (count($channel->callbacks)) {
    $channel->wait();
}
$channel->close();
$connection->close();