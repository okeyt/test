<?php

class Util_Log
{

    /**
     * 所有都打印
     */
    const ALL = 0;

    /**
     * Detailed debug information
     */
    const DEBUG = 1;

    /**
     * Interesting events
     */
    const INFO = 2;
    /**
     * Uncommon event
     */

    /**
     * Exceptional occurrences that are not errors
     */
    const WARN = 3;
    /**
     * Runtime errors
     */
    const ERROR = 4;
    /**
     * fatal conditions
     */
    const FATAL = 5;

    /**
     * Action must be taken immediately
     */
    const OFF = 6;
    /**
     * Urgent alert
     */

    static $aLevelNames = array(
        'ALL'=>0, 'DEBUG'=>1, 'INFO'=>2, 'WARN'=>3, 'ERROR'=>4,
        'FATAL'=>5, 'OFF'=>6,
    );


    private $iLevel = 0;
    private $sLogFile;

    public function __construct ($p_sLogFile)
    {
        $this->sLogFile = $p_sLogFile;
    }

    public function setLogLevel($p_iLevel)
    {
        if ($p_iLevel >= count(self::$aLevelNames) || $p_iLevel < 0)
        {
            throw new Exception('invalid log level:' . $p_iLevel);
        }

        $this->iLevel = $p_iLevel;
    }

    public function debug($p_sMsg)
    {
        $this->_log(self::DEBUG, $p_sMsg);
    }

    public function info($p_sMsg)
    {
        $this->_log(self::INFO, $p_sMsg);
    }

    public function warn($p_sMsg)
    {
        $this->_log(self::WARN, $p_sMsg);
    }

    public function error($p_mMsg)
    {
        $this->_log(self::ERROR, $p_mMsg);
    }

    public function fatal($p_mMsg)
    {
        $this->_log(self::FATAL, $p_mMsg);
    }

    private function _log($p_iLevel, $p_mMsg)
    {
        if ($p_iLevel < $this->iLevel) {
            return;
        }

        $sLogLevelName = array_keys(self::$aLevelNames)[$p_iLevel];
        $sContent = date('Y-m-d H:i:s') . ' [' . $sLogLevelName . '] ' . $this->convertToStr($p_mMsg) . PHP_EOL;
        file_put_contents($this->sLogFile, $sContent, FILE_APPEND);
    }

    protected function convertToStr($data)
    {
        if (null === $data || is_bool($data)) {
            return var_export($data, true);
        }

        if (is_scalar($data)) {
            return (string) $data;
        }

        if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
            return @json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        return str_replace('\\/', '/', @json_encode($data));
    }

    public function __destruct ()
    {}
}