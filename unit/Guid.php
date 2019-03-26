<?php

class Util_Guid
{

    public static function get ($sJoin = '')
    {
        mt_srand((double) microtime() * 10000);
        $charid = strtoupper(md5(uniqid(mt_rand(), true)));
        $uuid = substr($charid, 0, 8) . $sJoin . substr($charid, 8, 4) . $sJoin . substr($charid, 12, 4) . $sJoin . substr($charid, 16, 4) . $sJoin . substr($charid, 20, 12);
        return $uuid;
    }
}