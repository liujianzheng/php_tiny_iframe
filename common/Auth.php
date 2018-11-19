<?php

//访问鉴权

function isAuthAllowIn()
{
    $client_ip = getClientIp();
    $allow_ip = getConf('CLIENT_IP_WHITE_LIST');
    $ip_white_list = explode(',', $allow_ip['IP']);
    if (!in_array($client_ip, $ip_white_list)) {
        return false;
    }
    return true;
}

?>