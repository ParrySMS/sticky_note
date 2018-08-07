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
    public $token_info = [];


    /** 默认开启token检查 并且存储token_info
     * LogicCheck constructor.
     * @param bool $has_token
     * @throws Exception
     */
    public function __construct($has_token = true)
    {
        if ($has_token == true) {
            //检查token
            if (!isset($_COOKIE[TOKEN_NAME])) {

                throw new Exception('Precondition Failed', 412);
            }
            $token = $_COOKIE[TOKEN_NAME];
            $this->token_info = $this->getTokenInfo($token);
        }
    }


    public function getTokenInfo($token)
    {

        $crypt = new ThinkCrypt();
        $tokenAr = $crypt->tokenDecrypt($token);

        $uid = $tokenAr['uid'];
        $md5_openid = $tokenAr['md5_openid'];

        $user = new User();
        if (!$user->hasUser($uid)) {
            throw new Exception(MSG_BLACK_USER, 20040301);
        }

        $openid = $user->getOpenid($uid);
        if (md5($openid) != $md5_openid) {
            throw new Exception('TOKEN_ERROR: invalid token3', 403);
        }

        return $tokenAr;

    }

    public function postNote($note)
    {
        //可能会有数字0 故不用empty
        if(is_null($note)||$note === ''){
            throw new Exception('note null',400);
        }
    }



}