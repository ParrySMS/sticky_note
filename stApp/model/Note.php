<?php
/**
 * Created by PhpStorm.
 * User: L
 * Date: 2018-8-9
 * Time: 15:43
 */

namespace stApp\model;


class Note
{
    public $nid;
    public $text;
    public $time;
    public $status;
    public $is_top;

    /**
     * Note constructor.
     * @param $nid
     * @param $text
     * @param $time
     * @param $status
     * @param $is_top
     */
    public function __construct($nid, $text, $time, $status=NOTE_STATUS_NOT_FINISH, $is_top =NOTE_NOT_TOP)
    {
        $this->nid = $nid;
        $this->text = $text;
        $this->time = $time;
        $this->status = $status;
        $this->is_top = $is_top;
    }


}