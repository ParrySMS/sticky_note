<?php
/**
 * Created by PhpStorm.
 * User: L
 * Date: 2018-8-11
 * Time: 9:44
 */

namespace stApp\dao;

use \Exception;

class Action extends BaseDao
{
    protected $table;

    public function __construct()
    {
        parent::__construct();
        $this->table = PREFIX.'_action';
    }


    /** 插入记录 并且返回id
     * @param $uid
     * @param $ip
     * @param $agent
     * @param $uri
     * @param $error_code
     * @param null $time
     * @return int|mixed|string
     */
    public function insert($uid,$ip,$agent,$uri,$error_code,$time = null)
    {
        //秒级时间
        if ($time === null) {
            $time = date(DB_TIME_FORMAT);
        }

        $pdo = $this->database->insert($this->table,[
            'uid'=>$uid,
            'agent'=>$agent,
            'ip'=>$ip,
            'uri'=>$uri,
            'error_code'=>$error_code,
            'time'=>$time,
            'visible'=>VISIBLE_NORMAL
        ]);

        $id = $this->database->id();
        //因为可能是在catch块里的记录 所以不适用throw报错
        if (!is_numeric($id) || $id < 1) {
//          var_dump($this->database->error());
            echo '<br/>'.__CLASS__ . '->' . __FUNCTION__ . '(): error';
//            throw new Exception(__CLASS__ . '->' . __FUNCTION__ . '(): error', 500);
        }
        return $id;
    }


}