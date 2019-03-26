<?php

class Util_Uri
{

    /**
     * 获取URL的Domain
     *
     * @param
     *            $p_sUrl
     */
    public static function getDomain ($p_sUrl)
    {
        if (empty($p_sUrl)) {
            $sDomain = '';
        } else {
            $sDomain = parse_url($p_sUrl, PHP_URL_HOST);
        }

        return $sDomain;
    }

    /**
     * 获取文件服务路径
     *
     * @param string $p_sFileKey
     * @param string $p_sExtension
     * @param int $p_iWidth
     * @param int $p_iHeight
     * @param string $p_sOption
     * @return string
     */
    public static function getDFSViewURL ($p_sFileKey, $p_iWidth = 0, $p_iHeight = 0, $p_sOption = '', $sBiz = '')
    {
        $sViewUrl = Yaf_G::getConf('dfsview', 'url');

        if (! $p_sFileKey) {
            return '';
        }
        if ('banner' == $sBiz) {
            $sViewUrl .= '/fjbanner';
        }

        list ($p_sKey, $p_sExt) = explode('.', $p_sFileKey);
        if (0 == $p_iWidth && 0 == $p_iHeight) {
            return $sViewUrl . '/' . $p_sKey . '.' . $p_sExt;
        } else {
            if ('' == $p_sOption) {
                return $sViewUrl . '/' . $p_sKey . '/' . $p_iWidth . 'x' . $p_iHeight . '.' . $p_sExt;
            } else {
                return $sViewUrl . '/' . $p_sKey . '/' . $p_iWidth . 'x' . $p_iHeight . '_' . $p_sOption . '.' . $p_sExt;
            }
        }
    }

    /**
     * 获得CRIC文件服务路径
     * @param unknown $p_sFileKey
     * @param number $p_iWidth
     * @param number $p_iHeight
     * @param number $p_iWaterPiC
     *   0		无水印
     *   1	新浪背景	http://i.data.house.sina.com.cn/images/waterpic/sina_background.png
     *   2	百度背景	http://i.data.house.sina.com.cn/images/waterpic/bd_background.png
     *   3	百度二手房	http://pic.fangyou.com/LOGO/baiduesflogo.png
     *   4	克尔瑞	http://pic.fangyou.com/LOGO/circlogo2.png
     *   5	商旅	http://pic.fangyou.com/LOGO/shanglvlogo.png
     *   6	新浪二手房	http://pic.fangyou.com/LOGO/sinaesflogo_20111108.png
     *   7	写字楼	http://pic.fangyou.com/LOGO/xzil.png
     *   8	Cric2012	http://pic.fangyou.com/LOGO/cric2012_log.png
     *   9	旅游地产	http://pic.fangyou.com/LOGO/tripwatermark_new2.png
     *   10	筑想网	http://pic.fangyou.com/LOGO/zxw.png
     * @param number $p_iWaterPos
     *   0	无水印
     *   1	左上角
     *   2	右上角
     *   3	左下角
     *   4	右下角
     *   5	中间
     *   6	背景
     *   7	在左起0，高30%的地方加水印
     *   8	右下角并上移30px
     * @param number $p_iCutType
     *   0	按指定尺寸缩放
     *   1	中间截取
     *   2	补白
     *   3	中间截取，90%质量压缩
     *   4	补白，90%质量压缩
     *   5	按原图宽高比缩放
     *   6	按指定尺寸缩放加60质量
     *   7	中间截取加60质量
     *   8	补白加60质量
     */
    public static function getCricViewURL ($p_sFileKey, $p_iWidth = 0, $p_iHeight = 0, $p_iWaterPiC = 0, $p_iWaterPos = 0, $p_iCutType = 0)
    {
        if (strpos($p_sFileKey, '.') !== false) {
            if ($p_iWidth == 1 && $p_iHeight == 1) {
                return self::getDFSViewURL($p_sFileKey, 0, 0);
            }
            return self::getDFSViewURL($p_sFileKey, $p_iWidth, $p_iHeight);
        }
        $iWhich = hexdec(substr($p_sFileKey,-1));
        $sServers = array('',2,3,4,5,6,7,8,9,10,11,12,13,14,15,16);
        return 'http://get.file' . $sServers[$iWhich] . '.dc.cric.com/'
             . $p_sFileKey . '_' . $p_iWidth . 'X' . $p_iHeight . '_'
             . $p_iWaterPiC . '_' . $p_iWaterPos . '_' . $p_iCutType
             . '.jpg';
    }


