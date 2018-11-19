<?php
require_once dirname(__FILE__) . '/../../common/Common.php';
require_once dirname(__FILE__) . '/../../dao/dao.class.php';

class Account_Login
{
    public function mustInput(&$args)
    {
        $keys = [];
        return getIncludeKeysInfo($args, $keys);
    }

    public function main_handler($request)
    {
        $config = getConf('ROUTE.LOCALHOST_DB');
        $dao = new Dao($config['HOST'], $config['USER'], $config['PASSWD'], $config['DBNAME'], $config['PORT']);
        $result = $dao->ExecuteSelectSql('select * from test limit 10');
        return getReturnData(SUCCESS, '登录成功', $result);
    }
}
