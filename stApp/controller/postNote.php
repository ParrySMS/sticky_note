<?php
/**
 * Created by PhpStorm.
 * User: L
 * Date: 2018-8-7
 * Time: 11:58
 */

namespace stApp\controller;
use \Exception;
use stApp\common\LogicCheck;
use stApp\service\Note;

class postNote extends BaseController
{
    /**
     * postNote constructor.
     */
    public function __construct($note_text)
    {
        try {
            $check = new LogicCheck();
            $uid = $check->token_info['uid'];

            $note_text = $check->note($note_text);
            $this->post($uid,$note_text);

        }catch (Exception $e){
            $this->error($e);
        }
    }

    private function post($uid,$note_text){
        $note = new Note();
        $this->echoJson($note->post($uid,$note_text));
    }

}