    public static function getNewsImgUrl($p_sFileKey, $p_iWidth = 0, $p_iHeight = 0, $p_sOption = '', $sBiz = '')
    {
        if(strpos($p_sFileKey, 'house.sina.com') > 0) {
            return $p_sFileKey;
        }
        return self::getDFSViewURL($p_sFileKey, $p_iWidth, $p_iHeight, $p_sOption, $sBiz);
    }

    /*
     * 生成news详情页地址
     * param $iNewsID
     * param $cityName 城市名称（拼音全拼）
     * param $iType 1生成网页地址，2生成h5页面地址
    */
    public static function getNewsDetailUrl($iNewsID, $cityName, $iType = 1, $isApp = false)
    {
        $newsUrl = Yaf_G::getConf('news','domain');
        $newsUrl = 'http://'.$newsUrl;

        if(1 == $iType) {
            $newsUrl .= '/'.$cityName. '/news/detail/'. $iNewsID;
        }else if(2 == $iType){
            $newsUrl = Yaf_G::getConf('touchweb','domain');
            $newsUrl = 'http://'.$newsUrl;
            if ($isApp) {
                $newsUrl .= '/h5/news/detail?iNewsID='. $iNewsID.'&noHeader=1';
            } else {
                $newsUrl .= '/h5/news/detail?iNewsID='. $iNewsID;
            }
        }

        return $newsUrl;
    }

    /*
     * 生成news详情页地址
     * param $iEavID 评测报告id
     * param $iSubEavID 对应子章节id
     * param $cityName 城市名称（拼音全拼）
     * param $iType 1生成网页地址，2生成h5页面地址
    */
    public static function getEvaluationDetailUrl($iEavID, $iSubEavID, $cityName, $iType = 1, $isApp = false)
    {
        $EavUrl = Yaf_G::getConf('touchweb','domain');
        $EavUrl = 'http://'.$EavUrl;

        $urlMapping = array(
            1 => 'hxanalyseIndex',
            2 => 'hxanalyseHx',
            3 => 'zxstandardindex',
            4 => 'zxstandardAnalysis',
            5 => 'sqpzindex',
            6 => 'sqpzScenery',
            7 => 'sqpzBuild',
            8 => 'sqpzPublic',
            9 => 'sqpzConfig',
            10 => 'sqpzParking',
            11 => 'wyfwindex',
            12 => 'wyfwService',
            13 => 'trafficindex',
            14 => 'trafficRail',
            15 => 'trafficBus',
            16 => 'regionindex',
            17 => 'regionEducate',
            18 => 'regionMedical',
            19 => 'regionBusiness',
            20 => 'regionPublic',
            21 => 'badfactorIndex',
            22 => 'badfactorOutside'
        );

        if(array_key_exists($iSubEavID, $urlMapping)) {
            $subUrl = $urlMapping[$iSubEavID];

            if(1 == $iType) {
                $EavUrl .= '/'.$cityName. '/Evaluation/'.$subUrl. '?eID='. $iEavID;
            }else if(2 == $iType){
                if ($isApp) {
                    $EavUrl .= '/h5/Evaluation/'.$subUrl. '?eID='. $iEavID.'&noHeader=1';
                } else {
                    $EavUrl .= '/h5/Evaluation/'.$subUrl. '?eID='. $iEavID;
                }
            }
        }

        return $EavUrl;
    }

    /*
     * 获取默认图片地址
     * $type 1楼盘列表2分析师3楼盘详情4导购列表（文章列表）
    */
    public static function getDefaultImg($type = 1)
    {
        $staticUrl = Yaf_G::getConf('static','domain');
        $imgUrl = null;
        switch($type) {
            case 1:
                $imgUrl = 'http://'. $staticUrl.'/img/blistd.png';
                break;
            case 2:
                $imgUrl = 'http://'. $staticUrl.'/touchweb/Evaluation/image/nopic.png';
                break;
            case 3:
                $imgUrl = 'http://'. $staticUrl.'/img/bd.png';
                break;
            case 4:
                $imgUrl = 'http://'. $staticUrl.'/img/default.png';
                break;
            default:
                break;

        }

        return $imgUrl;
    }

}