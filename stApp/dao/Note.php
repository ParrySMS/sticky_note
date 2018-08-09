<?php
/**
 * Created by PhpStorm.
 * User: L
 * Date: 2018-8-9
 * Time: 14:51
 */

namespace stApp\dao;

use \Exception;
use stApp\common\PmCheck;

class Note extends BaseDao
{

    protected $table = PREFIX . '_note';

    /** 插入新信息并且获取id
     * @param $uid
     * @param $note_text
     * @param null $commit_time
     * @param int $status
     * @param int $is_top
     * @return int|mixed|string
     * @throws Exception
     */
    public function insert($uid,$note_text,$commit_time = null ,$status = NOTE_STATUS_NOT_FINISH,$is_top = NOTE_IS_TOP)
    {
        //秒级时间
        if($commit_time === null){
            $commit_time = date('Y-m-d H:i:s');
        }

        $pdo = $this->database->insert($this->table,[
            'uid'=>$uid,
            'text'=>$note_text,
            'status'=>$status,
            'is_top'=>$is_top,
            'commit_time'=>$commit_time,
            'finish_time'=>null,
            'edit_time'=>null,
            'total_edit'=>0,
            'visible'=>1
        ]);

        $id = $this->database->id();
        if (!is_numeric($id) || $id < 1) {
//          var_dump($this->database->error());
            throw new Exception(__CLASS__ .'->'. __FUNCTION__ . '(): error', 500);
        }

        return $id;
    }
}