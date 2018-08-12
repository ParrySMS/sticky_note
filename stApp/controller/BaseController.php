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
    private $uid;


    /** 做用户行为记录
     * @param null $uid
     * @param null $error_code
     */
    public function actionLog($uid = null,$error_code = null)
    {
        $this->uid = $uid;
        $log = new Log();
        $log->action($uid,$error_code);
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


    /** 进行报错码的处理 分为200下和非200下报错
     * @param Exception $e
     */
    public function error(Exception $e){
        $this->actionLog($this->uid,$e->getCode());
        if ($e->getCode() <= 505) {//非200 直接输出
            $this->setStatus($e->getCode());
            echo MSG_ERROR_INFO . $e->getMessage();

        } else { //200下状态码 报错用json处理
            $this->setStatus(200);
            $json = new Json($e->getMessage(), null, $e->getCode());
            $this->echoJson($json);
        }
    }

    /** 输出json对象
     * @param Json $json
     */
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