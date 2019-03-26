<?php
/**
 * Created by PhpStorm.
 * User: yaobiqing
 * Date: 14/12/24
 * Time: 上午9:58
 */

class Util_ImageFactory
{


    public static function instance($p_sImplementation)
    {
        switch ($p_sImplementation) {
            case "gd":
            default:
                return new Util_ImageGD();
        }
    }
}