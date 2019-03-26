<?php
$redis = new Redis();
$redis->connect('localhost',6379);
$redis->auth('Justsoso123');

$num = $redis->incr('wyt_num');

$num = 'gog' . $redis->get('wyt_num');
$redis->publish('wyt',$num);