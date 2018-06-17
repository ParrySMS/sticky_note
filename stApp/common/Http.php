<?php
/**
 * Created by PhpStorm.
 * User: haier
 * Date: 2018-6-17
 * Time: 22:46
 */

namespace stApp\common;

use Exception;

class Http
{

    /**
     * 模拟get进行url请求
     * @param string $url
     * @param array $post_data
     */
    public function get($url, array $data = [])
    {
        $ch = curl_init();
        /* 设置验证方式 */
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Accept:text/plain;charset=utf-8',
            'Content-Type:application/x-www-form-urlencoded',
            'charset=utf-8'
        ));
        /* 设置返回结果为流 */
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        /* 设置超时时间*/
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        /* 设置通信方式 */
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        if (!empty($data)) {
            $url = $url . '?' . http_build_query($data);
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        $result = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        if ($result === false) {
            throw new Exception('Curl error: ' . $error, 501);
//            echo 'Curl error: ' . $error;
//            return null;
        } else {
            return $result;
        }
    }

    /**
     * 模拟post进行url请求
     * @param string $url
     * @param array $post_data
     */
    function post($url, array $data = [])
    {
        $ch = curl_init();
        /* 设置验证方式 */
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Accept:text/plain;charset=utf-8',
            'Content-Type:application/x-www-form-urlencoded',
            'charset=utf-8'
        ));
        /* 设置返回结果为流 */
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        /* 设置超时时间*/
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        /* 设置通信方式 */
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_URL, $url);
        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        }
        $result = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        if ($result === false) {
            throw new Exception('Curl error: ' . $error, 501);
//            return null;
        } else {
            return $result;
        }
    }


}