<?php
/**
 * Created by PhpStorm.
 * User: haier
 * Date: 2018-6-18
 * Time: 11:56
 */

namespace stApp\service;

date_default_timezone_set("Asia/Shanghai");

abstract class BaseService
{
    /**
     * @var \stApp\model\Json $json 功能实现后的封装好的json
     */
    protected $json;


    /** getter方法 实现某一个功能，返回数据封装到retdata对象里，返回json
     * @return \stApp\model\Json
     */
    public function getJson(){
        return $this->json;
    }
}
