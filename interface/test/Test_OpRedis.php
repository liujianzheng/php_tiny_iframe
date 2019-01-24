<?php
require_once dirname(__FILE__) . '/../../common/Common.php';
require_once dirname(__FILE__) . '/../../cache/redis_server.php';

class Test_OpRedis
{
    public function mustInput(&$args)
    {
        $keys = ['name'];
        return getIncludeKeysInfo($args, $keys);
    }

    public function main_handler($request)
    {
        $client_ip = getClientIp();
        $redis = new RedisServer();
        $mark_key = $client_ip . __CLASS__;
        if ($redis->isOutReqRateLimit($mark_key, 10, 3)) {
            return getReturnData(NOTE, '访问频率超限，请10秒后再试');
        }

        $name = $request['name'];

        $redis->hMset('user_info', ['name' => 'xyz', 'head_url' => 'http://www.qq.com', 'nick_name' => 'nickQQ']);
        $info = $redis->hGetAll($name);

        return getReturnData(SUCCESS, 'successful', $info);
    }
}
