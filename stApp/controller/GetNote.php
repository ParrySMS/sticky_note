<?php
/**
 * Created by PhpStorm.
 * User: L
 * Date: 2018-8-12
 * Time: 21:16
 */

namespace stApp\controller;

use \Exception;
use stApp\common\LogicCheck;
use stApp\service\Note;

class GetNote extends BaseController
{
    private $check;
    private $uid;
    private $note;

    public function __construct($has_token = true)
    {
        try {
            //参数检查
            $this->check = new LogicCheck($has_token);
            $uid = $this->check->token_info['uid'];
            $this->uid = $this->check->id($uid);
            //行为记录
            $this->actionLog($this->uid);

            $this->note = new Note();
        } catch (Exception $e) {
            $this->error($e);
        }
    }

    //todo 获取未完成 获取已完成 使用istop和committime分两次获取
    //todo 一次获取全部数据 再进行分别排序？？
    public function init()
    {
        $this->note
    }
}