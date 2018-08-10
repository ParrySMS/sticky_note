<?php
/**
 * Created by PhpStorm.
 * User: L
 * Date: 2018-8-10
 * Time: 17:39
 */

namespace stApp\service;


use stApp\common\Http;

class Log extends BaseService
{


    /**
     * Log constructor.
     */
    public function __construct($uid)
    {
        $http = new Http();
        $ip = $http->getIP();
        $agent = $http->getAgent();
        $uri = $_SERVER['REQUEST_URI'];
        //todo 实现dao类
    }
}