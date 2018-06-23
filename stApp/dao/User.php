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
               // 'visible' => 1
            ]
        ]);

        if(!is_array($data)){
            throw new Exception(__FUNCTION__.' error',500);
        }

        //空数组 查不到
        if(sizeof($data)==0){
            return 0;
        }

        return $data;
    }

    /** 插入新用户并返回id
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
    public function insertUser( $openid,$nickname,$sex,$province,$city,$country,$headimgurl,$privilege,$unionid){
        $pdo = $this->database->insert($this->table,[
            'openid'=>$openid,
            'nickname'=>$nickname,
            'sex'=>$sex,
            'province'=>$province,
            'city'=>$city,
            'country'=>$country,
            'headimgurl'=>$headimgurl,
            'privilege'=>$privilege,
            'unionid'=>$unionid,
        ]);
        $row = $pdo->rowCount();
        if($row!=1){//插入1条失败
            throw new Exception(__FUNCTION__.' error',500);
        }

        return $this->database->id();

    }


}