<?php

function getReturnData($code, $msg = 'successful', $data = [])
{
    return json_encode(['code' => $code, 'msg' => $msg, 'data' => $data]);
}

function doCurlPostRequest($url, $requestString, $timeout = 3, $header = array())
{
    if (empty($url) || $timeout <= 0) {
        return false;
    }
    $con = curl_init($url);
    if (empty($header)) {
        $header = ['Content-Type:application/json', 'charset:urf-8'];
    }
    curl_setopt($con, CURLOPT_HTTPHEADER, $header);
    curl_setopt($con, CURLOPT_POSTFIELDS, $requestString);
    curl_setopt($con, CURLOPT_POST, true);
    curl_setopt($con, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($con, CURLOPT_TIMEOUT, (int)$timeout);
    $response = curl_exec($con);
    curl_close($con);
    return $response;
}

function doCurlGetRequest($url, $timeout = 3, $header = array())
{
    if (empty($url) || $timeout <= 0) {
        return false;
    }
    $con = curl_init($url);
    if (empty($header)) {
        $header = ['Content-Type:application/json', 'charset:urf-8'];
    }
    curl_setopt($con, CURLOPT_HTTPHEADER, $header);
    curl_setopt($con, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($con, CURLOPT_TIMEOUT, (int)$timeout);
    $response = curl_exec($con);
    curl_close($con);
    return $response;
}


//常规输入监测，在interface.php所有处理之前执行
function isValidInput($data)
{
    if (empty($data)) {
        return true;
    }
    foreach ($data as $key => $value) {
        if (empty($value)) {
            continue;
        }
        if (is_array($value)) {
            if (!isValidInput($value)) {
                return false;
            }
            continue;
        }
        $value = trim($value);
        $str1 = strstr($value, "'");
        $str3 = strstr($value, '"');
        $str4 = strstr($value, ' ');
        if (!empty($str1) || !empty($str2) || !empty($str3)) {
            return false;
        }
        if (!empty($str4)) {
            $t_stamp = strtotime($str4);
            if ($t_stamp <= 0) {
                return false;
            }
        }
    }
    return true;
}

//是否是有效字符串（英文大小写、数据、下划线）
function isValidStr($str)
{
    $rule = '/^[a-z0-9_]+$/i';
    return empty($str) ? false : preg_match($rule, $str);
}

//是否是合法字符串
function isLegalStr($str)
{
    $rule = '/^[0-9a-z_!@#$%^&*.]+$/i';
    return empty($str) ? false : preg_match($rule, $str);
}

//是否是合法URL
function isLegalUrl($str)
{
    $rule_str = '/^[0-9a-z:&?=._%\-\/#]+$/i';
    return empty($str) ? false : preg_match($rule_str, $str);
}

//根据接口路由创建对象
function instance($interfaceName)
{
    $info = explode("_", $interfaceName);
    $dir = '';
    for ($i = 0; $i < count($info) - 1; $i++) {
        $dir = $dir . '/' . strtolower($info[$i]);
    }
    $file_name = dirname(__FILE__) . '/../interface/' . $dir . '/' . $interfaceName . '.php';

    if (file_exists($file_name)) {
        require_once($file_name);
        if (!class_exists($interfaceName)) {
            return null;
        } else {
            return new $interfaceName();
        }
    } else {
        return null;
    }
}

//获取数组是否包含指定的keys
function getIncludeKeysInfo($args, $keys)
{
    foreach ($keys as $key) {
        if (!isset($args[$key])) {
            return "{$key} is empty";
        }
    }
    return true;
}

//获取本地ip
function getLocalIp()
{
    if (!empty($_SERVER['SERVER_ADDR'])) {
        return $_SERVER['SERVER_ADDR'];
    }
    $result = shell_exec("/sbin/ifconfig");
    if (preg_match_all("/inet (\d+\.\d+\.\d+\.\d+)/", $result, $match) !== 0) {
        foreach ($match[0] as $k => $v) {
            if ($match[1][$k] != "127.0.0.1")
                return $match[1][$k];
        }
    }
    return "";
}

//获取客户端ip
function getClientIp()
{
    if (@$_SERVER["HTTP_X_FORWARDED_FOR"])
        $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
    else if (@$_SERVER["HTTP_CLIENT_IP"])
        $ip = $_SERVER["HTTP_CLIENT_IP"];
    else if (@$_SERVER["REMOTE_ADDR"])
        $ip = $_SERVER["REMOTE_ADDR"];
    else if (@getenv("HTTP_X_FORWARDED_FOR"))
        $ip = getenv("HTTP_X_FORWARDED_FOR");
    else if (@getenv("HTTP_CLIENT_IP"))
        $ip = getenv("HTTP_CLIENT_IP");
    else if (@getenv("REMOTE_ADDR"))
        $ip = getenv("REMOTE_ADDR");
    else
        $ip = "";
    return $ip;
}

?>
