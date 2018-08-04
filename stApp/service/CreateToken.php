<?php
/**
 * Created by PhpStorm.
 * User: haier
 * Date: 2018-6-18
 * Time: 11:55
 */

namespace stApp\service;


use stApp\common\Crypt;
use stApp\common\Http;
use stApp\common\ThinkCrypt;
use stApp\dao\User;
use Exception;
use stApp\model\Json;

class CreateToken extends BaseService
{
    private $user;
    private $crypt;

    /**
     * CreateToken constructor.
     */
    public function __construct()
    {
        $this->user = new User();
        $this->crypt = new ThinkCrypt();
    }


    /** 判断参数情况与新老用户，返回token与登录提示
     * @param $openid
     * @param null $info
     * @return int|string 返回0表示新用户 返回token说明老用户成功
     * @throws Exception
     */
    public function createToken($openid,$info = null ){

        if(empty($info)) {//默认老用户
            $userdata = $this->user->getUser($openid);
            $uid = $userdata['id'];
            $visible = $userdata['visible'];

            if ($uid == 0) { //新用户 需要更多用户信息
                return 0;
            }

            if($visible == 0){//黑名单用户
                throw new Exception(MSG_BLACK_USER,20040301);
            }
            //正常老用户
            $token = $this->getToken($uid,$openid);
        }else{//新用户
            $uid = $this->user->insertUser($info);
            $token = $this->getToken($uid,$openid);
        }

        $this->json = new Json('登录成功');
        return $token;
    }

    // 该方法弃用  首次进入 创建用户 改为不拆分
    private function getNewUserid($info){
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

        $openid = $info->openid;
        $nickname = $info->nickname;
        $sex = $info->sex;
        $province = $info->province;
        $city = $info->city;
        $country = $info->country;
        $headimgurl = $info->headimgurl;
        $privilege = $info->privilege;
        $unionid = $info->unionid;
       return $this->user->insertUser_multiPm($openid,$nickname,$sex,$province,$city,$country,$headimgurl,$privilege,$unionid);

    }


    /** 根据参数生成token
     * @param $uid
     * @param $openid
     * @return string
     */
    private function getToken($uid,$openid){
        $tokenStr = $this->crypt->getTokenStr($uid,$openid);
        $token = $this->crypt->thinkEncrypt($tokenStr);
        return $token;
    }

}