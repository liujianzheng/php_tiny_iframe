<?php
require_once dirname(__FILE__) . '/common/Common.php';
try {
//    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
//        echo getReturnData(NOTE, 'Only support POST request');
//    }
//    $input_str = file_get_contents("php://input");
//    $request = json_decode($input_str, true);
//    $result = Process($request);
    $result = Process($_REQUEST);
    header("Content-Length:" . strlen($result));
    echo $result;
} catch (Exception $e) {
    $result = getReturnData(ERROR, $e->getMessage());
    header("Content-Length:" . strlen($result));
    echo $result;
}

function Process($request)
{
    if(!isAuthAllowIn()){
        return getReturnData(NOTE, 'IP not authorize[' . getClientIp() . ']');
    }
    if (!isValidInput($request)) {
        return getReturnData(ERROR, 'Parameter contains illegal character');
    }
    $instance = instance($request['interfaceName']);
    if (!$instance) {
        return getReturnData(NOTE, 'InterfaceName not exist[' . $request['interfaceName'] . ']');
    }
    $retMsg = $instance->mustInput($request);
    if ($retMsg !== true) {
        return getReturnData(NOTE, $retMsg);
    }
    return $instance->main_handler($request);
}

?>