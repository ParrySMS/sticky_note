<?php
/**
 * Created by PhpStorm.
 * User: L
 * Date: 2018-8-9
 * Time: 14:48
 */

namespace stApp\service;


use stApp\model\Json;

class Note extends BaseService
{
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
    public function post($uid,$note_text)
    {
        $time = date(DB_TIME_FORMAT);
        $day_time = date(RET_TIME_FORMAT, strtotime($time));

        $nid = $this->note->insert($uid, $note_text, $time);

        //后端进行文字缩略 已弃用
//        $content = mb_substr($note_text, 0, NOTE_SHOW_CONTENT_LEN);
//        if (mb_strlen($content) == NOTE_SHOW_CONTENT_LEN) {
//            $content = $content . '...';
//        }

        $note_mod = new \stApp\model\Note($nid,$note_text,$day_time);
        $retdata = (object)['note'=>$note_mod];
        $this->json->setRetdata($retdata);
        return $this->json;
    }

}