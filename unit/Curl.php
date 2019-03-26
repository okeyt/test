<?php

class Util_Curl
{

    public static function post ($xml, $url)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 1);
        
        $timeout = 1;
        $timeout = $timeout ? $timeout : 1;
        curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($curl, CURLOPT_HEADER, 1);
        
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            "Content-type:text/xml; charset=utf-8"
        ));
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $xml);
        $rs = curl_exec($curl);
        $info = curl_getinfo($curl);
        
        $flag = true;
        
        if ((int) $info['http_code'] >= 400 || (int) $info['http_code'] < 100) {
            $flag = false;
        }
        
        if ((int) $info['http_code'] != 200) {
            // $log_file_array = APF::get_instance()->get_config($curl_log);
            // $log_file = $log_file_array[$city_group];
            // $command = "echo \"".date("Y-m-d H:i:s",time())." http_code:".$info['http_code']."; \n result:".$rs." \" >> $log_file";
            // exec($command);
        }
        curl_close($curl);
        
        return $flag;
    }

    public static function filePostContents ($url, $params = array())
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, count($params));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        
        ob_start();
        curl_exec($ch);
        $result = ob_get_contents();
        ob_end_clean();
        curl_close($ch);
        
        return $result;
    }

    public static function fileGetContents ($url, $params = array())
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // 指定post数据
        curl_setopt($ch, CURLOPT_POST, 1);
        // 添加变量
        curl_setopt($ch, CURLOPT_POSTFIELDS, implode('&', $params));
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    /**
     * 远程上传文件
     */
    public static function uploadRemoteImg ($url, $filename, $params = array())
    {
        $fields = $params;
        $fields['filename'] = '@' . $filename;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 设置该参数配合curl_exec使用，设置后可使curl_exec执行请求成功后返回结果，而非默认的true
        $response = curl_exec($ch);
        $error = curl_error($ch);
        if ($error) {
            die($error);
        }
        curl_close($ch);
        return $response;
    }

    /**
     * 远程抓图
     */
    public static function getRemoteImg ($imgurl, $dir, $timeout = 60)
    {
        $ctx = stream_context_create(array(
            'http' => array(
                'timeout' => $timeout //设置一个超时时间，单位为秒
            )// 设置一个超时时间，单位为秒
        ));
        $content = file_get_contents($imgurl, 0, $ctx);
        
        $filename = $dir . substr($imgurl, strrpos($imgurl, '.'));
        file_put_contents($filename, $content);
        
        return $filename;
    }

    /**
     * 远程抓图
     */
    public static function getRemoteImg2 ($imgurl, $dir, $newname, $timeout = 60)
    {
        $ctx = stream_context_create(array(
            'http' => array(
                'timeout' => $timeout //设置一个超时时间，单位为秒
            )// 设置一个超时时间，单位为秒
        ));
        $content = file_get_contents($imgurl, 0, $ctx);
        
        $filename = $dir . $newname;
        file_put_contents($filename, $content);
        
        return $filename;
    }
}