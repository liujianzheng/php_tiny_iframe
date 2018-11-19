<?php

//系统日志，记录在主目录的log/下
class SysLog
{
    private static $_instance;
    private $_path;
    private $_pid;

    function __construct($path)
    {
        $this->_path = $path;
        $this->_pid = getmypid();
    }

    public static function instance($path = '/tmp/')
    {
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self($path);
        }
        return self::$_instance;
    }

    public function log($fileName, $msg)
    {
        date_default_timezone_set('PRC');
        $now = time();
        $handle = fopen($this->_path . '/' . $fileName . date('Ymd', $now) . ".log", 'a');
        $data_time = date('Y-m-d H:i:s', $now);
        fwrite($handle, "[$data_time][$this->_pid]$msg\n");
        fclose($handle);
        return true;
    }
}

//系统日志基础函数
function _log($fileName, $msg = '')
{
    $log_path = dirname(__FILE__) . '/../log/';

    $bt = debug_backtrace();
    $function = '';
    $file = '';
    $line = '';
    foreach ($bt as $item) {
        if ($file) {
            $function = $item['function'];
            break;
        }
        if (substr($item['function'], -4) == '_log' && strlen($item['function']) > 4) {
            $file = $item['file'];
            $line = $item['line'];
        }
    }
    //file只取最后一截文件名
    $file = explode("/", rtrim($file, '/'));
    $file = $file[count($file) - 1];

    $client_ip = getClientIp();
    $local_ip = getLocalIp();
    $prefix = "[$client_ip][$local_ip][$file][$function][$line] ";
    SysLog::instance($log_path)->log($fileName, $prefix . $msg);
}

//通用日志记录
function sys_log($msg)
{
    _log('sys_info', $msg);
}

//数据库语句sql记录
function mysql_log($msg)
{
    _log('mysql_info', $msg);
}

?>