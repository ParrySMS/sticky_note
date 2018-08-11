<?php
/**
 * Created by PhpStorm.
 * User: haier
 * Date: 2018-6-18
 * Time: 1:02
 */

namespace stApp\controller;

use \Exception;
use stApp\model\Json;
use stApp\common\Log;

class BaseController
{
    /**
     * @var int $status 用于路由调用的状态码 默认200
     */
    protected $status = 200;



    // 做用户行为记录
    public function actionLog($uid)
    {
        new Log($uid);
    }

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


    public function error(Exception $e){
        if ($e->getCode() <= 505) {//非200 直接输出
            $this->setStatus($e->getCode());
            echo MSG_ERROR_INFO . $e->getMessage();

        } else { //200下状态码 报错用json处理
            $this->setStatus(200);
            $json = new Json($e->getMessage(), null, $e->getCode());
            $this->echoJson($json);
        }
    }

    public function echoJson(Json $json)
    {
        if (!is_null($json)) {
            print_r(json_encode($json));
        }
    }



    /**
     * 继承类将通过 __construct方法
     * 调用具体的 server 类 并返回json
     * server 类会调用dao类进行数据库操作
     *   或某些不需要数据库的功能进行直接实现
     **/

}