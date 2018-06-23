<?php

/**
 * Created by PhpStorm.
 * User: haier
 * Date: 2018-6-18
 * Time: 12:04
 */
namespace stApp\dao;
use Medoo\Medoo;

class BaseDao
{
    protected $database;

    /**
     * BaseDao constructor.
     * @param $database
     */
    public function __construct()
    {
        $this->database =  new Medoo([
            'database_type' => DATABASE_TYPE,
            'database_name' => DATABASE_NAME,
            'server' => SERVER,
            'username' => USERNAME,
            'password' => PASSWORD,
            'port'=>PORT,
            'charset'=>CHARSET
        ]);;
    }


}