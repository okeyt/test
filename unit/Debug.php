<?php

class Util_Debug
{
    /**
     * debug信息
     * @var unknown
     */
    private $_logs = array();

    /**
     * 以dir方式追求debug信息
     * @param string $msg
     */
    public function dir ($msg)
    {
        $this->add('dir', $msg);
    }

    /**
     * 以warn方式追求debug信息
     * @param string $msg
     */
    public function warn ($msg)
    {
        $this->add('warn', $msg);
    }

    /**
     * 以error方式追求debug信息
     * @param string $msg
     */
    public function error ($msg)
    {
        $this->add('error', $msg);
    }

    /**
     * 以debug方式追求debug信息
     * @param string $msg
     */
    public function debug ($msg)
    {
        $this->add('debug', $msg);
    }

    /**
     * 以log方式追求debug信息
     * @param string $msg
     */
    public function log ($msg)
    {
        $this->add('log', $msg);
    }

    /**
     * 以group方式追求debug信息
     * @param string $msg
     */
    public function group ($msg)
    {
        $this->add('group', $msg);
    }

    /**
     * 以groupCollapsed方式追求debug信息
     * @param string $msg
     */
    public function groupCollapsed ($msg)
    {
        $this->add('groupCollapsed', $msg);
    }

    /**
     * 以groupEnd方式追求debug信息
     * @param string $msg
     */
    public function groupEnd ()
    {
        $this->add('groupEnd', null);
    }

    /**
     * 追加debug信息
     * @param string $type
     * @param mixed $msg
     */
    public function add ($type, $msg)
    {
        if ($type != 'dir' && is_array($msg)) {
            $msg = json_encode($msg);
        }
        if (count($this->_logs) > 1000) {
            $this->clear();
        }
        $this->_logs[] = array($type, $msg);
    }

    /**
     * 取得所有debug信息
     * @return multitype:
     */
    public function getAll ()
    {
        return $this->_logs;
    }

    /**
     * 清空debug信息
     */
    public function clear ()
    {
        $this->_logs = array();
    }
}