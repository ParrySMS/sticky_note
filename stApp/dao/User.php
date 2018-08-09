<?php

/**
 * Created by PhpStorm.
 * User: haier
 * Date: 2018-6-18
 * Time: 12:04
 */

namespace stApp\dao;

use Exception;

class User extends BaseDao
{
    protected $table = PREFIX . '_user';

    /** 根据openid返回uid 如果没有则返回0
     * @param $openid
     * @return array|bool|int
     * @throws Exception
     */
    public function getUser($openid)
    {
        $data = $this->database->select($this->table, [
            'id',
            'visible'
        ], [
            'AND' => [
                'openid' => $openid,
                // 'visible[!]' => 0
            ]
        ]);

        if (!is_array($data)) {
            throw new Exception(__CLASS__ .'->'. __FUNCTION__ . '(): error', 500);
        }

        //找不到唯一的一个
        if (sizeof($data) != 1) {
            return 0;
        }

        return $data[0];
    }

    /** 该方法弃用 插入新用户并返回id 输入多参数
     * 参见 insertUser 方法
     * @param $openid
     * @param $nickname
     * @param $sex
     * @param $province
     * @param $city
     * @param $country
     * @param $headimgurl
     * @param $privilege
     * @param $unionid
     * @return int|string
     * @throws Exception
     */
//    public function insertUser_multiPm($openid, $nickname, $sex, $province, $city, $country, $headimgurl, $privilege, $unionid)
//    {
//        $pdo = $this->database->insert($this->table, [
//            'openid' => $openid,
//            'nickname' => $nickname,
//            'sex' => $sex,
//            'province' => $province,
//            'city' => $city,
//            'country' => $country,
//            'headimgurl' => $headimgurl,
//            'privilege' => $privilege,
//            'unionid' => $unionid,
//        ]);
//        $row = $pdo->rowCount();
//        if ($row != 1) {//插入1条失败
//            throw new Exception(__CLASS__ .'->'. __FUNCTION__ . '(): error', 500);
//        }
//
//        return $this->database->id();
//
//    }


    /** 插入新用户并返回id 输入唯一参数信息包
     * @param $info
     * @return int|mixed|string
     * @throws Exception
     */
    public function insertUser($info)
    {
        $pdo = $this->database->insert($this->table, [
            'openid' => $info->openid,
            'nickname' => $info->nickname,
            'sex' => $info->sex,
            'province' => $info->province,
            'city' => $info->city,
            'country' => $info->country,
            'headimgurl' => $info->headimgurl,
            'privilege' => $info->privilege,
            'unionid' => $info->unionid,
            'time'=>date('Y-m-d H:i:s'),
            'visible'=>1
        ]);
        $row = $pdo->rowCount();
        if ($row != 1) {//插入1条失败
            throw new Exception( __CLASS__ .'->'. __FUNCTION__ . '(): error', 500);
        }

        return $this->database->id();
    }

    /** 判断用户是否存在
     * @param $uid
     * @return bool
     */
    public function hasUser($uid)
    {
            $has = $this->database->has($this->table,[
                'AND'=>[
                    'id'=>$uid,
                    'visible[!]'=>0
                ]
            ]);

            return $has;
    }

    /** 获取用户opendid
     * @param $uid
     * @throws Exception
     */
    public function getOpenid($uid)
    {
        $data = $this->database->select($this->table,[
            'openid'
        ],[
            'AND'=>[
                'id'=>$uid,
                'visible'=>1
                //todo 关于visible的条件问题要重写构思
            ]
        ]);

        if(!is_array($data)||sizeof($data)==0){
            throw new Exception(__CLASS__.__FUNCTION__.' error',500);
        }
    }





}