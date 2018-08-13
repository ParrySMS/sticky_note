<?php
/**
 * Created by PhpStorm.
 * User: L
 * Date: 2018-8-9
 * Time: 14:48
 */

namespace stApp\service;

use \Exception;

use stApp\model\Json;

class Note extends BaseService
{
    private $note;

    /** 创建操作的dao 和 返回的json
     * Note constructor.
     */
    public function __construct()
    {
        $this->note = new \stApp\dao\Note();
        $this->json = new Json();
    }

    /** 插入一条note 并且返回这个note对象
     * @param $uid
     * @param $note_text
     * @return Json
     * @throws \Exception
     */
    public function post($uid, $note_text)
    {
        $time = date(DB_TIME_FORMAT);
        $day_time = date(RET_TIME_FORMAT, strtotime($time));

        $nid = $this->note->insert($uid, $note_text, $time);

        //后端进行文字缩略 已弃用
//        $content = mb_substr($note_text, 0, NOTE_SHOW_CONTENT_LEN);
//        if (mb_strlen($content) == NOTE_SHOW_CONTENT_LEN) {
//            $content = $content . '...';
//        }
        $noteOption = [
            'id' => $nid,
            'text' => $note_text,
            'time' => $day_time
        ];

        $note_mod = new \stApp\model\Note($noteOption);
        $retdata = (object)['note' => $note_mod];
        $this->json->setRetdata($retdata);
        return $this->json;
    }


    /** 调整状态为完成 先检查逻辑问题
     * @param $uid
     * @param $nid
     * @return Json
     * @throws Exception
     */
    public function finish($uid, $nid)
    {
        if ($this->hasNoteStatus($uid, $nid, NOTE_STATUS_FINISHED)) {
            throw new Exception(MSG_HAS_FINISHED, 20040302);
        }

        $this->note->updateFinish($uid, $nid);

        $retdata = (object)['nid' => $nid];
        $this->json->setRetdata($retdata);
        return $this->json;
    }


    /**调整状态为等待 先检查逻辑问题
     * @param $uid
     * @param $nid
     * @return Json
     * @throws Exception
     */
    public function unfinish($uid, $nid)
    {
        if ($this->hasNoteStatus($uid, $nid, NOTE_STATUS_UNFINISHED)) {
            throw new Exception(MSG_HAS_UNFINISHED, 20040303);
        }

        $this->note->updateUnfinish($uid, $nid);

        $retdata = (object)['nid' => $nid];
        $this->json->setRetdata($retdata);
        return $this->json;
    }


    /**
     * @param $uid
     * @param $nid
     * @param $top_status
     * @return Json
     * @throws Exception
     */
    public function top($uid, $nid, $top_status)
    {
        // 不同类别的报错 做过于频繁的处理
        if ($this->hasNoteTop($uid, $nid, $top_status)) {
            throw new Exception(MSG_HAS_TOPPED, 20040304);
        }

        //继续接
        $this->note->updateFieldIsTop($uid, $nid, $top_status);

        $retdata = (object)['nid' => $nid];
        $this->json->setRetdata($retdata);
        return $this->json;
    }

    /** 获取用户已经写了的note
     * 通过两次链接数据库实现查询
     * @param $uid
     * @return Json
     * @throws Exception
     */
    public function getMyNote($uid)
    {
        //用于保存的对象数组
        unset($unfinished_notes);
        $unfinished_notes = [];
        unset($finished_notes);
        $finished_notes = [];

        //取数据
        $unfinished_data = $this->note->getNotes($uid, NOTE_STATUS_UNFINISHED);

        if (sizeof($unfinished_data) > 0) {//有数据的情况下
            foreach ($unfinished_data as $d) {
                $d['time'] = date(RET_TIME_FORMAT, strtotime($d['time']));
                $unfinished_note = new \stApp\model\Note($d);
                $unfinished_notes [] = $unfinished_note;
            }
        }

        //取数据同理
        $finished_data = $this->note->getNotes($uid, NOTE_STATUS_FINISHED);

        if (sizeof($finished_data) > 0) {//有数据的情况下
            foreach ($finished_data as $d) {
                $d['time'] = date(RET_TIME_FORMAT, strtotime($d['time']));
                $finished_note = new \stApp\model\Note($d);
                $finished_notes [] = $finished_note;
            }
        }

        $retdata = (object)[
            'unfinished_num' => sizeof($unfinished_notes),
            'unfinished' => $unfinished_notes,
            'finished_num' => sizeof($finished_notes),
            'finished' => $finished_notes
        ];
        $this->json->setRetdata($retdata);
        return $this->json;

    }

    /** 实现删除 返回nid
     * @param $uid
     * @param $nid
     * @return Json
     * @throws Exception
     */
    public function delete($uid,$nid)
    {
        $this->note->setVisible($uid,$nid);

        $retdata = (object)[
            'nid' => $nid
        ];

        $this->json->setRetdata($retdata);
        return $this->json;
    }


    /** 判断是否有某状态下的某个note
     * @param $uid
     * @param $nid
     * @param $status
     * @return bool
     * @throws Exception
     */
    protected function hasNoteStatus($uid, $nid, $status)
    {
        $note_status = $this->note->getNoteStatus($uid, $nid);
        //status 可能是0
        if (is_null($note_status) || $note_status === '') {
            throw new Exception(MSG_NO_NOTE, 20040402);
        }

        return ($status == $note_status) ? true : false;
    }


    /** 判断是否某状态下已经有对应置顶状态
     * @param $uid
     * @param $nid
     * @param $top_status
     * @return bool
     * @throws Exception
     */
    protected function hasNoteTop($uid, $nid, $top_status)
    {
        $is_top = $this->note->getNoteTop($uid, $nid);
        //$is_top 可能是0
        if (is_null($is_top) || $is_top === '') {
            throw new Exception(MSG_NO_NOTE, 20040402);
        }

        return ($is_top == $top_status) ? true : false;
    }


}