<?php
/**
 * Created by PhpStorm.
 * User: L
 * Date: 2018-8-10
 * Time: 9:43
 */

namespace stApp\controller;

use \Exception;
use stApp\common\LogicCheck;

class EditNote extends BaseController
{

    public function editStatus($nid, $finish)
    {
        try {
            //参数检查
            $check = new LogicCheck();
            $uid = $check->token_info['uid'];
            $nid = $check->nid($nid);

            $note_status = ($finish === true) ? NOTE_STATUS_FINISHED : NOTE_STATUS_NOT_FINISH;

            $this->setNoteStatus($nid, $note_status);

        } catch (Exception $e) {
            $this->error($e);
        }

    }

    public function setNoteStatus($nid, $note_status)
    {

        if ($note_status == NOTE_STATUS_FINISHED) {
            $finish_time = date(DB_TIME_FORMAT);
        }



    }


}