<?php

class Util_PHPMail
{

    /**
     * 邮件字符集
     * 
     * @var string
     */
    private $_sCharset = 'UTF-8';

    /**
     * 邮件标题
     * 
     * @var string
     */
    private $_sSubject = '';

    /**
     * 发信人
     * 
     * @var array
     */
    private $_aFrom = array();

    /**
     * 抄送人
     * 
     * @var array
     */
    private $_aCC = array();

    /**
     * 暗送人
     * 
     * @var array
     */
    private $_aBCC = array();

    /**
     * 收件人
     * 
     * @var array
     */
    private $_aTo = array();

    /**
     * 回信目的地址
     * 
     * @var array
     */
    private $_aReplyTo = array();

    /**
     * 退信目的地址
     * 
     * @var array
     */
    private $_aReturnTo = array();

    /**
     * 自定义头部信息
     * 
     * @var string
     */
    private $_sHeader = '';

    /**
     * 附件
     * 
     * @var array
     */
    private $_aAttach = array();

    /**
     * 邮件内图片
     * 
     * @var array
     */
    private $_aMailImage = array();

    /**
     * 是否HTML
     * 
     * @var boolean
     */
    private $_bIsHTML = false;

    /**
     * 邮件内容
     * 
     * @var string
     */
    private $_sBody = '';

    /**
     * 构造函数
     */
    function __construct ()
    {}

    /**
     * 析构函数
     */
    function __destruct ()
    {}

    /**
     * 初始化邮件
     */
    function initMail ()
    {
        $this->_aFrom = array();
        $this->_aTo = array();
        $this->_aCC = array();
        $this->_aBCC = array();
        $this->_aReplyTo = array();
        $this->_aReturnTo = array();
        $this->_sHeader = '';
        $this->_sSubject = '';
        $this->_aAttach = array();
        $this->_aMailImage = array();
        $this->_bIsHTML = false;
        $this->_sBody = '';
    }

    /**
     * 设置来源邮箱
     * 
     * @param string $p_sAddr            
     * @param string $p_sName            
     */
    function addFrom ($p_sAddr, $p_sName = '')
    {
        $this->_aFrom[$p_sAddr] = $p_sName;
    }

    /**
     * 设置抄送者
     * 
     * @param string $p_sAddr            
     * @param string $p_sName            
     */
    function addCC ($p_sAddr, $p_sName = '')
    {
        $this->_aCC[$p_sAddr] = $p_sName;
    }

    /**
     * 设置暗送者
     * 
     * @param string $p_sAddr            
     * @param string $p_sName            
     */
    function addBCC ($p_sAddr, $p_sName = '')
    {
        $this->_aBCC[$p_sAddr] = $p_sName;
    }

    /**
     * 设置收件人
     * 
     * @param string $p_sAddr            
     * @param string $p_sName            
     */
    function addTo ($p_sAddr, $p_sName = '')
    {
        $this->_aTo[$p_sAddr] = $p_sName;
    }

    /**
     * 设置回复邮箱
     * 
     * @param string $p_sAddr            
     * @param string $p_sName            
     */
    function addReplyTo ($p_sAddr, $p_sName = '')
    {
        $this->_aReplyTo[$p_sAddr] = $p_sName;
    }

    /**
     * 设置退信邮箱
     * 
     * @param string $p_sAddr            
     * @param string $p_sName            
     */
    function addReturnTo ($p_sAddr, $p_sName = '')
    {
        $this->_aReturnTo[$p_sAddr] = $p_sName;
    }

    /**
     * 设置邮件头信息
     * 
     * @param string $p_sHeader            
     */
    function setHeader ($p_sHeader)
    {
        $this->_sHeader = $p_sHeader;
    }

    /**
     * 设置邮件标题
     * 
     * @param string $p_sSubject            
     */
    function setSubject ($p_sSubject)
    {
        $this->_sSubject = $p_sSubject;
    }

    /**
     * 设置邮件体
     * 
     * @param string $p_sBody            
     * @param boolean $p_bIsHTML            
     */
    function setBody ($p_sBody, $p_bIsHTML = false)
    {
        $this->_bIsHTML = $p_bIsHTML;
        $this->_sBody = $p_sBody;
    }

