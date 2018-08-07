<?php
/**
 * Created by PhpStorm.
 * User: L
 * Date: 2018-8-7
 * Time: 11:58
 */

namespace stApp\controller;
use \Exception;
use stApp\common\LogicCheck;

class postNote extends BaseController
{
    private $check;

    /**
     * postNote constructor.
     */
    public function __construct()
    {
        try {
            $this->check = new LogicCheck();
            $token_info = $this->check->token_info;
            


        }catch (Exception $e){
            $this->error($e);
        }
    }
}