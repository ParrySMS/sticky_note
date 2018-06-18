<?php

/**
 * Created by PhpStorm.
 * User: haier
 * Date: 2018-6-17
 * Time: 20:19
 */
namespace stApp\controller;
use Exception;
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

        }catch (Exception $e){
            $this->setStatus($e->getCode());
            echo MSG_ERROR_INFO.$e->getMessage();
        }
    }

    public function wx_login($code){
        //获取openid 获取用户信息
        $wx = new WxRequest();
        $acc = $wx->getAccessToken($code);
        $info = $wx->getUserinfo($acc->access_token,$acc->openid);

        //todo 创建用户 生成token
        $scCT = new CreateToken($info);


        $json = $scLg->getJson();
        if (!is_null($json)) {
            print_r(json_encode($json));
        }



    }


    public function qq_login(){
    //暂无需求
    }

    public function pw_login(){
    //暂无需求

    }

    public function agent_login(){
    //暂无需求
    }
}