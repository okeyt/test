<?php
/**
 * 下载文件
 */
class Util_Download {

    /**
     * 下载文件
     * @param unknown_type $filepath    文件路径 (share/tianshui/08830e924dff44de3abf132a70ff25b1.zip)
     * @param unknown_type $filename    文件名(json.zip)
     * @param unknown_type $mime        文件mime
     */
    public static function download($filepath, $filename, $mime = 'application/force-download'){

        if(!file_exists($filepath) || !is_file($filepath)){
            header('Content-Type: text/html; charset=utf8');
            exit('文件不存在,请联系管理员');
        }
        if(!is_readable($filepath)){
            header('Content-Type: text/html; charset=utf8');
            exit('文件不可读,请联系管理员!');
        }

        $ua = $_SERVER["HTTP_USER_AGENT"];

        $encoded_filename = urlencode($filename);
        $encoded_filename = str_replace("+", "%20", $encoded_filename);


        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private',false);
        header('Content-Type: '.$mime);
        header("Accept-Length:".filesize($filepath));
        if (preg_match("/MSIE/", $ua)) {
            header('Content-Disposition: attachment; filename="' . $encoded_filename . '"');
        } else if (preg_match("/Firefox/", $ua)) {
            header('Content-Disposition: attachment; filename*="utf8\'\'' . $filename . '"');
        } else {
            header('Content-Disposition: attachment; filename="' . $filename . '"');
        }
       // header('Content-Disposition: attachment; filename="'.basename($filename));
        header('Content-Transfer-Encoding: binary');
        header('Connection: close');
        readfile($filepath);
    }
}
