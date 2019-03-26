<?php
/**
 *
 * User: wangyongtao
 * Date: 2019/2/22
 * Time: 11:54 AM
 */

require_once __DIR__."/../conf.php";

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('localhost',5672,'wyt','123456');
$channel = $connection->channel();

$channel->queue_declare('hello', false, false, false, false);

$data = implode(' ', array_slice($argv, 1));

empty($data) && $data = "Hello World!";

$msg = new AMQPMessage($data);
$channel->basic_publish($msg, '', 'hello');

echo " [x] Sent 'Hello World!'\n";

$channel->close();
$connection->close();