    /**
     * 添加附件
     * 
     * @param string $p_sName            
     * @param string $p_sPath            
     */
    function addAttachment ($p_sName, $p_sPath)
    {
        $oFInfo = finfo_open();
        $sMimeType = finfo_file($oFInfo, $p_sPath, FILEINFO_MIME_TYPE);
        finfo_close($oFInfo);
        $this->_aAttach[] = array(
            'oContent' => Util_File::tryReadFile($p_sPath),
            'sName' => $p_sName,
            'sMimeType' => $sMimeType
        );
    }

    /**
     * 添加附件 数据流的形式发送
     * 
     * @param
     *            $p_sName
     * @param
     *            $p_oContent
     * @param
     *            $p_sMimeType
     * @author : tanwei
     */
    function addAttachmentStream ($p_sName, $p_oContent, $p_sMimeType)
    {
        $this->_aAttach[] = array(
            'oContent' => $p_oContent,
            'sName' => $p_sName,
            'sMimeType' => $p_sMimeType
        );
    }

    /**
     * 添加邮件中的图片
     * 
     * @param string $p_sName            
     * @param string $p_sPath            
     */
    function addBodyImage ($p_sName, $p_sPath)
    {
        $oFInfo = finfo_open();
        $sMimeType = finfo_file($oFInfo, $p_sPath, FILEINFO_MIME_TYPE);
        finfo_close($oFInfo);
        $this->_aMailImage[] = array(
            'oContent' => Util_File::tryReadFile($p_sPath),
            'sName' => $p_sName,
            'sMimeType' => $sMimeType,
            'sCID' => md5(uniqid(microtime(true)))
        );
    }

    /**
     * 发送邮件
     * 
     * @return boolean
     */
    function sendMail ()
    {
        $sSubject = '=?' . $this->_sCharset . '?B?' . base64_encode($this->_sSubject) . '?=';
        $sHeader = 'MIME-Version: 1.0' . PHP_EOL;
        $sHeader .= 'From: ' . $this->mkSBody($this->_aFrom) . PHP_EOL;
        if (! empty($this->_aReplyTo)) {
            $sHeader .= 'Reply-To: ' . $this->mkSBody($this->_aReplyTo) . PHP_EOL;
        }
        if (! empty($this->_aReturnTo)) {
            $sHeader .= 'Return-Path: ' . $this->mkSBody($this->_aReturnTo) . PHP_EOL;
        }
        if (! empty($this->_aCC)) {
            $sHeader .= 'CC: ' . $this->mkSBody($this->_aCC) . PHP_EOL;
        }
        if (! empty($this->_sBCC)) {
            $sHeader .= 'BCC: ' . $this->mkSBody($this->_sBCC) . PHP_EOL;
        }
        $sBoundary = '=_' . md5(uniqid(microtime(true)));
        $sHeader .= 'Content-Type: multipart/related;charset="' . $this->_sCharset . '"; boundary="' . $sBoundary . '"' . PHP_EOL;
        $sHeader .= $this->_sHeader;
        $sMail = $this->buildMail($sBoundary);
        return mail($this->mkSBody($this->_aTo), $sSubject, $sMail, $sHeader);
    }

    /**
     * 组合邮箱信息
     * 
     * @param array $p_aSBody            
     * @return string
     */
    private function mkSBody ($p_aSBody)
    {
        $aTmp = array();
        foreach ($p_aSBody as $sAddr => $sName) {
            if ('' == $sName) {
                $aTmp[] = $sAddr;
            } else {
                $aTmp[] = '"=?' . $this->_sCharset . '?B?' . base64_encode($sName) . '?=" <' . $sAddr . '>';
            }
        }
        return implode(',', $aTmp);
    }

    /**
     * 生成邮件内容
     * 
     * @param string $p_sBoundary            
     * @return string
     */
    private function buildMail ($p_sBoundary)
    {
        if ($this->_bIsHTML) {
            $sMultipart = $this->_buildHTML($p_sBoundary);
        } else {
            $sMultipart = $this->_buildTxt($p_sBoundary);
            $this->_aAttach[] = array(
                'oContent' => $this->_sBody,
                'sName' => 'body.txt',
                'sMimeType' => 'text/plain'
            );
        }
        foreach ($this->_aAttach as $aAttach) {
            $sMultipart .= '--' . $p_sBoundary . PHP_EOL;
            $sMultipart .= $this->_buildAttach($aAttach);
        }
        $sMultipart .= '--' . $p_sBoundary . '--' . PHP_EOL;
        return $sMultipart;
    }

