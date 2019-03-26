<?php

class Util_Cookie
{

    public static function key ($name)
    {
        return Util_Common::getConf('prefix', 'cookie') . '_' . $name;
    }
    
    // 获取某个Cookie值
    public static function get ($name)
    {
        $value = isset($_COOKIE[self::key($name)]) ? self::decrypt($_COOKIE[self::key($name)]) : null;
        return json_decode($value, true);
    }
    
    // 设置某个Cookie值
    public static function set ($name, $value, $expire = 0, $path = '/', $domain = '')
    {
        if (empty($domain)) {
            $domain = Util_Common::getConf('domain', 'cookie');
        }
        if ($expire > 0) {
            $expire += time();
        }
        $value = json_encode($value);
        setcookie(self::key($name), self::encrypt($value, true), $expire, $path, $domain);
    }
    
    // 删除某个Cookie值
    public static function delete ($name, $path = '/', $domain = '')
    {
        if (empty($domain)) {
            $domain = Util_Common::getConf('domain', 'cookie');
        }
        self::set($name, '', time() - 3600, $path, $domain);
    }
    
    // cookie encrypt
    public static function encrypt ($txt, $dynamic = false)
    {
        $key = Util_Common::getConf('cryptkey', 'cookie');
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_.";
        $ikey = "-8vh4-HGf834-._8";
        if ($dynamic) {
            $nh1 = rand(0, 64);
            $nh2 = rand(0, 64);
            $nh3 = rand(0, 64);
        } else {
            $nh1 = 10;
            $nh2 = 39;
            $nh3 = 61;
        }
        
        $ch1 = $chars{$nh1};
        $ch2 = $chars{$nh2};
        $ch3 = $chars{$nh3};
        $nhnum = $nh1 + $nh2 + $nh3;
        $knum = 0;
        $i = 0;
        while (isset($key{$i}))
            $knum += ord($key{$i ++});
        $mdKey = substr(md5(md5(md5($key . $ch1) . $ch2 . $ikey) . $ch3), $nhnum % 8, $knum % 8 + 16);
        $txt = base64_encode($txt);
        $txt = str_replace(array(
            '+',
            '/',
            '='
        ), array(
            '-',
            '_',
            '.'
        ), $txt);
        $tmp = '';
        $j = 0;
        $k = 0;
        $tlen = strlen($txt);
        $klen = strlen($mdKey);
        for ($i = 0; $i < $tlen; $i ++) {
            $k = $k == $klen ? 0 : $k;
            $j = ($nhnum + strpos($chars, $txt{$i}) + ord($mdKey{$k ++})) % 64;
            $tmp .= $chars{$j};
        }
        
        $tmplen = strlen($tmp);
        $tmp = substr_replace($tmp, $ch3, $nh2 % ++ $tmplen, 0);
        $tmp = substr_replace($tmp, $ch2, $nh1 % ++ $tmplen, 0);
        $tmp = substr_replace($tmp, $ch1, $knum % ++ $tmplen, 0);
        return $tmp;
    }
    
    // cookie decrypt
    public static function decrypt ($txt)
    {
        $key = Util_Common::getConf('cryptkey', 'cookie');
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_.";
        $ikey = "-8vh4-HGf834-._8";
        $knum = 0;
        $i = 0;
        $tlen = strlen($txt);
        while (isset($key{$i}))
            $knum += ord($key{$i ++});
        $ch1 = $txt{$knum % $tlen};
        $nh1 = strpos($chars, $ch1);
        $txt = substr_replace($txt, '', $knum % $tlen --, 1);
        $ch2 = $txt{$nh1 % $tlen};
        $nh2 = strpos($chars, $ch2);
        $txt = substr_replace($txt, '', $nh1 % $tlen --, 1);
        $ch3 = $txt{$nh2 % $tlen};
        $nh3 = strpos($chars, $ch3);
        $txt = substr_replace($txt, '', $nh2 % $tlen --, 1);
        $nhnum = $nh1 + $nh2 + $nh3;
        $mdKey = substr(md5(md5(md5($key . $ch1) . $ch2 . $ikey) . $ch3), $nhnum % 8, $knum % 8 + 16);
        $tmp = '';
        $j = 0;
        $k = 0;
        $tlen = strlen($txt);
        $klen = strlen($mdKey);
        for ($i = 0; $i < $tlen; $i ++) {
            $k = $k == $klen ? 0 : $k;
            $j = strpos($chars, $txt{$i}) - $nhnum - ord($mdKey{$k ++});
            while ($j < 0)
                $j += 64;
            $tmp .= $chars{$j};
        }
        $tmp = str_replace(array(
            '-',
            '_',
            '.'
        ), array(
            '+',
            '/',
            '='
        ), $tmp);
        return trim(base64_decode($tmp));
    }
}