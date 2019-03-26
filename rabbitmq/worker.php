<?php
/**
 *
 * User: wangyongtao
 * Date: 2019/2/22
 * Time: 1:40 PM
 */

require_once __DIR__."/../conf.php";

use PhpAmqpLib\Connection\AMQPStreamConnection;
$connection = new AMQPStreamConnection('localhost', 5672, 'wyt', '123456');
$channel = $connection->channel();
//队列名称 第三个参数 $durable true持久化 false 不持久化
$channel->queue_declare('task_queue', false, true, false, false);
echo " [*] Waiting for messages. To exit press CTRL+C\n";
$callback = function ($msg) {
    echo ' [x] Received ', $msg->body, "\n";
    sleep(substr_count($msg->body, '.'));
    echo " [x] Done\n";
    $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
};
$channel->basic_qos(null, 1, null);
// 第四个参数$no_ack false 需要确认 ack
$channel->basic_consume('task_queue', '', false, false, false, false, $callback);
while (count($channel->callbacks)) {
    $channel->wait();
}
$channel->close();
$connection->close();