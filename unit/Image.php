<?php

class Util_Image
{

    /**
     * 处理图片的类库
     * 
     * @var emu
     */
    static $_eType = 'imagick';

    /**
     * 使用Imagick重新绘制图片
     * 
     * @param string $p_sPath            
     * @param int $p_iWidth            
     * @param int $p_iHeight            
     * @param string $p_sExtension            
     * @param array $p_aOption            
     * @return blob
     */
    static function resizeImage_Imagick ($p_sPath, $p_iWidth, $p_iHeight, $p_sExtension, $p_aOption = array())
    {
        $oImage = new Imagick();
        $oImage->readImage($p_sPath);
        $iOWidth = $oImage->getImageWidth();
        $iOHeight = $oImage->getImageHeight();
        if ($iOWidth < $p_iWidth and $iOHeight < $p_iHeight) { // 不做拉伸图片处理
            $p_iWidth = $iOWidth;
            $p_iHeight = $iOHeight;
        }
        if (true === $p_aOption['bThumbnail']) {
            switch ($p_aOption['sMode']) {
                case 'cut': // 裁剪
                    $oImage->cropThumbnailImage($p_iWidth, $p_iHeight);
                    break;
                case 'zoom': // 缩放
                default:
                    switch ($p_aOption['sZoomMode']) {
                        case 'fill': // 填充
                            $oImage->thumbnailImage($p_iWidth, $p_iHeight);
                            break;
                        case 'scale': // 等比例缩放
                        default:
                            switch ($p_aOption['sZoomScaleMode']) {
                                case 'width':
                                    $oImage->thumbnailImage($p_iWidth, round($p_iWidth * $iOHeight / $iOWidth), true);
                                    break;
                                case 'height':
                                    $oImage->thumbnailImage(round($p_iHeight * $iOWidth / $iOHeight), $p_iHeight, true);
                                    break;
                                case 'mix':
                                default:
                                    $oImage->thumbnailImage($p_iWidth, $p_iHeight, true);
                                    break;
                            }
                            break;
                    }
                    break;
            }
        } else {
            switch ($p_aOption['sMode']) {
                case 'cut':
                    $oImage->cropImage($p_iWidth, $p_iHeight, round(($iOWidth - $p_iWidth) / 2), round(($iOHeight - $p_iHeight) / 2));
                    break;
                case 'zoom':
                default:
                    switch ($p_aOption['sZoomMode']) {
                        case 'fill':
                            $oImage->resizeImage($p_iWidth, $p_iHeight, Imagick::FILTER_CATROM, 1);
                            break;
                        case 'scale':
                        default:
                            switch ($p_aOption['sZoomScaleMode']) {
                                case 'width':
                                    $oImage->resizeImage($p_iWidth, round($p_iWidth * $iOHeight / $iOWidth), Imagick::FILTER_CATROM, 1, true);
                                    break;
                                case 'height':
                                    $oImage->resizeImage(round($p_iHeight * $iOWidth / $iOHeight), $p_iHeight, Imagick::FILTER_CATROM, 1, true);
                                    break;
                                case 'mix':
                                default:
                                    $oImage->resizeImage($p_iWidth, $p_iHeight, Imagick::FILTER_CATROM, 1, true);
                                    break;
                            }
                            break;
                    }
                    break;
            }
        }
        if (false !== $p_aOption['mWatermark']) { // 水印
            $aWatermark = $p_aOption['mWatermark'];
            if (file_exists($aWatermark['sFilePath'])) {
                $oWaterMark = new Imagick();
                $oWaterMark->readImage($aWatermark['sFilePath']);
                $aEdge = $aWatermark['aEdge'];
                if (isset($aEdge['iLeft'])) {
                    $iPosX = $aEdge['iLeft'];
                } elseif (isset($aEdge['iRight'])) {
                    $iPosX = $oImage->getImageWidth() - $oWaterMark->getImageWidth() - $aEdge['iRight'];
                } else {
                    throw new Exception(__CLASS__ . ': configuration(resize_watermark_edge) lost.');
                    return false;
                }
                if (isset($aEdge['iUp'])) {
                    $iPosY = $aEdge['iUp'];
                } elseif (isset($aEdge['iDown'])) {
                    $iPosY = $oImage->getImageHeight() - $oWaterMark->getImageHeight() - $aEdge['iDown'];
                } else {
                    throw new Exception(__CLASS__ . ': configuration(resize_watermark_edge) lost.');
                    return false;
                }
                $oImage->compositeImage($oWaterMark, Imagick::COMPOSITE_DEFAULT, $iPosX, $iPosY);
                $oWaterMark->clear();
                $oWaterMark->destroy();
            } else {
                throw new Exception(__CLASS__ . ': configuration(resize_watermark_path) lost.');
                return false;
            }
        }
        
        $oImage->setImageFormat($p_sExtension);
        $oImage->setImageCompression(Imagick::COMPRESSION_JPEG);
        $a = $oImage->getImageCompressionQuality() * 0.75;
        if ($a == 0) {
            $a = 75;
        }
        $oImage->setImageCompressionQuality($a);
        $oImage->stripImage();
        $blImage = $oImage->getImageBlob();
        $oImage->clear();
        $oImage->destroy();
        return $blImage;
    }

