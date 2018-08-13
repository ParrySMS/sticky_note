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
    private $check;
    private $uid;
    private $note;

    /**
     * EditNote constructor.
     */
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

    /** 调整状态控制器
     * @param $nid
     * @param $note_status
     */
    public function editStatus($nid, $note_status)
    {
        try {

            $nid = $this->check->id($nid);

            $this->setNoteStatus($this->uid, $nid, $note_status);

        } catch (Exception $e) {
            $this->error($e);
        }

    }

    /** 调整置顶控制器
     * @param $nid
     * @param $top_status
     */
    public function top($nid, $top_status)
    {
        try {
            //参数检查
            $nid = $this->check->id($nid);

            $this->setNoteTop($this->uid, $nid, $top_status);

        } catch (Exception $e) {
            $this->error($e);
        }


    }

    /** 软删除 控制器 故放进edit里
     * @param $nid
     */
    public function delete($nid)
    {
        try {
            //参数检查
            $nid = $this->check->id($nid);
            $json = $this->note->delete($this->uid,$nid);
            $this->echoJson($json);

        } catch (Exception $e) {
            $this->error($e);
        }

    }

    /** 编辑内容 测试器
     * @param $nid
     * @param $text
     */
    public function edit($nid,$text)
    {
        try {
            //参数检查
            $nid = $this->check->id($nid);
            $text = $this->check->note($text);

            $json = $this->note->edit($this->uid,$nid,$text);
            $this->echoJson($json);

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
    protected function setNoteStatus($uid, $nid, $note_status)
    {

        switch ($note_status) {
            //因为不同状态涉及到时间等多个参数 故分开写函数
            case NOTE_STATUS_UNFINISHED:
                $json = $this->note->unfinish($uid, $nid);
                break;
            case NOTE_STATUS_FINISHED:
                $json = $this->note->finish($uid, $nid);
                break;
            //预留未来可能多个状态区的设计
            default:
                throw new Exception(__CLASS__ . '->' . __FUNCTION__ . '(): $note_status value error', 500);

        }
        $this->echoJson($json);
    }

    /** 设置置顶状态
     * @param $uid
     * @param $nid
     * @param $top_status
     * @throws Exception
     */
    protected function setNoteTop($uid, $nid, $top_status)
    {
        switch ($top_status) {
            //因为只涉及到一个 is_top 字段  所以合并成一个函数
            case NOTE_IS_TOP:
            case NOTE_NOT_TOP:
                //用一个函数
                $json = $this->note->top($uid, $nid, $top_status);
                break;
            //预留未来可能多个状态区的设计
            default:
                throw new Exception(__CLASS__ . '->' . __FUNCTION__ . '(): $top_status value error', 500);

        }
        $this->echoJson($json);
    }


}