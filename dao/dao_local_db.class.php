<?php

require_once dirname(__FILE__) . '/dao.class.php';

class DaoLocalDb extends Dao
{
    //用户状态定义
    const STATUS_INIT = 0;      //审批中（初始化状态）
    const STATUS_PASS = 1;      //通过

    function __construct()
    {
        $config = getConf('LOCALHOST_DB');
        parent::__construct($config['HOST'], $config['PORT'], $config['USER'], $config['PASSWD'], $config['DBNAME']);
    }

    //获取用户状态描述
    public function getUserStatusDes($status)
    {
        switch ($status) {
            case self::STATUS_INIT:
                return '审批中';
            case self::STATUS_PASS:
                return '通过';
            default:
                return '';
        }
    }

    //获取用户信息详情
    public function getUserInfo($name = '')
    {
        try {
            $sql = "select * from test.user where name='{$name}' and status = " . self::STATUS_PASS;
            $result = $this->ExecuteSelectSql($sql);
            return empty($result) ? array() : $result[0];
        } catch (Exception $e) {
            return array();
        }
    }
}
