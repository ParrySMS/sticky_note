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
    //tokenAr = compact('uid', 'md5_openid', 'ip', 'date', 'nonstr');


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


    /** 解密之后逻辑匹配检查
     * @param $token
     * @return array|null
     * @throws Exception
     */
    public function getTokenInfo($token)
    {

        $crypt = new ThinkCrypt();
        $tokenAr = $crypt->tokenDecrypt($token);

        $uid = $tokenAr['uid'];
        $md5_openid = $tokenAr['md5_openid'];

        $user = new User();
        if (!$user->hasUser($uid)) {
            throw new Exception(MSG_NO_USER, 20040401);
        }

        $openid = $user->getOpenid($uid);
        if (!md5($openid) === $md5_openid) {
            throw new Exception('TOKEN_ERROR: invalid token3', 403);
        }

        return $tokenAr;

    }

    /** note数据检查
     * @param $text
     * @return array|string
     * @throws Exception
     *
     */
    public function note($text)
    {
        //空检查 可能会有数字0 故不用empty
        if (is_null($text) || $text === '') {
            throw new Exception('text null', 400);
        }
        //长度检查
        return $this->lenCheck($text);
    }

    /** code空检查
     * @param $code
     * @throws Exception
     */
    public function code($code)
    {
        if (empty($code)) {
            throw new Exception('code null', 400);
        }
    }

    /** 检查id
     * @param $id
     * @param bool $intval
     * @return int|null|string
     * @throws Exception
     */
    public function id($id, $intval = true)
    {
        $id = $this->getNumeric($id, $intval);
        if ($id === null) {
            throw new Exception('id null', 400);
        }
        return $id;
    }


    /** 检查url
     * @param $url
     * @throws Exception
     */
    public function url($url)
    {

        if (empty($url)) {
            throw new Exception('url null', 400);
        }

        $pattern = "/\b(?:(?:https?|http):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i";
        if (!preg_match($pattern, $url)) {
            throw new Exception("url type error: $url", 400);
        }
    }


}