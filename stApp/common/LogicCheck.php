<?php
/**
 * Created by PhpStorm.
 * User: L
 * Date: 2018-8-4
 * Time: 10:45
 */

namespace stApp\common;
use \Exception;
use stApp\dao\User;

class LogicCheck extends PmCheck
{

    public function __construct($has_token = true)
    {
        if ($has_token == true) {
            //检查token
            if(!isset($_COOKIE[TOKEN_NAME])){
                throw new Exception('Precondition Failed',412);
            }

            $token = $_COOKIE[TOKEN_NAME];
            $this->isVaildToken($token,false);
        }
    }

    public function isVaildToken($token,$return = true){

        $crypt = new ThinkCrypt();
        $tokenAr = $crypt->tokenDecrypt($token);

        $uid = $tokenAr['uid'];
        $md5_openid = $tokenAr['md5_openid'];

        $user = new User();
        if(!$user->hasUser($uid)) {
// todo 看文档 确定报错内容
            throw new Exception('');
        }

            $openid = $user->getOpenid($uid);
            if(md5($openid)!=$md5_openid) {
                throw new Exception('');
            }
    }



}