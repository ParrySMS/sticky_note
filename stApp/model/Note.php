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
     * @param array $option
     */
    public function __construct(array $option)
    {
        $this->nid = isset($option['id'])?$option['id']:null;
        $this->text = isset($option['text'])?$option['text']:null;
        $this->time = isset($option['time'])?$option['time']:null;
        $this->status = isset($option['status'])?$option['status']:NOTE_STATUS_UNFINISHED;
        $this->is_top = isset($option['is_top'])?$option['is_top']:NOTE_NOT_TOP;
    }


}