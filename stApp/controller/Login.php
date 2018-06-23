<?php

/**
 * Created by PhpStorm.
 * User: haier
 * Date: 2018-6-17
 * Time: 20:19
 */

namespace stApp\controller;

use Exception;
use stApp\model\Json;
use stApp\service\WxRequest;
use stApp\service\CreateToken;

class Login extends BaseController
{
    private $token;

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Login constructor.
     */
    public function __construct($code)
    {
        try {

            $this->wx_login($code);


        } catch (Exception $e) {
            if ($e->getCode() <= 505) {//非200 直接输出
                $this->setStatus($e->getCode());
                echo MSG_ERROR_INFO . $e->getMessage();

            } else { //200下状态码 报错用json处理
                $this->setStatus(200);
                $json = new Json($e->getMessage(),null,$e->getCode());
                if (!is_null($json)) {
                    print_r(json_encode($json));
                }
            }
        }
    }

    /** 微信登录 生成token
     * @param $code
     * @throws Exception
     */
    public function wx_login($code)
    {
        //获取openid 获取用户信息
        $wx = new WxRequest();
        $acc = $wx->getAccessToken($code);

        $scCT = new CreateToken();
        //默认假设老用户，直接获取token
        $token = $scCT->createToken($acc->openid);

        if ($token == 0) {//新用户 获取失败 需要更多信息
            $info = $wx->getUserinfo($acc->access_token, $acc->openid);
            $token = $scCT->createToken($acc->openid, $info);
        }

        if(empty($token)){
            throw new Exception(__FUNCTION__.' error: token empty',500);
        }
        //token创建成功
        $json = $scCT->getJson();
        if (!is_null($json)) {
            print_r(json_encode($json));
        }
    }

    public function qq_login()
    {
        //暂无需求
    }

    public function pw_login()
    {
        //暂无需求

    }

    public function agent_login()
    {
        //暂无需求
    }
}