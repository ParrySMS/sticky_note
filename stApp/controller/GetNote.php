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

    /**
     * @throws Exception
     */
    public function init()
    {
        $this->echoJson($this->note->getMyNote($this->uid));
    }
}