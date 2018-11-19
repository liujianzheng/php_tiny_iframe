<?php

//系统配置类
class ConfFactory
{
    public static $conf = array();

    public static function getInstance()
    {
        if (!empty(ConfFactory::$conf)) {
            return ConfFactory::$conf;
        }
        $path = dirname(__FILE__) . '/../conf/';
        $inc = parse_ini_file($path . 'common.inc.ini', true);
        $ini_file = strtolower($inc['COMMON']['INC']) . '.route.ini';
        ConfFactory::$conf = parse_ini_file($path . $ini_file, true);
        return ConfFactory::$conf;
    }
}

function getConf($key)
{
    $conf = ConfFactory::getInstance();
    return $conf[$key];
}

?>