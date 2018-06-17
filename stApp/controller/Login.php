<?php

/**
 * Created by PhpStorm.
 * User: haier
 * Date: 2018-6-17
 * Time: 20:19
 */
namespace stApp\controller;
use Exception;
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
        //todo 参数检查 获取openid 获取用户信息 生成token
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