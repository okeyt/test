<?php

class Util_Memcache
{

    /**
     * Cache对象
     *
     * @var object
     */
    private $oCache = null;

    /**
     * 操作总共开销时间
     *
     * @var int
     */
    private static $iTotalTime = 0;

    /**
     * 操作次数
     *
     * @var int
     */
    private static $iOptCnt = 0;

    /**
     * 配制
     *
     * @var string
     */
    private $aConf = '';

    /**
     * 开启debug时
     *
     * @var object
     */
    private $oDebug = null;

    /**
     * 构造函数
     *
     * @param unknown $sKey            
     */
    public function __construct ($aConf)
    {
        $this->aConf = $aConf;
        $this->oDebug = Util_Common::getDebug();
        $this->connect();
    }

    /**
     * debug的汇总时间
     * @return string
     */
    public static function getDebugStat ()
    {
        return '[Memcache]->Query: ' . self::$iOptCnt . ', Use Time:' . round(self::$iTotalTime * 1000, 2);
    }
    
    /**
     * 连接服务器
     *
     * @return boolean
     */
    public function connect ()
    {
        $this->oCache = new Memcache();
        foreach ($this->aConf as $host) {
            $this->oCache->addServer($host['host'], $host['port']);
        }
        $this->oCache->setCompressThreshold(10000, 0.2);
        return true;
    }

    /**
     * 加载缓存
     *
     * @param $sKey string/array            
     * @return boolean
     */
    public function get ($sKey)
    {
        if ($this->oDebug) {
            $iStartTime = microtime(true);
            self::$iOptCnt ++;
            $ret = $this->oCache->get($sKey);
            $iUseTime = (microtime(true) - $iStartTime);
            self::$iTotalTime += $iUseTime;
            if (is_array($sKey)) {
                $sKeyDesc = implode(',', $sKey);
            } else {
                $sKeyDesc = $sKey;
            }
            $this->oDebug->groupCollapsed('Memcache get ' . $sKeyDesc . ' ：' . round($iUseTime * 1000, 2) . '毫秒， 命中：' . ($ret === false ? 'false' : 'true'));
            $this->oDebug->debug($ret);
            $this->oDebug->groupEnd();
        } else {
            $ret = $this->oCache->get($sKey);
        }
        return $ret;
    }

    /**
     * 加载多个缓存
     *
     * @param $prex string
     *            拼接前缀
     * @param $sKey array
     *            拼接数组
     * @return array
     */
    public function getMulti ($sPrex, $aKey)
    {
        $newKey = array();
        foreach ($aKey as $k => $v) {
            $newKey[] = $sPrex . $v;
        }
        return $this->get($newKey);
    }

    /**
     * 存储缓存
     *
     * @param $sKey string            
     * @param $mValue mixed            
     * @param $iTll int            
     * @return boolean
     */
    public function set ($sKey, $mValue, $iTll = 0)
    {
        if ($this->oDebug) {
            $iStartTime = microtime(true);
            self::$iOptCnt ++;
            $ret = $this->oCache->set($sKey, $mValue, MEMCACHE_COMPRESSED, $iTll);
            $iUseTime = (microtime(true) - $iStartTime);
            self::$iTotalTime += $iUseTime;
            $this->oDebug->groupCollapsed('Memcache set ' . $sKey . ' ：' . round($iUseTime * 1000, 2) . '毫秒');
            $this->oDebug->debug($mValue);
            $this->oDebug->groupEnd();
        } else {
            $ret = $this->oCache->set($sKey, $mValue, MEMCACHE_COMPRESSED, $iTll);
        }
        
        return $ret;
    }

    /**
     * 删除某个缓存
     *
     * @param $sKey string            
     * @param $time int            
     * @return boolean
     */
    public function delete ($sKey, $time = 0)
    {
        if ($this->oDebug) {
            $iStartTime = microtime(true);
            self::$iOptCnt ++;
            $ret = $this->oCache->delete($sKey, $time);
            $iUseTime = (microtime(true) - $iStartTime);
            self::$iTotalTime += $iUseTime;
            $this->oDebug->debug('Memcache remove ' . $sKey . ' ：' . round($iUseTime * 1000, 2) . '毫秒');
        } else {
            $ret = $this->oCache->delete($sKey, $time);
        }
        return $ret;
    }

    /**
     * 增加一个cache项
     *
     * @param string $sKey            
     * @param mixed $mValue            
     * @param int $iTll            
     */
    public function add ($sKey, $mValue, $iTll)
    {
        if ($this->oDebug) {
            $iStartTime = microtime(true);
            self::$iOptCnt ++;
            $ret = $this->oCache->add($sKey, $mValue, MEMCACHE_COMPRESSED, $iTll);
            $iUseTime = (microtime(true) - $iStartTime);
            self::$iTotalTime += $iUseTime;
            $this->oDebug->groupCollapsed('Memcache add ' . $sKey . ' ：' . round($iUseTime * 1000, 2) . '毫秒');
            $this->oDebug->debug($mValue);
            $this->oDebug->groupEnd();
        } else {
            $ret = $this->oCache->add($sKey, $mValue, MEMCACHE_COMPRESSED, $iTll);
        }
        return $ret;
    }

    /**
     * 取得缓存状态
     */
    public function status ()
    {
        return $this->oCache->getExtendedStats();
    }

    /**
     * 清空缓存
     *
     * @param $mode string            
     * @param $errorLevel int            
     * @return boolean
     */
    public function flush ()
    {
        return $this->oCache->flush();
    }

    /**
     * 关闭连接
     *
     * @return void
     */
    public function close ()
    {
        if (null != $this->oCache) {
            return $this->oCache->close();
        }
    }

    public function __destruct ()
    {
        $this->close();
    }
}