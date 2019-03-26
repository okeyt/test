<?php
class Util_Xml2Array {

    public static function build_xml ($array, $is_header=true) {
        $xml = $is_header ? "<?xml version='1.0' encoding='UTF-8'?>" : '';

        foreach($array as $key=>$val) {
            $xml .= is_numeric($key) ? '' : "<{$key}>";
            $xml .= is_array($val) ? self::build_xml($val,false) : $val;
            list($key,) = explode(' ',$key);
            $xml .= is_numeric($key) ? '' : "</{$key}>";;
        }
        return $xml;
    }

    public static function xml2json($source){
        $source = str_replace('&', ' ', $source);
        if(is_file($source)){
            $xml_array=simplexml_load_file($source);
        }else{
            $xml_array=simplexml_load_string($source);
        }
        $json = json_encode($xml_array);
        return $json;
    }

    public static function xml2array($source){
        $json = self::xml2json($source);
        return json_decode($json, true);
    }
}