<?php
/**
 *
 * User: wangyongtao
 * Date: 2019/2/15
 * Time: 6:03 PM
 */

$file = "/www/vanilla";

if ( !is_dir($file) ) {
    exit('not dir');
}

function getDir($path)
{
    //判断目录是否为空
    if(!file_exists($path)) {
        return [];
    }

    $files = scandir($path);
    $fileItem = [];
    foreach($files as $v) {
        $newPath = $path .DIRECTORY_SEPARATOR . $v;
        if(is_dir($newPath) && $v != '.' && $v != '..') {
            $fileItem = array_merge($fileItem, getDir($newPath));
        }else if(is_file($newPath)){
            $fileItem[] = $newPath;
        }
    }

    return $fileItem;
}

$arr = getDir($file);

sort($arr);
var_dump($arr);