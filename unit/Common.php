<?php

class Util_Common
{

    protected static $_aInstance;

    public static function getMongoDb ($sType = 'bll')
    {
        if (! isset(self::$_aInstance['mongodb'][$sType])) {
            $aConf = self::getConf($sType, 'mongodb');
            if (empty($aConf)) {
                throw new Exception('MongoDb配置【' . $sType . '】未找到!');
            }
            self::$_aInstance['mongodb'][$sType] = new MongoQB_Builder($aConf);
        }

        return self::$_aInstance['mongodb'][$sType];
    }

    public static function getSolr ($sType = 'bll', $aParam = array(), $limit = 10)
    {
        if (! isset(self::$_aInstance['solr'][$sType])) {
            $sHost = self::getConf($sType, 'solr');
            if (empty($sHost)) {
                throw new Exception('Solr配置【' . $sType . '】未找到!');
            }
            self::$_aInstance['solr'][$sType] = new Util_Solr($sHost, $aParam, $limit);
        }

        return self::$_aInstance['solr'][$sType];
    }

    /**
     * @param string $sType
     * @return mixed
     */
    public static function getES($sType = 'bll')
    {
        //http://www.elasticsearch.com/guide/en/elasticsearch/client/php-api/current/index.html
        //https://github.com/elasticsearch/elasticsearch-php
        if (! isset(self::$_aInstance['es'][$sType])) {
            $aConf = self::getConf($sType, 'es');
            if (empty($aConf)) {
                throw new Exception('elasticsearch配置【' . $sType . '】未找到!');
            }
            self::$_aInstance['es'][$sType] = new Elasticsearch\Client($aConf);
        }

        return self::$_aInstance['es'][$sType];

    }

    public static function getRedis ($sType = 'bll')
    {
        if (! isset(self::$_aInstance['redis'][$sType])) {
            $aConf = self::getConf($sType, 'redis');
            if (empty($aConf)) {
                throw new Exception('Redis配置【' . $sType . '】未找到!');
            }
            self::$_aInstance['redis'][$sType] = new Util_Redis($aConf['host'], $aConf['port'], $aConf['db']);
        }

        return self::$_aInstance['redis'][$sType];
    }

    public static function getCache ($sType = 'bll')
    {
        if (! isset(self::$_aInstance['cache'][$sType])) {
            $aConf = self::getConf($sType, 'cache');
            if (empty($aConf)) {
                throw new Exception('Cache配置【' . $sType . '】未找到!');
            }
            self::$_aInstance['cache'][$sType] = new Util_Memcache($aConf);
        }

        return self::$_aInstance['cache'][$sType];
    }

    public static function getOrm ($sDbName, $sTblName, $bWhereCache)
    {
        if (! isset(self::$_aInstance['orm'][$sDbName][$sTblName])) {
            self::$_aInstance['orm'][$sDbName][$sTblName] = new Db_Orm($sDbName, $sTblName);
            self::$_aInstance['orm'][$sDbName][$sTblName]->setWhereCache($bWhereCache);
        }

        return self::$_aInstance['orm'][$sDbName][$sTblName];
    }

    public static function getMsSQLDB ($sDbName, $sType = 'master')
    {
    	if (! isset(self::$_aInstance['mssqldb'][$sDbName])) {
    		$aConf = self::getConf($sDbName, 'mssql');
    		if (empty($aConf)) {
    			throw new Exception('MSSQL DB配置【' . $sDbName . '】未找到!');
    		}
    		self::$_aInstance['mssqldb'][$sDbName] = new Db_MsSQL($aConf);
    	}
    	return self::$_aInstance['mssqldb'][$sDbName];
    }

    public static function getDb ($sDbName, $sType = 'master')
    {
        if (! isset(self::$_aInstance['database'][$sDbName])) {
            $aConf = self::getConf($sDbName, 'database');
            if (empty($aConf)) {
                throw new Exception('DB配置【' . $sDbName . '】未找到!');
            }
            $aConf = isset($aConf[$sType]) ? $aConf[$sType] : $aConf['master'];
            if (isset($aConf['option'])) {
                $aOption = $aConf['option'];
            } else {
                $aOption = array(
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,
                    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true
                );
            }
            self::$_aInstance['database'][$sDbName] = new Db_PDO($aConf['dsn'], $aConf['user'], $aConf['pass'], $aOption);
            if (isset($aConf['init'])) {
                foreach ($aConf['init'] as $sql) {
                    self::$_aInstance['database'][$sDbName]->exec($sql);
                }
            }
        }

        return self::$_aInstance['database'][$sDbName];
    }

    public static function getLogger ($sType = 'bll')
    {
        if (! isset(self::$_aInstance['logger'][$sType])) {
            $aConf = self::getConf($sType, 'logger');
            if (empty($aConf)) {
                throw new Exception('Logger配置【' . $sType . '】未找到!');
            }
            self::$_aInstance['logger'][$sType] = new Util_Log($aConf);
        }

        return self::$_aInstance['logger'][$sType];
    }

    public static function getDebug ()
    {
        if (self::isDebug()) {
            if (! isset(self::$_aInstance['debug'])) {
                self::$_aInstance['debug'] = new Util_Debug();
            }
            return self::$_aInstance['debug'];
        }
        return null;
    }

    public static function getDebugData ()
    {
        if (self::isDebug()) {
            $aDebug[] = '共次总耗时：' . Yaf_G::getRunTime() . '毫秒';
            if (isset(self::$_aInstance['orm'])) {
                $aDebug[] = Db_Orm::getDebugStat();
            }
            if (isset(self::$_aInstance['cache'])) {
                $aDebug[] = Util_Memcache::getDebugStat();
            }
            if (isset(self::$_aInstance['redis'])) {
                $aDebug[] = Util_Redis::getDebugStat();
            }
            if (class_exists('Sdk_Base')) {
                $sInfo = Sdk_Base::getDebugStat();
                if (! empty($sInfo)) {
                    $aDebug[] = $sInfo;
                }
            }

            $sDebug = join(' ', $aDebug);
            $aDebug = self::getDebug()->getAll();
            array_unshift($aDebug, $sDebug);
            return $aDebug;
        }
        return null;
    }

    public static function isDebug ()
    {
        return Yaf_G::isDebug();
    }

    public static function getConf ($sKey, $sType = null, $sFile = null)
    {
        return Yaf_G::getConf($sKey, $sType, $sFile);
    }

    public static function getUrl($sAction = null, $aParam = null, $bFullPath = false, $sDomain = null, $sPostfix = null)
    {
        return Yaf_G::getUrl($sAction, $aParam, $bFullPath, $sDomain, $sPostfix);
    }
}