    /**
     * 重新绘制图片
     * 
     * @param string $p_sPath            
     * @param int $p_iWidth            
     * @param int $p_iHeight            
     * @param string $p_sExtension            
     * @param array $p_aOption            
     * @return blob
     */
    static function resizeImage ($p_sPath, $p_iWidth, $p_iHeight, $p_sExtension, $p_aOption = array())
    {
        switch (self::$_eType) {
            case 'gd':
                return self::resizeImage_GD($p_sPath, $p_iWidth, $p_iHeight, $p_sExtension, $p_aOption);
                break;
            case 'imagick':
                return self::resizeImage_Imagick($p_sPath, $p_iWidth, $p_iHeight, $p_sExtension, $p_aOption);
                break;
        }
    }

    /**
     * 使用GD重新绘制图片
     * 
     * @param string $p_sPath            
     * @param int $p_iWidth            
     * @param int $p_iHeight            
     * @param string $p_sExtension            
     * @param array $p_aOption            
     * @return blob
     */
    static function resizeImage_GD ($p_sPath, $p_iWidth, $p_iHeight, $p_sExtension, $p_aOption = array())
    {
        /*
         * 图片质量 jpg是否渐进式jpg gif是否背景透明 是否等比例缩放 是剪裁还是缩放 缩放的情况下是否要填充空白 等比例缩放是满足高还是满足宽还是都满足
         */
        switch ($p_sExtension) {
            case 'jpg':
                $oImage = imagecreatefromjpeg($p_sPath);
                break;
        }
        $iOWidth = imagesx($oImage);
        $iOHeight = imagesy($oImage);
        if ($iOWidth < $p_iWidth and $iOHeight < $p_iHeight) { // 不做拉伸图片处理
            $p_iWidth = $iOWidth;
            $p_iHeight = $iOHeight;
        }
        if (true === $p_aOption['bThumbnail']) {
            switch ($p_aOption['sMode']) {
                case 'cut': // 裁剪
                    $oImage->cropThumbnailImage($p_iWidth, $p_iHeight);
                    break;
                case 'zoom': // 缩放
                default:
                    switch ($p_aOption['sZoomMode']) {
                        case 'fill': // 填充
                            $oImage->thumbnailImage($p_iWidth, $p_iHeight);
                            break;
                        case 'scale': // 等比例缩放
                        default:
                            switch ($p_aOption['sZoomScaleMode']) {
                                case 'width':
                                    $oImage->thumbnailImage($p_iWidth, round($p_iWidth * $iOHeight / $iOWidth), true);
                                    break;
                                case 'height':
                                    $oImage->thumbnailImage(round($p_iHeight * $iOWidth / $iOHeight), $p_iHeight, true);
                                    break;
                                case 'mix':
                                default:
                                    $oImage->thumbnailImage($p_iWidth, $p_iHeight, true);
                                    break;
                            }
                            break;
                    }
                    break;
            }
        } else {
            switch ($p_aOption['sMode']) {
                case 'cut':
                    $oImage->cropImage($p_iWidth, $p_iHeight, round(($iOWidth - $p_iWidth) / 2), round(($iOHeight - $p_iHeight) / 2), true);
                    break;
                case 'zoom':
                default:
                    switch ($p_aOption['sZoomMode']) {
                        case 'fill':
                            $oImage->resizeImage($p_iWidth, $p_iHeight, Imagick::FILTER_CATROM);
                            break;
                        case 'scale':
                        default:
                            switch ($p_aOption['sZoomScaleMode']) {
                                case 'width':
                                    $oImage->resizeImage($p_iWidth, round($p_iWidth * $iOHeight / $iOWidth), Imagick::FILTER_CATROM, 1, true);
                                    break;
                                case 'height':
                                    $oImage->resizeImage(round($p_iHeight * $iOWidth / $iOHeight), $p_iHeight, Imagick::FILTER_CATROM, 1, true);
                                    break;
                                case 'mix':
                                default:
                                    $oImage->resizeImage($p_iWidth, $p_iHeight, Imagick::FILTER_CATROM, 1, true);
                                    break;
                            }
                            break;
                    }
                    break;
            }
        }
        
        /*
         * if($iOWidth>$p_iWidth or $iOHeight>$p_iHeight){ if($p_aOption){ $oImage->thumbnailImage($p_iWidth,$p_iHeight,true); }else{ $oImage->resizeImage($p_iWidth,$p_iHeight,Imagick::FILTER_CATROM,1); } } if(false!==$p_aOption['mWatermark']){ if(file_exists($p_aOption['mWatermark'])){ $blWaterMark=file_get_contents($p_aOption['mWatermark']); $oWaterMark=new Imagick(); $oWaterMark->readImageBlob($blWaterMark); $iPosX=$oImage->getImageWidth()-$oWaterMark->getImageWidth()-30; $iPosY=$oImage->getImageHeight()-$oWaterMark->getImageHeight()-30; $oImage->compositeImage($oWaterMark,Imagick::COMPOSITE_DEFAULT,$iPosX,$iPosY); $oWaterMark->clear(); $oWaterMark->destroy(); }else{ throw new Exception(__CLASS__ . ': configuration(resize_watermark) lost.'); return false; } }
         */
        
        $oImage->setImageFormat($p_sExtension);
        $oImage->setImageCompression(Imagick::COMPRESSION_JPEG);
        /*
         * $a = $imagick->getImageCompressionQuality() * 0.75; if ($a == 0) { $a = 75; } $imagick->setImageCompressionQuality($a);
         */
        $oImage->stripImage();
        $blImage = $oImage->getImageBlob();
        $oImage->clear();
        $oImage->destroy();
        return $blImage;
    }

