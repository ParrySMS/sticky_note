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
            throw new Exception(MSG_NO_USER, 20040302);
        }

        $openid = $user->getOpenid($uid);
        if (!md5($openid) === $md5_openid) {
            throw new Exception('TOKEN_ERROR: invalid token3', 403);
        }

        return $tokenAr;

    }

    /** note数据检查
     * @param $note
     * @return array|string
     * @throws Exception
     *
     */
    public function note($note)
    {
        //空检查 可能会有数字0 故不用empty
        if(is_null($note)||$note === ''){
            throw new Exception('text null',400);
        }
        //长度检查
        return $this->lenCheck($note);
    }

    /** code空检查
     * @param $code
     * @throws Exception
     */
    public function code($code)
    {
        if(empty($code)){
            throw new Exception('code null',400);
        }
    }

    /** 检查nid
     * @param $nid
     * @param bool $intval
     * @return int|null|string
     * @throws Exception
     */
    public function nid($nid, $intval = true){
        $nid = $this->getNumeric($nid,$intval);
        if($nid === null){
            throw new Exception('nid null',400);
        }
        return $nid;
    }



}