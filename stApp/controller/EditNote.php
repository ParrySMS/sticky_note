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
use stApp\service\Note;

class EditNote extends BaseController
{

    /** 调整状态控制器
     * @param $nid
     * @param $note_status
     */
    public function editStatus($nid, $note_status)
    {
        try {
            //参数检查
            $check = new LogicCheck();
            $uid = $check->token_info['uid'];
            $nid = $check->nid($nid);

            $this->setNoteStatus($uid,$nid, $note_status);

        } catch (Exception $e) {
            $this->error($e);
        }

    }

    public function top($nid)
    {
        try {
            //参数检查
            $check = new LogicCheck();
            $uid = $check->token_info['uid'];
            $nid = $check->nid($nid);

            $this->setNoteTop($uid,$nid);

        } catch (Exception $e) {
            $this->error($e);
        }


    }



    /** 实现不同类别具体状态调整
     * @param $uid
     * @param $nid
     * @param $note_status
     * @throws Exception
     */
    protected function setNoteStatus($uid,$nid, $note_status)
    {
        $note = new Note();
        switch ($note_status){
            case NOTE_STATUS_UNFINISHED:
                $json = $note->unfinish($uid,$nid);
                break;
            case NOTE_STATUS_FINISHED:
                $json = $note->finish($uid,$nid);
                break;
                //预留未来可能多个状态区的设计
            default:
                throw new Exception(__CLASS__ .'->'. __FUNCTION__ . '(): $note_status value error', 500);

        }


        $this->echoJson($json);
    }


}