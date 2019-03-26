<?php

/**
 * 地图位置相关的使用
 * @author xiejinci
 *
 */

class Util_Gps
{
    /**
     * 取得高德GPS
     * @param string $sCity
     * @param string $sAddr
     * @return multitype:number
     */
    public static function getAmapGps ($sCity, $sAddr)
    {
        $opts = array(
            'http' => array(
                'method' => "GET",
                'timeout' => 10
            )
        );
        $context = stream_context_create($opts);
        $url = "http://restapi.amap.com/v3/place/text?keywords=" . urlencode($sAddr) . "&key=8325164e247e15eea68b59e89200988b&city=" . urlencode($sCity);
        $aRet = file_get_contents($url, false, $context);
        $aRet = json_decode($aRet, true);
        $sRet = isset($aRet['pois']['0']['location']) ? $aRet['pois']['0']['location'] : '0,0';
        $aTmp = explode(',', $sRet);
        return array(
            'lng' => $aTmp[0],
            'lat' => $aTmp[1]
        );
    }

    /**
     * 取得百度GPS
     * @param string $sCity
     * @param string $sAddr
     * @return multitype:number
     */
    public static function getBaiduGps ($sCity, $sAddr)
    {
        $opts = array(
            'http' => array(
                'method' => "GET",
                'timeout' => 10
            )
        );
        $context = stream_context_create($opts);
        $url = "http://api.map.baidu.com/geocoder?address=" . urlencode($sAddr) . "&output=json&key=37492c0ee6f924cb5e934fa08c6b1676&city=" . urlencode($sCity);
        $aRet = file_get_contents($url, false, $context);
        $aRet = json_decode($aRet, true);
        return isset($aRet['result']['location']) ? $aRet['result']['location'] : array(
            'lng' => 0,
            'lat' => 0
        );
    }
}