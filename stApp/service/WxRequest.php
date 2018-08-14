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
    const WX_EXPIRE = 7000;
    const  WXSNS_URL = 'https://api.weixin.qq.com/sns/';
    const JSSDK_PATH = './jssdk/';
    const JSAPI_TICKET_FILE = 'jsapi_ticket.php';
    const ACCESS_TOKEN_FILE = 'access_token.php';


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
    public function getAccessTokenOAuth2($code)
    {

        //appid 和 appsecret在配置文件中
        //根据code获得Access Token 与 openid
        $access_token_url = $this::WXSNS_URL . 'oauth2/access_token';
        $data = [
            'appid' => $this->app_id,
            'secret' => $this->app_secret,
            'code' => $code,
            'grant_type' => 'authorization_code'
        ];

        $access_token_json = $this->http->get($access_token_url, $data);
        $access_token_object = json_decode($access_token_json);
        if (!is_object($access_token_object)) {
            throw new Exception(__CLASS__ . __FUNCTION__ . '() error: json_decode', 500);
        }
        //var_dump($access_token_array);
        if (isset($access_token_object->errmsg)) {
            $errmsg = $access_token_object->errmsg;
            $errcode = $access_token_object->errmsg;
            throw new Exception("wxsns unauthorized: $errcode -> $errmsg", 501);
        }

        return empty($access_token_object) ? null : $access_token_object;
        /**
         * 正常返回
         * access_token    网页授权接口调用凭证,注意：此access_token与基础支持的access_token不同
         * expires_in    access_token接口调用凭证超时时间，单位（秒）
         * refresh_token    用户刷新access_token
         * openid    用户唯一标识，请注意，在未关注公众号时，用户访问公众号的网页，也会产生一个用户和公众号唯一的OpenID
         * scope    用户授权的作用域，使用逗号（,）分隔
         */
    }

    /** 授权$access_token刷新  不刷新则7200s过期
     * @param $access_token
     * @return mixed|null
     * @throws Exception
     */
    public function refreshToken($access_token)
    {
        $refresh_url = $this::WXSNS_URL . 'oauth2/refresh_token';
        $data = [
            'appid' => $this->app_id,
            'refresh_token' => $access_token,
            'grant_type' => 'refresh_token'
        ];
        $refresh_json = $this->http->get($refresh_url, $data);
        $refresh_object = json_decode($refresh_json);

        if (!is_object($refresh_object)) {
            throw new Exception(__CLASS__ . __FUNCTION__ . '() error: json_decode', 500);
        }

        if (isset($refresh_object->errmsg)) {
            $errmsg = $refresh_object->errmsg;
            $errcode = $refresh_object->errcode;
            throw new Exception("wxsns unauthorized: $errcode -> $errmsg", 501);
        }
        return empty($refresh_object) ? null : $refresh_object;
        /**
         * 正常返回
         * access_token    网页授权接口调用凭证,注意：此access_token与基础支持的access_token不同
         * expires_in    access_token接口调用凭证超时时间，单位（秒）
         * refresh_token    用户刷新access_token
         * openid    用户唯一标识
         * scope    用户授权的作用域，使用逗号（,）分隔
         */
    }


    /** 拉取用户信息
     * @param $access_token
     * @param $openid
     * @return mixed|null
     * @throws Exception
     *
     */
    public function getUserinfo($access_token, $openid)
    {
        $userinfo_url = $this::WXSNS_URL . 'userinfo';
        $data = [
            'access_token' => $access_token,
            'openid' => $openid,
            'lang' => 'zh_CN'
        ];
        $userinfo_json = $this->http->get($userinfo_url, $data);
        $userinfo_object = json_decode($userinfo_json);

        if (!is_object($userinfo_object)) {
            throw new Exception(__CLASS__ . __FUNCTION__ . '() error: json_decode', 500);
        }

        if (isset($userinfo_object->errmsg)) {
            $errmsg = $userinfo_object->errmsg;
            $errcode = $userinfo_object->errcode;
            throw new Exception("wxsns unauthorized: $errcode -> $errmsg", 501);
        }
        return empty($userinfo_object) ? null : $userinfo_object;
        /**   正常返回
         * openid    用户的唯一标识
         * nickname    用户昵称
         * sex    用户的性别，值为1时是男性，值为2时是女性，值为0时是未知
         * province    用户个人资料填写的省份
         * city    普通用户个人资料填写的城市
         * country    国家，如中国为CN
         * headimgurl    用户头像，最后一个数值代表正方形头像大小（有0、46、64、96、132数值可选，0代表640*640正方形头像），用户没有头像时该项为空。若用户更换头像，原有头像URL将失效。
         * privilege    用户特权信息，json 数组，如微信沃卡用户为（chinaunicom）
         * unionid    只有在用户将公众号绑定到微信开放平台帐号后，才会出现该字段。
         */
    }

    public function getAccessTokenGlobal()
    {
        // access_token 应该全局存储与更新，以下代码以写入到文件中做示例
        $file_data = json_decode($this->getPhpFile($this::JSSDK_PATH.$this::ACCESS_TOKEN_FILE));

        if ($file_data->expire_time < time()) {//如果过期了
            $url = 'https://api.weixin.qq.com/cgi-bin/token';
            $data = [
                'grant_type' => 'client_credential',
                'appid' => $this->app_id,
                'secret' => $this->app_secret
            ];
            //获取数据包对象
            $acc_object = json_decode($this->http->get($url, $data));

            if (!is_object($acc_object)) {
                throw new Exception(__CLASS__ . __FUNCTION__ . '() error: json_decode', 500);
            }

            if (isset($acc_object->errmsg)) {
                $errmsg = $acc_object->errmsg;
                $errcode = $acc_object->errcode;
                throw new Exception("wxsns unauthorized: $errcode -> $errmsg", 501);
            }
            //更新写入文件
            $file_data->expire_time = time() + $this::WX_EXPIRE;
            $file_data->access_token = $acc_object->access_token;
            $this->setPhpFile($this::JSSDK_PATH.$this::ACCESS_TOKEN_FILE, json_encode($file_data));

        }

        //没过期 直接读文件数据即可
        return $file_data->access_token;

    }

    public function getSignPackage($url)
    {

    }

    private function getJsApiTicket()
    {
        // jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
        $file_data = json_decode($this->getPhpFile($this::JSSDK_PATH . $this::JSAPI_TICKET_FILE));

        //如果过期了
        if ($file_data->expire_time < time()) {
            //因为同时更新的 所以应该是两个都过期
            $accessToken = $this->getAccessTokenGlobal();
            // 如果是企业号用以下 URL 获取 ticket
            // $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
            $url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket';
            $data = [
                'type'=>'jsapi',
                'access_token'=>$accessToken
            ];
            $ticket_obj = json_decode($this->http->get($url,$data));

            /**成功返回如下JSON：
             * {
            "errcode":0,
            "errmsg":"ok",
            "ticket":"bxLdikRXVbTPdHSM05e5u5sUoXNKd8-41ZO3MhKoyN5OfkWITDGgnr2fwJ0m9E8NYzWKVZvdVtaUgWvsdshFKA",
            "expires_in":7200
            }
             */

            //var_dump($res);
            $ticket = $res->ticket;
            if ($ticket) {
                $file_data->expire_time = time() + 7000;
                $file_data->jsapi_ticket = $ticket;
                $this->setPhpFile("../jssdk/jsapi_ticket.php", json_encode($file_data));
            }

        } else {
            $ticket = $file_data->jsapi_ticket;
        }
        return $ticket;
    }

    /** 读取文件
     * @param $filename
     * @return string
     */
    private function getPhpFile($filename)
    {
        return trim(substr(file_get_contents($filename), 15));
    }

    /** 写入文件
     * @param $filename
     * @param $content
     */
    private function setPhpFile($filename, $content)
    {
        $fp = fopen($filename, "w");
        fwrite($fp, "<?php exit();?>" . $content);
        fclose($fp);
    }
}