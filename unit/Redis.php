<?php

class Util_Redis
{

    private $_sHost;

    private $_iPort;

    private $_iSelectDb;

    private $_oRedis;

    private $_PingTime = 0;

    private $_oDebug = null;

    private static $_iUseTime = 0;

    private static $_iOptCnt = 0;

    /**
     * 同步还是异同
     *
     * @var unknown
     */
    private $_bSync = true;

    private $_aCache = array();

    private $_aOpt = array();

    private $_aDelKey = array();

    private $_aUseFun = array(
        'llen',
        'lpop',
        'lpush',
        'rpush',
        'rpop',
        'lset',
        'lrem',
        'lrange', // 'lindex','linsert'
        'sadd',
        'scard',
        'sismember',
        'smembers',
        'srem',
        'srandmember',
        'zadd',
        'zcard',
        'zcount',
        'zincrby',
        'zrange',
        'zrem',
        'zremrangebyscore',
        'zrangebyscore',
        'zrank',
        'zrevrange',
        'zrevrangebyscore',
        'zrevrank',
        'zscore',
        'select',
        'bgsave',
        'flushdb',
        'flushall',
        'sort',
        'keys',
        'incr',
        'hincrby',
        'setoption'
    );

    public function __construct ($host = '127.0.0.1', $port = 6379, $db = 0)
    {
        $this->_sHost = $host;
        $this->_iPort = $port;
        $this->_iSelectDb = $db;
        $this->_oDebug = Util_Common::getDebug();
        $this->connect();
        $this->_aUseFun = array_flip($this->_aUseFun);
    }

    public function connect ()
    {
        $this->_oRedis = new Redis();
        $ret = $this->_oRedis->connect($this->_sHost, $this->_iPort);
        if ($ret == false) {
            throw new Exception('Cannot connect redis host=' . $this->_sHost . ', port=' . $this->_iPort);
        }
        $this->_oRedis->setOption(1, 1);
        $this->_oRedis->select($this->_iSelectDb);
        $this->_iPingTime = time();
    }

    public function clear ($key)
    {
        $keys = $this->_oRedis->keys($key);
        if (! empty($keys)) {
            foreach ($keys as $key) {
                $this->_oRedis->del($key);
            }
        }
    }

    public function get ($key)
    {
        if (isset($this->_aCache[$key])) {
            return $this->_aCache[$key];
        }
        if (isset($this->_aDelKey[$key])) {
            return null;
        }
        
        $this->_aCache[$key] = $this->call('get', array(
            $key
        ));
        
        return $this->_aCache[$key];
    }

    public function mGet ($keys)
    {
        if (empty($keys)) {
            return array();
        }
        
        $ret = $this->call('mget', array(
            $keys
        ));
        foreach ($keys as $key) {
            if (isset($this->_aCache[$key])) {
                $ret[$key] = $this->_aCache[$key];
            } elseif (isset($this->_aDelKey[$key])) {
                unset($ret[$key]);
            }
        }
        
        return $ret;
    }

    public function del ($key)
    {
        if (isset($this->_aCache[$key])) {
            unset($this->_aCache[$key]);
        }
        
        $this->_aDelKey[$key] = 1;
        $this->addOpt('del', array(
            $key
        ));
    }

    public function expire ($key, $expire)
    {
        if ($expire < 99999999) {
            $this->addOpt('expire', array(
                $key,
                $expire
            ));
        } else {
            $this->addOpt('expireat', array(
                $key,
                $expire
            ));
        }
    }

    public function set ($key, $val, $ttl = 0)
    {
        $this->_aCache[$key] = $val;
        
        if (isset($this->_aDelKey[$key])) {
            unset($this->_aDelKey[$key]);
        }
        
        if ($ttl > 0) {
            $this->addOpt('setex', array(
                $key,
                $ttl,
                $val
            ));
        } else {
            $this->addOpt('set', array(
                $key,
                $val
            ));
        }
    }

    public function hSet ($key, $field, $val)
    {
        $this->_aCache[$key][$field] = $val;
        
        if (isset($this->_aDelKey[$key . '::' . $field])) {
            unset($this->_aDelKey[$key . '::' . $field]);
        }
        $this->addOpt('hset', array(
            $key,
            $field,
            $val
        ));
    }

    public function hDel ($key, $field)
    {
        if (isset($this->_aCache[$key][$field])) {
            unset($this->_aCache[$key][$field]);
        }
        
        $this->_aDelKey[$key . '::' . $field] = 1;
        $this->addOpt('hdel', array(
            $key,
            $field
        ));
    }

