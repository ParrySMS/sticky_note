<?php

/**
 * Created by PhpStorm.
 * User: haier
 * Date: 2018-6-17
 * Time: 21:30
 */

namespace stApp\service;

use \Exception;
use stApp\common\Http;

class WxRequest
{

    private $app_id;
    private $app_secret;
    public $http;
    const  WXSNS_URL = 'https://api.weixin.qq.com/sns/';


    /**
     * WxRequest constructor.
     */
    public function __construct($app_id = APPID, $app_secret = APPSECRET)
    {
        $this->app_id = $app_id;
        $this->app_secret = $app_secret;
        $this->http = new Http();
    }

    /**网页授权1 用code换去access_token包 内含openid
     * @param $code
     * @param string $appid
     * @param string $appsecret
     * @return null
     */
    public function getAccessToken($code)
    {

        //appid 和 appsecret在配置文件中
        //根据code获得Access Token 与 openid
        $access_token_url = $this::WXSNS_URL .'oauth2/access_token';
        $data = [
            'appid' => $this->app_id,
            'secret' => $this->app_secret,
            'code' => $code,
            'grant_type' => 'authorization_code'
        ];

        $access_token_json = $this->http->get($access_token_url,$data);
        $access_token_object = json_decode($access_token_json);
        //var_dump($access_token_array);
        if (isset($access_token_object->errmsg)) {
            $errmsg = $access_token_object->errmsg;
            $errcode = $access_token_object->errmsg;
            throw new Exception("wxsns unauthorized: $errcode -> $errmsg", 501);
        }
        return empty($access_token_object) ? null : $access_token_object;
            /**
             * 正常返回
            access_token	网页授权接口调用凭证,注意：此access_token与基础支持的access_token不同
            expires_in	access_token接口调用凭证超时时间，单位（秒）
            refresh_token	用户刷新access_token
            openid	用户唯一标识，请注意，在未关注公众号时，用户访问公众号的网页，也会产生一个用户和公众号唯一的OpenID
            scope	用户授权的作用域，使用逗号（,）分隔
             */
    }

    /** 授权$access_token刷新  不刷新则7200s过期
     * @param $access_token
     * @return mixed|null
     * @throws Exception
     */
    public function refresh_token($access_token){
        $refresh_url = $this::WXSNS_URL .'oauth2/refresh_token';
        $data = [
            'appid' => $this->app_id,
            'refresh_token' => $access_token,
            'grant_type' => 'refresh_token'
        ];
        $refresh_json = $this->http->get($refresh_url,$data);
        $refresh_object = json_decode($refresh_json);
        if (isset($refresh_object->errmsg)) {
            $errmsg = $refresh_object->errmsg;
            $errcode = $refresh_object->errcode;
            throw new Exception("wxsns unauthorized: $errcode -> $errmsg", 501);
        }
        return empty($refresh_object) ? null : $refresh_object;
        /**
         * 正常返回
        access_token	网页授权接口调用凭证,注意：此access_token与基础支持的access_token不同
        expires_in	access_token接口调用凭证超时时间，单位（秒）
        refresh_token	用户刷新access_token
        openid	用户唯一标识
        scope	用户授权的作用域，使用逗号（,）分隔
         */
    }


    /** 拉取用户信息
     * @param $access_token
     * @param $openid
     * @return mixed|null
     * @throws Exception
     *
     */
    public function getUserinfo($access_token,$openid){
        $userinfo_url = $this::WXSNS_URL.'userinfo';
        $data = [
            'access_token' => $access_token,
            'openid' => $openid,
            'lang' => 'zh_CN'
        ];
        $userinfo_json = $this->http->get($userinfo_url,$data);
        $userinfo_object = json_decode($userinfo_json);
        if (isset($userinfo_object->errmsg)) {
            $errmsg = $userinfo_object->errmsg;
            $errcode = $userinfo_object->errcode;
            throw new Exception("wxsns unauthorized: $errcode -> $errmsg", 501);
        }
        return empty($userinfo_object) ? null : $userinfo_object;
        /**   正常返回
        openid	用户的唯一标识
        nickname	用户昵称
        sex	用户的性别，值为1时是男性，值为2时是女性，值为0时是未知
        province	用户个人资料填写的省份
        city	普通用户个人资料填写的城市
        country	国家，如中国为CN
        headimgurl	用户头像，最后一个数值代表正方形头像大小（有0、46、64、96、132数值可选，0代表640*640正方形头像），用户没有头像时该项为空。若用户更换头像，原有头像URL将失效。
        privilege	用户特权信息，json 数组，如微信沃卡用户为（chinaunicom）
        unionid	只有在用户将公众号绑定到微信开放平台帐号后，才会出现该字段。
         */
    }
}