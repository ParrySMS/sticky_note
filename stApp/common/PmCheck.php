<?php
/**
 * Created by PhpStorm.
 * User: haier
 * Date: 2018-6-18
 * Time: 1:20
 */

namespace stApp\common;

use Exception;


class PmCheck
{

    /** 进行了参数的防注入异常检查
     * PmCheck constructor.
     * @param bool $is_encode
     */
    public function __construct($is_encode = false)
    {
        if ($is_encode == false) {
            //默认检查
            $_GET = $this->arrayCheck($_GET);
            $_POST = $this->arrayCheck($_POST);
        } else {
            //部分前端加密数据
            $get_body = $this->pmDecode($_GET);
            $post_body = $this->pmDecode($_POST);
            $_GET = $this->arrayCheck($get_body);
            $_POST = $this->arrayCheck($post_body);
        }
//            $_COOKIE = $this->array_check($_COOKIE);
//            $_FILES = $this->array_check($_FILES);
    }


    /** 前端传输的弱解密
     * @param $params
     * @return array|bool|string
     */
    public function pmDecode($params)
    {
        if (is_array($params)) {
            foreach ($params as $key => $p) {
                $params[$key] = base64_decode(base64_decode($p));
            }
        } else {
            $params = base64_decode(base64_decode($params));
        }
        return $params;
    }


    /** 字符串过滤函数
     * @param $str
     * @return mixed|string
     */
    protected function strCheck($str)
    {
        $str = trim($str);
        $str = strip_tags($str);
        //使用addslashes函数 添加反斜杠来处理
        $str = addslashes($str);
        //允许回车
//        $str = preg_replace('/\r\n/', ' ', $str);
        //过滤成全角
        $str = str_replace('<', '〈', $str);
        $str = str_replace('>', '〉', $str);
        $str = str_replace('_', '＿', $str);
        $str = str_replace('%', '％', $str);
        $str = str_replace('&', '\'&', $str);
        //html标签处理
        $str = htmlspecialchars($str);
//        var_dump($str);
        return $str;
    }


    /** 把字符串转化为对应的数字值
     * @param $val
     * @param bool $intval
     * @return int|null|string
     */
    protected function getNumeric($val, $intval = false)
    {
        if (!is_numeric($val)) {
            return null;
        }

        if ($intval == true) {
            $val = intval($val);
        }

        return $val + 0;
    }


// 数组遍历过滤函数
    protected function arrayCheck(&$array)
    {
        //如果是数组，遍历数组，递归调用
        if (is_array($array)) {
            foreach ($array as $k => $v) {
                $array [$k] = $this->arrayCheck($v);
            }
        } else if (is_string($array)) {
            $array = $this->strCheck($array);
        } else if (is_numeric($array)) {
            //不适用强制转换
//            $array = intval($array);
        }
        return $array;
    }


    /** 是否含有可疑字符
     * @param $sql_str
     * @return bool
     */
    protected function hasInject($sql_str)
    {
        $num = preg_match('/select|insert|update|delete|\'|\/\*|\*|\.\.\/|\.\/|UNION|into|load_file|outfile/', $sql_str);
        return ($num == 0) ? false : true;
    }

    /**删除反斜杠
     * @param $array
     * @return array|string
     */
    protected function stripslashesArray(&$array)
    {
        if (is_array($array)) {
            foreach ($array as $k => $v) {
                $array [$k] = stripslashes_array($v);
            }
        } else if (is_string($array)) {
            $array = stripslashes($array);
        }
        return $array;
    }

    /** 长度检查
     * @param $str
     * @param $min
     * @param $max
     * @return array|string
     * @throws Exception
     */
    protected function lenCheck($str, $min = 0, $max = 1000)
    {
        if (mb_strlen($str) < $min) {
            throw new Exception("STRLEN_ERROR: min $min byte, $str", 400);
            //die ("min: $min byte");
        } else if (mb_strlen($str) > $max) {
            throw new Exception("STRLEN_ERROR: max $max byte, $str", 400);
            //die ("max: $max byte");
        }
        return $this->stripslashesArray($str);
    }

    /** 检查是否为手机号
     * @param $phone
     * @return mixed
     * @throws Exception
     */
    protected function phoneCheck($phone)
    {
        if (!preg_match("/^1[34578]\d{9}$/", $phone)) {
            throw new Exception("PARAM_ERROR: invalid phone number,$phone", 400);
        }
        return $phone;
    }

    /** 是否微信号
     * @param $wx
     * @param int $wx_min
     * @param int $wx_max
     * @return mixed
     * @throws Exception
     */
    protected function wxCheck($wx, $wx_min = 6, $wx_max = 20)
    {
        if (!preg_match('/^[0-9a-zA-Z_-]{' . $wx_min . ',' . $wx_max . '}$/i', $wx)) {
            throw new Exception("PARAM_ERROR: WX not allowed", 400);
        }

        return $wx;
    }





}