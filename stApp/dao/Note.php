<?php
/**
 * Created by PhpStorm.
 * User: L
 * Date: 2018-8-9
 * Time: 14:51
 */

namespace stApp\dao;

use \Exception;

class Note extends BaseDao
{

    protected $table;

    /**
     * Note constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->table = PREFIX . '_note';
    }


    /** 插入新信息并且获取id
     * @param $uid
     * @param $note_text
     * @param null $commit_time
     * @param int $status
     * @param int $is_top
     * @return int|mixed|string
     * @throws Exception
     */
    public function insert($uid, $note_text, $commit_time = null, $status = NOTE_STATUS_UNFINISHED, $is_top = NOTE_NOT_TOP)
    {
        //秒级时间
        if ($commit_time === null) {
            $commit_time = date(DB_TIME_FORMAT);
        }

        $pdo = $this->database->insert($this->table, [
            'uid' => $uid,
            'text' => $note_text,
            'status' => $status,
            'is_top' => $is_top,
            'commit_time' => $commit_time,
            'finish_time' => null,
            'edit_time' => null,
            'total_edit' => 0,
            'visible' => NOTE_VISIBLE_NORMAL
        ]);

        $id = $this->database->id();
        if (!is_numeric($id) || $id < 1) {
//          var_dump($this->database->error());
            throw new Exception(__CLASS__ . '->' . __FUNCTION__ . '(): error', 500);
        }

        return $id;
    }


    /** note调整为完成状态 消除置顶 保存完成时间
     * @param $uid
     * @param $nid
     * @param int $status
     * @param null $finish_time
     * @throws Exception
     */
    public function updateFinish($uid, $nid, $status = NOTE_STATUS_FINISHED, $finish_time = null)
    {
        //秒级时间
        if ($finish_time === null) {
            $finish_time = date(DB_TIME_FORMAT);
        }

        $pdo = $this->database->update($this->table, [
            'status' => $status,
            'is_top' => NOTE_NOT_TOP,//改变状态 默认清除置顶
            'finish_time' => $finish_time,
        ], [
            'AND' => [
                'id' => $nid,
                'uid' => $uid,
                'visible[!]' => NOTE_VISIBLE_DELETED
            ]
        ]);

        $affected = $pdo->rowCount();
        if (!is_numeric($affected) || $affected != 1) {
            throw new Exception(__CLASS__ . '->' . __FUNCTION__ . '(): error', 500);
        }

    }


    public function updateUnfinish($uid, $nid, $status = NOTE_STATUS_UNFINISHED, $commit_time = null)
    {
        //秒级时间
        if ($commit_time === null) {
            $commit_time = date(DB_TIME_FORMAT);
        }

        //相当于把已结束的重新发布 原先的完成时间还是保留着
        $pdo = $this->database->update($this->table, [
            'status' => $status,
            'is_top' => NOTE_NOT_TOP,//改变状态 默认清除置顶
            'commit_time' => $commit_time,
        ], [
            'AND' => [
                'id' => $nid,
                'uid' => $uid,
                'visible[!]' => NOTE_VISIBLE_DELETED
            ]
        ]);

        $affected = $pdo->rowCount();
        if (!is_numeric($affected) || $affected != 1) {
            throw new Exception(__CLASS__ . '->' . __FUNCTION__ . '(): error', 500);
        }

    }

    /** 获取一个note的完成状态
     * @param $uid
     * @param $nid
     * @return array|bool|mixed
     */
    public function getNoteStatus($uid, $nid)
    {
        $status = $this->database->get($this->table,
            'status',
            [
                'AND' => [
                    'id' => $nid,
                    'uid' => $uid,
                    'visible[!]' => NOTE_VISIBLE_DELETED
                ]
            ]);

        return $status;

    }


    /** 获取一个note的置顶状态
     * @param $uid
     * @param $nid
     * @return array|bool|mixed
     */
    public function getNoteTop($uid, $nid)
    {
        $top = $this->database->get($this->table,
            'is_top',
            [
                'AND' => [
                    'id' => $nid,
                    'uid' => $uid,
                    'visible[!]' => NOTE_VISIBLE_DELETED
                ]
            ]);

        return $top;
    }

    /**  修改置顶状态
     * @param $uid
     * @param $nid
     * @param $is_top
     * @param null $edit_time
     * @throws Exception
     */
    public function updateFieldIsTop($uid, $nid, $is_top, $edit_time = null)
    {

        //秒级时间
        if ($edit_time === null) {
            $edit_time = date(DB_TIME_FORMAT);
        }

        $pdo = $this->database->update($this->table, [
            'is_top' => $is_top,
            'edit_time' => $edit_time,
            'total_edit[+]' => 1
        ], [
            'AND' => [
                'id' => $nid,
                'uid' => $uid,
                'visible[!]' => NOTE_VISIBLE_DELETED
            ]
        ]);

        $affected = $pdo->rowCount();
        if (!is_numeric($affected) || $affected != 1) {
            throw new Exception(__CLASS__ . '->' . __FUNCTION__ . '(): error', 500);
        }


    }


    /** 获取某状态下的note 已经按照置顶和对应时间排序
     * @param $uid
     * @param $status
     * @return array|bool
     * @throws Exception
     */
    public function getNotes($uid, $status)
    {
        //完成 用完成时间排序  未完成 用提交时间排序
        $time_order = $status == NOTE_STATUS_FINISHED ? 'finish_time' : 'commit_time';

        $data = $this->database->select($this->table,[
            'id',
            'text',
            'status',
            'is_top',
            'commit_time(time)'
        ],[
            'AND'=>[
                'uid'=>$uid,
                'status'=>$status,
                'visible[!]'=>NOTE_VISIBLE_DELETED
            ],
            'ORDER'=>[
                'is_top'=>'DESC',
                $time_order =>'ASC',
            ]
        ]);

        if(!is_array($data)){
            throw new Exception(__CLASS__ . '->' . __FUNCTION__ . '(): error', 500);
        }

        return $data;

    }
}