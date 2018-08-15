<?php
/**
 * Created by PhpStorm.
 * User: L
 * Date: 2018-8-15
 * Time: 10:01
 */

namespace stApp\controller;

use Exception;
use stApp\common\LogicCheck;
use stApp\service\WxRequest;

class JSSDK extends BaseController
{
    public function __construct($url)
    {
        try {
            $check = new LogicCheck();
            $uid = $check->token_info['uid'];
            $uid = $check->id($uid);
            //记录行为
            $this->actionLog($uid);
            $check->url($url);

            $this->signPackage($url);

        }catch (Exception $e){
            $this->error($e);
        }
    }

    /** 获取sign包 输出json
     * @param $url
     * @throws Exception
     */
    protected function signPackage($url){
        $wx = new WxRequest();
        $this->echoJson($wx->sign($url));
    }

}