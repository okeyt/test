<?php

class Util_File
{

    /**
     * Mime定义
     * 
     * @var array
     */
    private static $_aMimeType = array(
        'application/msword' => 'doc',
        'application/octet-stream' => '',
        'application/pdf' => 'pdf',
        'application/vnd.ms-excel' => 'xls',
        'application/vnd.ms-publisher' => 'pub',
        'application/vnd.ms-powerpoint' => 'pptx',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'       =>  'xlsx',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',

        'audio/midi'            =>  'mid',
        'audio/mpeg'            =>  'mp3',
         'audio/ogg'            =>  'ogg',
        'audio/x-m4a'           =>  'm4a',
        'audio/x-realaudio'     =>  'ra',
        'video/3gpp'            =>  '3gp',
        'video/mp2t'            =>  'ts',
        'video/mp4'             =>  'mp4',
        'video/mpeg'            =>  'mpeg',
        'video/quicktime'       =>  'mov',
        'video/webm'            =>  'webm',
        'video/x-flv'           =>  'flv',
        'video/x-m4v'           =>  'm4v',
        'video/x-mng'           =>  'mng',
        'video/x-ms-asf'        =>  'asf',
        'video/x-ms-wmv'        =>  'wmv',
        'video/x-msvideo'       =>  'avi',

        'application/vnd.rn-realmedia' => 'rmvb',
        'application/x-msdownload' => 'exe',
        'application/zip' => 'zip',
        'application/x-gzip' => 'gzip',
        'application/x-shockwave-flash' => 'swf',
        'image/bmp' => 'bmp',
        'image/x-ms-bmp' => 'bmp',
        'image/gif' => 'gif',
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'text/plain' => 'txt',
        'text/xml' => 'xml'
    );

    /**
     * 根据mimetype返回后缀名
     * 
     * @param string $p_sMimeType            
     * @return string
     */
    static function getExtension ($p_sMimeType)
    {
        if ('' == $p_sMimeType) {
            return 'dat';
        } else {
            if (isset(self::$_aMimeType[$p_sMimeType])) {
                return self::$_aMimeType[$p_sMimeType];
            } else {
                return 'dat';
            }
        }
    }

    /**
     * 根据后缀名返回mimetype
     * 
     * @param string $p_sExtension            
     * @return string
     */
    static function getMimeType ($p_sExtension)
    {
        if ('' == $p_sExtension) {} else {
            foreach (self::$_aMimeType as $sMime => $sExtension) {
                if ($p_sExtension == $sExtension) {
                    return $sMime;
                }
            }
        }
    }

    /**
     * 尝试读取文件内容
     * 
     * @param string $p_sFilePath            
     * @param int $p_iTryTime            
     * @return string/false
     */
    static function tryReadFile ($p_sFilePath, $p_iTryTime = 5)
    {
        $sContent = '';
        for ($i = 0; $i < $p_iTryTime; ++ $i) {
            $sContent = @file_get_contents($p_sFilePath);
            if (false !== $sContent) {
                return $sContent;
            }
        }
        return false;
    }

    /**
     * 尝试写文件内容
     * 
     * @param string $p_sFilePath            
     * @param string $p_sContent            
     * @param int $p_iTryTime            
     * @return true/false
     */
    static function tryWriteFile ($p_sFilePath, $p_sContent, $p_iFlag = FILE_APPEND, $p_iTryTime = 5)
    {
        for ($i = 0; $i < $p_iTryTime; ++ $i) {
            $bResult = @file_put_contents($p_sFilePath, $p_sContent, $p_iFlag);
            if (false !== $bResult) {
                return true;
            }
        }
        return false;
    }

    /**
     * 尝试删除文件
     * 
     * @param string $p_sFilePath            
     * @param int $p_iTryTime            
     * @return true/false
     */
    static function tryDeleteFile ($p_sFilePath, $p_iTryTime = 5)
    {
        for ($i = 0; $i < $p_iTryTime; ++ $i) {
            $bResult = @unlink($p_sFilePath);
            if (false !== $bResult) {
                return true;
            }
        }
        return false;
    }

    /**
     * 尝试删除整个目录
     * 
     * @param string $p_sDir            
     * @param boolean $p_bRecursive            
     * @param int $p_iTryTime            
     */
    static function tryDeleteDir ($p_sDir, $p_bRecursive = true, $p_iTryTime = 5)
    {
        if (is_dir($p_sDir)) {
            $aTmp = scandir($p_sDir);
            foreach ($aTmp as $sPath) {
                if ('.' == $sPath or '..' == $sPath) {
                    continue;
                }
                $sFullPath = $p_sDir . DIRECTORY_SEPARATOR . $sPath;
                if (is_dir($sFullPath)) {
                    if ($p_bRecursive) {
                        if (! self::tryDeleteDir($sFullPath, $p_bRecursive, $p_iTryTime)) {
                            return false;
                        }
                    } else {
                        return false;
                    }
                } else {
                    return self::tryDeleteFile($sFullPath, $p_iTryTime);
                }
            }
        } else {
            return false;
        }
    }

    /**
     * 尝试创建目录
     * 
     * @param string $p_sDir            
     * @param int $p_iMode            
     * @param boolean $p_bRecursive            
     * @param int $p_iTryTime            
     * @return true/false
     */
    static function tryMakeDir ($p_sDir, $p_iMode = null, $p_bRecursive = null, $p_iTryTime = 5)
    {
        for ($i = 0; $i < $p_iTryTime; ++ $i) {
            umask(0000);
            $bResult = @mkdir($p_sDir, $p_iMode, $p_bRecursive);
            if (false !== $bResult) {
                return true;
            }
        }
        return false;
    }

