<?php

class Util_Crypt
{

    /**
     * 加密函数
     * 
     * @param string $p_sValue            
     * @param string $p_sKey            
     * @return string
     */
    public static function encrypt ($p_sValue, $p_sKey)
    {
        return self::_code($p_sValue, 'ENCODE', $p_sKey);
    }

    /**
     * 解密函数
     * 
     * @param string $p_sValue            
     * @param string $p_sKey            
     * @return string
     */
    public static function decrypt ($p_sValue, $p_sKey)
    {
        return self::_code($p_sValue, 'DECODE', $p_sKey);
    }

    /**
     * 编码函数
     * 
     * @param string $p_sValue            
     * @param string $p_sOperation            
     * @param string $p_sKey            
     * @return string
     */
    private static function _code ($p_sValue, $p_sOperation, $p_sKey)
    {
        $p_sKey = md5($p_sKey);
        $p_sKey_length = strlen($p_sKey);
        $p_sValue = $p_sOperation == 'DECODE' ? base64_decode($p_sValue) : substr(md5($p_sValue . $p_sKey), 0, 8) . $p_sValue;
        $p_sValue_length = strlen($p_sValue);
        
        $rndkey = $box = array();
        $result = '';
        for ($i = 0; $i <= 255; $i ++) {
            $rndkey[$i] = ord($p_sKey[$i % $p_sKey_length]);
            $box[$i] = $i;
        }
        
        for ($j = $i = 0; $i < 256; $i ++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }
        
        for ($a = $j = $i = 0; $i < $p_sValue_length; $i ++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($p_sValue[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }
        
        if ($p_sOperation == 'DECODE') {
            if (substr($result, 0, 8) == substr(md5(substr($result, 8) . $p_sKey), 0, 8)) {
                return substr($result, 8);
            } else {
                return '';
            }
        } else {
            return str_replace('=', '', base64_encode($result));
        }
    }
}