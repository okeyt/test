<?php
require_once __DIR__."/../conf.php";

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('localhost',5672,'wyt','123456');
$channel    = $connection->channel();


$channel->queue_declare('task_queue',false,true,false,false);

$data = implode(' ',array_slice($argv,1));

empty($data) && $data = "Hello World!";

$msg = new AMQPMessage($data,['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);

$channel->basic_publish($msg,'','task_queue');

echo ' [x] Sent ',$data,"\n";

$channel->close();
$connection->close();
?>