    /**
     * 尝试复制文件
     * 
     * @param string $p_sSourceFilePath            
     * @param string $p_sDestSourceFilePath            
     * @param boolean $p_bOverWritten            
     * @param int $p_iTryTime            
     * @return true/false
     */
    static function tryCopyFile ($p_sSourceFilePath, $p_sDestSourceFilePath, $p_bOverWritten = false, $p_iTryTime = 5)
    {
        if (! $p_bOverWritten) {
            if (file_exists($p_sDestSourceFilePath)) {
                return false;
            }
        }
        for ($i = 0; $i < $p_iTryTime; ++ $i) {
            $bResult = @copy($p_sSourceFilePath, $p_sDestSourceFilePath);
            if (false !== $bResult) {
                return true;
            }
        }
        return false;
    }

    /**
     * 尝试移动文件
     * 
     * @param string $p_sSourceFilePath            
     * @param string $p_sDestSourceFilePath            
     * @param boolean $p_bOverWritten            
     * @param int $p_iTryTime            
     * @return true/false
     */
    static function tryMoveFile ($p_sSourceFilePath, $p_sDestSourceFilePath, $p_bOverWritten = false, $p_iTryTime = 5)
    {
        if (! $p_bOverWritten) {
            if (file_exists($p_sDestSourceFilePath)) {
                return false;
            }
        }
        for ($i = 0; $i < $p_iTryTime; ++ $i) {
            $bResult = @rename($p_sSourceFilePath, $p_sDestSourceFilePath);
            if (false !== $bResult) {
                return true;
            }
        }
        return false;
    }

    /**
     * 尝试读取目录
     * 
     * @param string $p_sDir            
     * @param boolean $p_bRecursive            
     * @return array
     */
    static function tryReadDir ($p_sDir, $p_bRecursive = false)
    {
        if (file_exists($p_sDir)) {
            $aTmp = scandir($p_sDir);
        } else {
            $aTmp = array();
        }
        $aResult = array();
        foreach ($aTmp as $sPath) {
            if ('.' == $sPath or '..' == $sPath) {
                continue;
            }
            $sFullPath = $p_sDir . DIRECTORY_SEPARATOR . $sPath;
            if (is_dir($sFullPath)) {
                if ($p_bRecursive) {
                    $aSubResult = self::tryReadDir($sFullPath, $p_bRecursive);
                    $aResult = array_merge($aResult, $aSubResult);
                } else {
                    $aResult[] = array(
                        'sPath' => $sFullPath,
                        'sType' => 'Directory'
                    );
                }
            } elseif (is_file($sFullPath)) {
                $aResult[] = array(
                    'sPath' => $sFullPath,
                    'sType' => 'File'
                );
            } elseif (is_link($sFullPath)) {
                $aResult[] = array(
                    'sPath' => $sFullPath,
                    'sType' => 'Link'
                );
            } else {
                $aResult[] = array(
                    'sPath' => $sFullPath,
                    'sType' => 'Unknown'
                );
            }
        }
        return $aResult;
    }

    /**
     * 格式化输出文件大小
     * 
     * @param int $p_iBytes            
     * @param string $p_sUnit            
     * @param string $p_sType            
     * @return int/string/array
     */
    static function formatFileSize ($p_iBytes, $p_sUnit = 'auto', $p_sType = 'string')
    {
        switch (strtolower($p_sUnit)) {
            case 'kb':
                return round($p_iBytes / 1024, 2);
                break;
            case 'mb':
                return round($p_iBytes / 1048576, 2);
                break;
            case 'gb':
                return round($p_iBytes / 1073741824, 2);
                break;
            case 'auto':
            case 'auto-sub-abs':
            case 'auto-sub-dec':
                $aTmp = array();
                $aTmp[] = floor($p_iBytes / 1073741824);
                $aTmp[] = floor(($p_iBytes % 1073741824) / 1048576);
                $aTmp[] = floor(($p_iBytes % 1048576) / 1024);
                $aTmp[] = floor($p_iBytes % 1024);
                $aUnit = array(
                    'GB',
                    'MB',
                    'KB',
                    'B'
                );
                if ('string' == $p_sType) {
                    $iFlag = 0;
                    foreach ($aTmp as $iIndex => $iValue) {
                        if ($iValue > 0) {
                            $iFlag = $iIndex;
                            break;
                        }
                    }
                    if ('auto-sub-abs' == $p_sUnit or 'auto-sub-dec' == $p_sUnit) {
                        $iSubFlag = $iFlag + 1;
                        if (isset($aTmp[$iSubFlag])) {
                            if ('auto-sub-abs' == $p_sUnit) {
                                return $aTmp[$iFlag] . $aUnit[$iFlag] . $aTmp[$iSubFlag] . $aUnit[$iSubFlag];
                            } else {
                                return ($aTmp[$iFlag] + round($aTmp[$iSubFlag] / 1024, 2)) . $aUnit[$iFlag];
                            }
                        }
                    }
                    return $aTmp[$iFlag] . $aUnit[$iFlag];
                } else {
                    return $aTmp;
                }
                break;
        }
        return $p_iBytes;
    }
}