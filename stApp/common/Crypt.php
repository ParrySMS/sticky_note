<?php
/**
 * Created by PhpStorm.
 * User: haier
 * Date: 2017-11-6
 * Time: 11:44
 */



namespace stApp\common;

/** 若安全性要求增加 可以更换更复杂的加密算法
 * Interface Crypt
 * @package zzxApp\common
 */
interface Crypt
{
    function thinkEncrypt($data, $key, $expire);
    /*
     * 加密
     */
    function thinkDecrypt($token, $database);
    /*
     * 解密
     */


}