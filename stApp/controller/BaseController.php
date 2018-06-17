<?php
/**
 * Created by PhpStorm.
 * User: haier
 * Date: 2018-6-18
 * Time: 1:02
 */

namespace stApp\controller;


class BaseController
{

    /**
     * @var int $status 用于路由调用的状态码 默认200
     */
    protected $status = 200;

    /** getter方法
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }
    /**
     * @param int $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }



    /**
     * 继承类将通过 __construct方法
     * 调用具体的model类 并返回json
     * model类会调用dao类进行数据库操作
     *   或某些不需要数据库的功能进行直接实现
     **/

}