<?php
/**
 *
 * User: wangyongtao
 * Date: 2019/2/2
 * Time: 10:19 AM
 */

$redis = new Redis();
$redis->connect('localhost',6379);
$redis->auth('Justsoso123');
$redis->setOption(Redis::OPT_READ_TIMEOUT,-1);
$redis->subscribe(['wyt','wyt_talk','wyt_to'],function($redis,$channel,$message){

    var_dump([$redis,$channel,$message]);
});