<?php
/**
 * Created by PhpStorm.
 * User: yaobiqing
 * Date: 14/12/24
 * Time: 上午9:49
 */

class Util_ImageGD
{
    /**
     * @var string
     * 水印图片的地址
     */
    protected $_sWaterMarkImgPath;

    /**
     * @var array
     * 需要水印的尺寸
     * 最小打水印尺寸为241X241
     */
    protected $_aNeedWaterMarkSize = array(
        'iWidth'  => 241,
        'iHeight' => 241
    );

    /**
     * @var array
     * 水印的位置
     */
    protected $_sWaterMarkPosition = 'bottom-right';

    /**
     * @var array
     * 生成图片的函数
     */
    protected $_aImgCreateTypeFunc = array(
        'image/jpg'  => 'imagejpeg',
        'image/jpeg' => 'imagejpeg',
        'jpg'        => 'imagejpeg',
        'jpeg'       => 'imagejpeg',
        'image/png'  => 'imagepng',
        'png'        => 'imagepng',
        'image/gif'  => 'imagegif',
        'gif'        => 'imagegif'
    );

    public function __construct()
    {
        $this->_sWaterMarkImgPath = Util_Common::getConf('defaultWaterMarkPath', 'image');
    }


    /**
     * @param $p_sPosition
     */
    public function setWaterMarkPosition($p_sPosition)
    {
        $this->_sWaterMarkPosition = $p_sPosition;
    }
    /**
     * @param $p_sWaterImg
     */
    public function setWaterMarkImg($p_sWaterImg)
    {
        $this->_sWaterMarkImgPath = $p_sWaterImg;
    }

    /**
     * @return string
     */
    public function getWaterMarkImg()
    {
        return $this->_sWaterMarkImgPath;
    }

    /**
     * @param $p_iWidth
     * @param $p_iHeight
     */
    public function setWaterMarkSize($p_iWidth, $p_iHeight)
    {
        $this->_aNeedWaterMarkSize['iWidth']  = $p_iWidth;
        $this->_aNeedWaterMarkSize['iHeight'] = $p_iHeight;
    }

    /**
     * @return array
     */
    public function getWaterMarkSize()
    {
        return $this->_aNeedWaterMarkSize;
    }

    /**
     * @param $p_oImg
     * 检测图片是否需要打水印
     */
    protected function chkWaterMark($p_oImg)
    {
        $iWidth  = imagesx($p_oImg);
        $iHeight = imagesy($p_oImg);
        if ($iWidth > $this->_aNeedWaterMarkSize['iWidth'] && $iHeight > $this->_aNeedWaterMarkSize['iHeight']) {
            return true;
        }

        return false;
    }

    /**
     * @param $p_oImg
     * @return mixed
     */
    protected function waterMark($p_oImg)
    {
        $oWaterMarkImg    = imagecreatefrompng($this->_sWaterMarkImgPath);
        $iWaterMarkWidth  = imagesx($oWaterMarkImg);
        $iWaterMarkHeight = imagesy($oWaterMarkImg);

        $iImgWidth  = imagesx($p_oImg);
        $iImgHeight = imagesy($p_oImg);
        //图片大小小于水印大小加上30 就不打水印了
        if ($iImgWidth < $iWaterMarkWidth + 30 || $iImgHeight < $iWaterMarkHeight + 30) {
            imagedestroy($oWaterMarkImg);
            return $p_oImg;
        }

        switch($this->_sWaterMarkPosition) {
            //左下
            case "bottom-left":
                $iDesX = 30;
                $iDesY = $iImgHeight - $iWaterMarkHeight - 30;
                break;
            //右下
            case "bottom-right":
                $iDesX = $iImgWidth - $iWaterMarkWidth - 30;
                $iDesY = $iImgHeight - $iWaterMarkHeight - 30;
                break;
            //中下
            case "bottom-middle":
                $iDesX = ($iImgHeight / 2) - 15;
                $iDesY = $iImgHeight - $iWaterMarkHeight - 30;
                break;
            //左上
            case "top-left":
                $iDesX = 30;
                $iDesY = 30;
                break;
                break;
            //中上
            case "top-middle":
                $iDesX = ($iImgHeight / 2) - 15;
                $iDesY = 30;
                break;
                break;
            //右上
            case "top-right":
                $iDesX = $iImgWidth - $iWaterMarkWidth - 30;
                $iDesY = 30;
                break;
                break;
            //默认右下
            default:
                $iDesX = $iImgWidth - $iWaterMarkWidth - 30;
                $iDesY = $iImgHeight - $iWaterMarkHeight - 30;
                break;
        }

        imagealphablending($oWaterMarkImg, true);

        imagecopy($p_oImg, $oWaterMarkImg,
            $iDesX, $iDesY,
            0, 0,
            $iWaterMarkWidth, $iWaterMarkHeight
        );

        imagedestroy($oWaterMarkImg);

        return $p_oImg;
    }

