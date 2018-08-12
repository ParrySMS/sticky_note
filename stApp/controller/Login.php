<?php

/**
 * Created by PhpStorm.
 * User: haier
 * Date: 2018-6-17
 * Time: 20:19
 */

namespace stApp\controller;

use Exception;
use stApp\common\LogicCheck;
use stApp\common\PmCheck;
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

    /** 应该是用构造方法控制登录模式
     * Login constructor.
     */
    public function __construct($code)
    {
        try {
            $this->actionLog();
            $check = new LogicCheck(false);
            $check->code($code);

            $this->wx_login($code);

        } catch (Exception $e) {
            $this->error($e);
        }
    }

    /** 微信登录 生成token
     * @param $code
     * @throws Exception
     */
    protected function wx_login($code)
    {
        //获取openid 获取用户信息
        $wx = new WxRequest();
        $acc = $wx->getAccessToken($code);

        $scCT = new CreateToken();
        //默认假设老用户，直接获取token
        $token = $scCT->createToken($acc->openid);

        if ($token === 0) {//新用户 获取失败 需要更多信息
            $info = $wx->getUserinfo($acc->access_token, $acc->openid);
            $token = $scCT->createToken($acc->openid, $info);
        }

        if (empty($token)) {
            throw new Exception(__CLASS__.__FUNCTION__ . ' error: token empty', 500);
        }
        //token创建成功
        $this->token = $token;
        $this->echoJson($scCT->getJson());
    }


    protected function qq_login()
    {
        //暂无需求
    }

    protected function pw_login()
    {
        //暂无需求

    }

    protected function agent_login()
    {
        //暂无需求
    }
}