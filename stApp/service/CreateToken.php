<?php
/**
 * Created by PhpStorm.
 * User: haier
 * Date: 2018-6-18
 * Time: 11:55
 */

namespace stApp\service;


class CreateToken
{


    /**
     * CreateToken constructor.
     */
    public function __construct($info)
    {
        $openid = $info->openid;


    }

    public function getNewUserid(){
        //todo 首次进入 创建用户
    }

    public function getUid(){
            //todo 得到用户id 非首次则直接查询
    }


}