    /**
     * @param $p_oImg
     * @param $p_iWidth
     * @param $p_iHeight
     * @param bool $p_bCrop 是否裁剪 默认裁剪
     * @return resource
     */
    public function resize($p_oImg, $p_iWidth, $p_iHeight, $p_bCrop = true, $p_WaterMark = true)
    {
        $iOriginalWidth  = imagesx($p_oImg);
        $iOriginalHeight = imagesy($p_oImg);

        $iDesX = 0;
        $iDesY = 0;

        if (0 == $p_iWidth && 0 == $p_iHeight) {

            $oDesImg = $p_oImg;
        } else {
            $fRateWidth  = doubleval($p_iWidth) / doubleval($iOriginalWidth);
            $fRateHeight = doubleval($p_iHeight) / doubleval($iOriginalHeight);

            if ($fRateHeight != $fRateWidth) {
                if ($p_bCrop) {
                    if ($fRateWidth > $fRateHeight) {
                        $iTmpHeight      = $p_iHeight / $p_iWidth * $iOriginalWidth;
                        $iDesY           = ($iOriginalHeight - $iTmpHeight) / 2;
                        $iOriginalHeight = $iTmpHeight;
                    } else {
                        $iTmpWidth      = $p_iWidth * $iOriginalHeight / $p_iHeight;
                        $iDesX          = ($iOriginalWidth - $iTmpWidth) / 2;
                        $iOriginalWidth = $iTmpWidth;
                    }
                } else {
                    if ($fRateWidth > $fRateHeight) {
                        $p_iWidth = $fRateHeight * $iOriginalWidth;
                    } else {
                        $p_iHeight = $fRateWidth * $iOriginalHeight;
                    }
                }

            }
            $oDesImg = imagecreatetruecolor($p_iWidth, $p_iHeight);

            imagecopyresampled($oDesImg, $p_oImg, 0, 0, $iDesX, $iDesY,
                $p_iWidth, $p_iHeight,
                $iOriginalWidth, $iOriginalHeight
            );

            imagedestroy($p_oImg);
        }

        if ($this->chkWaterMark($oDesImg) && $p_WaterMark) {
            $oDesImg = $this->waterMark($oDesImg);
        }

        return $oDesImg;
    }

    /**
     * @param $p_oImg
     * @param string $p_sType
     * @param null $p_sFileName
     */
    public function createTypeImg($p_oImg, $p_sType = 'image/jpeg', $p_sFileName = null)
    {
        $sTypeImageFunc = isset($this->_aImgCreateTypeFunc[$p_sType]) ?
            $this->_aImgCreateTypeFunc[$p_sType] : 'imagejpeg';

        if ($p_sFileName) {
            if ($sTypeImageFunc == 'imagejpeg') {
                $sTypeImageFunc($p_oImg, $p_sFileName, 100);
            } else {
                $sTypeImageFunc($p_oImg, $p_sFileName);
            }

        } else {
            if ($sTypeImageFunc == 'imagejpeg') {
                $sTypeImageFunc($p_oImg, null, 100);
            } else {
                $sTypeImageFunc($p_oImg);
            }
        }
    }

    /**
     * @param $p_imgBlob
     * @return string
     */
    public function buildEtagFromImg($p_imgBlob)
    {
        $etag = substr(md5($p_imgBlob), 0, 8);
        return "\"$etag\"";
    }

    /**
     * @param $p_oImg
     * @param string $p_sType
     * @return string
     */
    public function getImageBlob($p_oImg, $p_sType = 'image/jpeg')
    {
        $sTmpfile = tempnam("/dev/shm/", "phpgd_");
        $this->createTypeImg($p_oImg, $p_sType, $sTmpfile);
        $image = file_get_contents($sTmpfile);
        @unlink($sTmpfile);
        return $image;
    }


}