    public function hGet ($key, $field)
    {
        if (isset($this->_aCache[$key][$field])) {
            return $this->_aCache[$key][$field];
        }
        if (isset($this->_aDelKey[$key . '::' . $field])) {
            return null;
        }
        
        $this->_aCache[$key][$field] = $this->call('hget', array(
            $key,
            $field
        ));
        
        return $this->_aCache[$key][$field];
    }

    public function hMGet ($key, $fields)
    {
        if (empty($fields)) {
            return array();
        }
        
        $ret = $this->call('hmget', array(
            $key,
            $fields
        ));
        foreach ($fields as $field) {
            if (isset($this->_aCache[$key][$field])) {
                $ret[$field] = $this->_aCache[$key][$field];
            } elseif (isset($this->_aDelKey[$key . '::' . $field])) {
                unset($ret[$field]);
            } else {
                $this->_aCache[$key][$field] = $ret[$field];
            }
        }
        
        return $ret;
    }

    public function hVals ($key)
    {
        $ret = $this->hGetAll($key);
        return array_values($ret);
    }

    public function hKeys ($key)
    {
        $ret = $this->call('hkeys', array(
            $key
        ));
        if (isset($this->_aCache[$key])) {
            foreach ($this->_aCache[$key] as $field => $val) {
                if (false === in_array($field, $ret)) {
                    $ret[] = $field;
                }
            }
        }
        
        // 删除已经删除的key
        foreach ($ret as $k => $field) {
            if (isset($this->_aDelKey[$key . '::' . $field])) {
                unset($ret[$k]);
            }
        }
        
        return $ret;
    }

    public function hGetAll ($key)
    {
        $ret = $this->call('hgetall', array(
            $key
        ));
        
        if (isset($this->_aCache[$key])) {
            foreach ($this->_aCache[$key] as $field => $val) {
                $ret[$field] = $val;
            }
        }
        
        // 删除已经删除的key
        foreach ($ret as $field => $val) {
            if (isset($this->_aDelKey[$key . '::' . $field])) {
                unset($ret[$field]);
            }
        }
        
        return $ret;
    }

    public function hLen ($key)
    {
        return count($this->hKeys($key));
    }

    public function sync ($is_error)
    {
        if (! $is_error) {
            foreach ($this->_aOpt as $opt) {
                $this->call($opt[0], $opt[1]);
            }
        }
        
        $this->_aOpt = array();
        $this->_aCache = array();
    }

    public function __call ($method, $params)
    {
        $method = strtolower($method);
        if (! isset($this->_aUseFun[$method])) {
            throw new Exception('Redis不支持【' . $method . '】函数:' . json_encode($params));
        }
        
        return $this->call($method, $params);
    }

    private function addOpt ($method, $params)
    {
        $this->_aOpt[] = array(
            $method,
            $params
        );
        if ($this->_bSync) {
            $this->sync(false);
        }
    }

    private function call ($method, $params)
    {
        // 超过240秒，则重新建立连接
        $now_time = time();
        if ($now_time - $this->_iPingTime > 240) {
            @$this->_oRedis->close();
            $this->_oRedis = null;
            $this->connect();
        }
        
        if (! empty($params)) {
            // 将字符串型的数值格式化成数值型
            foreach ($params as $k => $v) {
                if (is_numeric($v)) {
                    $params[$k] = $v + 0;
                }
            }
        }
        if ($this->_oDebug) {
            $time1 = microtime(true);
            $ret = call_user_func_array(array(
                $this->_oRedis,
                $method
            ), $params);
            $time2 = microtime(true);
            $use_time = round(($time2 - $time1) * 1000, 2);
            self::$_iUseTime += $use_time;
            self::$_iOptCnt ++;
            
            $aArg = array();
            $aVal = null;
            foreach ($params as $v) {
                if (is_array($v)) {
                    $aVal = $v;
                } else {
                    $aArg[] = $v;
                }
            }
            if ($method == 'hmget' || $method == 'mget') {
                $aArg[] = '[' . join(',', $aVal) . ']';
                $aVal = null;
            }
            $this->_oDebug->groupCollapsed('Redis run ' . $use_time . '毫秒  ' . $method . ' ：' . join(',', $aArg));
            if ($aVal) {
                $this->_oDebug->debug($aVal);
            }
            if (! is_bool($ret)) {
                $this->_oDebug->debug($ret);
            }
            $this->_oDebug->groupEnd();
        } else {
            $ret = call_user_func_array(array(
                $this->_oRedis,
                $method
            ), $params);
        }
        return $ret;
    }

    public static function getDebugStat ()
    {
        return '[Redis]->Query: ' . self::$_iOptCnt . ', Use Time:' . self::$_iUseTime;
    }

    public function __destruct ()
    {
        if (is_resource($this->_oRedis)) {
            $this->_oRedis->close();
        }
    }
}