<?php
/**
 * Created by PhpStorm.
 * User: L
 * Date: 2018-8-10
 * Time: 17:39
 */

namespace stApp\common;



use stApp\dao\Action;

class Log
{


    /**
     * Log constructor.
     */
    public function __construct($uid)
    {
        //这个uid 必须是已经检查过的

        //默认直接进行记录action行为
        $this->action($uid);

    }

    /** 记录action
     * @param $uid
     * @throws \Exception
     */
    public function action($uid){
        $http = new Http();
        $ip = $http->getIP();
        $agent = $http->getAgent();
        $uri = $_SERVER['REQUEST_URI'];
        // 实现dao类
        $action = new Action();
        $action->insert($uid,$ip,$agent,$uri);

    }

}