    /**
     * 生成txt内容
     * 
     * @param string $p_sOrigBoundary            
     * @return string
     */
    private function _buildTxt ($p_sOrigBoundary)
    {
        $sMultipart = '--' . $p_sOrigBoundary . PHP_EOL;
        $sMultipart .= 'Content-Type: text/plain;charset="' . $this->_sCharset . '"' . PHP_EOL;
        $sMultipart .= 'Content-Transfer-Encoding: base64' . PHP_EOL . PHP_EOL;
        $sMultipart .= chunk_split(base64_encode($this->_sBody), 76, PHP_EOL) . PHP_EOL;
        return $sMultipart;
    }

    /**
     * 生成html内容
     * 
     * @param string $p_sOrigBoundary            
     * @return string
     */
    private function _buildHTML ($p_sOrigBoundary)
    {
        if (count($this->_aMailImage) > 0) {
            $aPattern = $aReplace = array();
            foreach ($this->_aMailImage as $aImage) {
                $aPattern[] = '/' . $aImage['sName'] . '/i';
                $aReplace[] = 'cid:' . $aImage['sCID'];
            }
            $this->_sBody = preg_replace($aPattern, $aReplace, $this->_sBody);
            
            $sMultipart = '--' . $p_sOrigBoundary . PHP_EOL;
            $sMultipart .= 'Content-Type: text/html;charset="' . $this->_sCharset . '"' . PHP_EOL;
            $sMultipart .= 'Content-Transfer-Encoding: base64' . PHP_EOL . PHP_EOL;
            $sMultipart .= chunk_split(base64_encode($this->_sBody), 76, PHP_EOL) . PHP_EOL;
            foreach ($this->_aMailImage as $aImage) {
                $sMultipart .= '--' . $p_sOrigBoundary . PHP_EOL;
                $sMultipart .= $this->_buildHTMLImage($aImage);
            }
        } else {
            $sMultipart = '--' . $p_sOrigBoundary . PHP_EOL;
            $sMultipart .= 'Content-Type: text/html;charset="' . $this->_sCharset . '"' . PHP_EOL;
            $sMultipart .= 'Content-Transfer-Encoding: base64' . PHP_EOL . PHP_EOL;
            $sMultipart .= chunk_split(base64_encode($this->_sBody), 76, PHP_EOL) . PHP_EOL;
        }
        return $sMultipart;
    }

    /**
     * 编译HTML中带的图片
     * 
     * @param array $p_aImage            
     * @return string
     */
    private function _buildHTMLImage ($p_aImage)
    {
        $sMultipart = 'Content-Type: ' . $p_aImage['sMimeType'];
        if ($p_aImage['sName'] != '') {
            $sMultipart .= '; name="' . $p_aImage['sName'] . '"' . PHP_EOL;
        } else {
            $sMultipart .= PHP_EOL;
        }
        $sMultipart .= 'Content-ID: <' . $p_aImage['sCID'] . '>' . PHP_EOL;
        $sMultipart .= 'Content-Transfer-Encoding: base64' . PHP_EOL;
        $sMultipart .= 'Content-Disposition: inline; filename="' . $p_aImage['sName'] . '"' . PHP_EOL . PHP_EOL;
        $sMultipart .= chunk_split(base64_encode($p_aImage['oContent']), 76, PHP_EOL) . PHP_EOL;
        return $sMultipart;
    }

    /**
     * 编译附件内容
     * 
     * @param array $p_aAttach            
     * @return string
     */
    private function _buildAttach ($p_aAttach)
    {
        $sMultipart = 'Content-Type: ' . $p_aAttach['sMimeType'] . ';charset="' . $this->_sCharset;
        if ($p_aAttach['sName'] != '') {
            $sMultipart .= '; name="' . $p_aAttach['sName'] . '"' . PHP_EOL;
        } else {
            $sMultipart .= PHP_EOL;
        }
        $sMultipart .= 'Content-Transfer-Encoding: base64' . PHP_EOL;
        $sMultipart .= 'Content-Disposition: attachment; filename="' . $p_aAttach['sName'] . '"' . PHP_EOL . PHP_EOL;
        $sMultipart .= chunk_split(base64_encode($p_aAttach['oContent']), 76, PHP_EOL) . PHP_EOL;
        return $sMultipart;
    }
}
?>