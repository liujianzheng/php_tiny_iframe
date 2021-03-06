<?php
require_once dirname(__FILE__) . '/../../common/Common.php';
require_once dirname(__FILE__) . '/../../dao/dao_local_db.class.php';

class Test_FirstInterface
{
    public function mustInput(&$args)
    {
        $keys = ['name'];
        return getIncludeKeysInfo($args, $keys);
    }

    public function main_handler($request)
    {
        $name = $request['name'];
        return getReturnData(SUCCESS, 'successful', "hello {$name}");
    }
}