    /**
     * 生成验证码图片
     * 
     * @param int $p_iWidth            
     * @param int $p_iHeight            
     * @param string $p_sStr            
     * @param int $p_iFontSize            
     * @param int $p_iPointDensity            
     * @param int $p_iCircleDensity            
     * @param int $p_iFontAngle            
     */
    static function createIdentifyCodeImage ($p_iWidth, $p_iHeight, $p_sStr, $p_iFontSize = 0, $p_iPointDensity = 0, $p_iCircleDensity = 0, $p_iFontAngle = 0)
    {
        // 获取各种默认值
        $sTextFont = Util_Common::getConfig('sImgFont');
        if (0 == $p_iFontSize) {
            $p_iFontSize = round($p_iHeight * 3 / 5);
        }
        if (0 == $p_iPointDensity) {
            $p_iPointDensity = round($p_iHeight * $p_iWidth / 100);
        }
        if (0 == $p_iCircleDensity) {
            $p_iCircleDensity = round($p_iHeight * $p_iWidth / 200);
        }
        // 生成画布
        $oImg = imagecreatetruecolor($p_iWidth, $p_iHeight);
        
        // 填充颜色
        $bgc = imagecolorallocate($oImg, 255, 255, 255);
        imagefill($oImg, 0, 0, $bgc);
        
        // 获取字体范围大小
        $aTextSize = imagettfbbox($p_iFontSize, $p_iFontAngle, $sTextFont, $p_sStr);
        $iTextHeight = (max($aTextSize[1], $aTextSize[3]) - min($aTextSize[5], $aTextSize[7]));
        $iTextWidth = (max($aTextSize[4], $aTextSize[2]) - min($aTextSize[0], $aTextSize[6]));
        // 字体起始位置
        $iTextStartLeft = ($p_iWidth - $iTextWidth) / 2;
        $iTextStartHeight = $p_iHeight / 2 + $iTextHeight / 2;
        // 字体颜色
        $colors = [
            [
                0,
                10,
                210
            ],
            [
                24,
                157,
                10
            ],
            [
                177,
                70,
                20
            ]
        ];
        $colorsValue = $colors[array_rand($colors)];
        $oTextColor = imagecolorallocate($oImg, $colorsValue[0], $colorsValue[1], $colorsValue[2]);
        // 往画布上画字符串
        // imagettftext($oImg, $p_iFontSize, $p_iFontAngle, $iTextStartLeft, $iTextStartHeight, $oTextColor, $sTextFont, $p_sStr);
        
        $len = strlen($p_sStr);
        $_x = ($p_iWidth - 40) / $len;
        
        for ($i = 0; $i < $len; $i ++) {
            $iTextStartLeft = $_x * $i + mt_rand(20, 25);
            imagettftext($oImg, $p_iFontSize, mt_rand(- 10, 10), $iTextStartLeft, $iTextStartHeight, $oTextColor, $sTextFont, $p_sStr[$i]);
        }
        
        for ($i = 0; $i < 100; $i ++) {
            $color = imagecolorallocate($oImg, rand(50, 220), rand(50, 220), rand(50, 220));
            imagesetpixel($oImg, rand(0, $p_iWidth), rand(0, $p_iHeight), $color);
        }
        
        // 往画布上画线条
        for ($i = 0; $i < 5; $i ++) {
            $color = imagecolorallocate($oImg, mt_rand(0, 156), mt_rand(0, 156), mt_rand(0, 156));
            imageline($oImg, mt_rand(0, $p_iWidth), mt_rand(0, $p_iHeight), mt_rand(0, $p_iWidth), mt_rand(0, $p_iHeight), $color);
        }
        
        ob_start();
        imagegif($oImg);
        $blImage = ob_get_contents();
        ob_end_clean();
        imagedestroy($oImg);
        return $blImage;
    }
}