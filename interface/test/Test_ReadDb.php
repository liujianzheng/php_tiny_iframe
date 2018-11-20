<?php
require_once dirname(__FILE__) . '/../../common/Common.php';
require_once dirname(__FILE__) . '/../../dao/dao_local_db.class.php';

class Test_ReadDb
{
    public function mustInput(&$args)
    {
        $keys = ['id', 'name'];
        return getIncludeKeysInfo($args, $keys);
    }

    public function main_handler($request)
    {
        $id = (int)$request['id'];
        $name = $request['name'];

        $dao = new DaoLocalDb();
        $result = $dao->getUserInfo($name);
        return getReturnData(SUCCESS, 'successful', $result);
    }
}
