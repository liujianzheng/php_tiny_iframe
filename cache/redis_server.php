<?php

class RedisServer
{
    function __construct()
    {
        $config = getConf('LOCALHOST_REDIS');
        $this->_redis = new Redis();
        $ret = $this->_redis->connect($config['HOST'], $config['PORT'], 3);
        if (!$ret) {
            sys_log('cache server redis connect failed[please check host and port]');
            throw new Exception('cache server connect failed');
        }
        if (!empty($config['PASSWD'])) {
            $ret = $this->_redis->auth($config['PASSWD']);
            if (!$ret) {
                sys_log('cache server redis auth failed [password error]');
                throw new Exception('cache server auth failed');
            }
        }
        if (!empty($config['DB'])) {
            $this->_redis->select($config['DB']);
        }
    }

    //$ttl表示几秒后过期，0表示永久生效
    public function set($key, $value, $ttl = 0)
    {
        return $this->_redis->set($key, $value, $ttl);
    }

    public function get($key)
    {
        return $this->_redis->get($key);
    }

    public function hSet($key, $field, $value)
    {
        return $this->_redis->hSet($key, $field, $value);
    }

    //批量设置field=>$value，如：$fv=['a'=>'xx', 'b'=>'yy']
    public function hMset($key, $fv = [])
    {
        return $this->_redis->hMset($key, $fv);
    }

    public function hGet($key, $field)
    {
        return $this->_redis->hGet($key, $field);
    }

    public function hGetAll($key)
    {
        return $this->_redis->hGetAll($key);
    }

    public function del($key)
    {
        return $this->_redis->del($key);
    }

    public function exists($key)
    {
        return $this->_redis->exists($key);
    }

    public function expire($key, $ttl = 0)
    {
        return $this->_redis->expire($key, $ttl);
    }

    //是否超出访问频率限制，指定时间段内的key访问次数，返回值：true-超限，false-未超限
    public function isOutReqRateLimit($mark_key, $time_interval, $limit_num)
    {
        $req_num = $this->_redis->incr($mark_key);
        $ttl = $this->_redis->ttl($mark_key);
        if ($ttl < 0) {
            $this->_redis->expire($mark_key, $time_interval);
        }
        if ($req_num > $limit_num) {
            return true;
        }
        return false;
    }

    function __destruct()
    {
        $this->_redis->close();
    }
}
