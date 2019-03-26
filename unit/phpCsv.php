<?php

class Util_phpCsv
{

    /**
     * 生成cvs文件
     * @param $filename
     * @param $data
     */
    public static function export_csv($filename)
    {
        header("Content-type:text/csv");
        header("Content-Disposition:attachment;filename=".$filename);
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:0'); header('Pragma:public');
        //echo chr(0xEF).chr(0xBB).chr(0xBF);//加BOM防止utf8乱码
    }
}