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
        $note_mod = new \stApp\model\Note($nid, $note_text, $day_time);
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


    public function top($uid, $nid, $top_status)
    {
        //todo 不同类别的报错怎么处理
        if ($this->hasNoteTop($uid, $nid, $top_status)) {
            throw new Exception(MSG_HAS_FINISHED, 20040302);
        }

        $this->note->updateFinish($uid, $nid);

        $retdata = (object)['nid' => $nid];
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


    protected function hasNoteTop($uid, $nid, $is_top)
    {
        $is_top = $this->note->getNoteTop($uid, $nid);
        //status 可能是0
        if (is_null($note_status) || $note_status === '') {
            throw new Exception(MSG_NO_NOTE, 20040402);
        }

        return ($status == $note_status) ? true : false;
    }




}