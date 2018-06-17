<?php

/**
 * Created by PhpStorm.
 * User: haier
 * Date: 2017-8-19
 * Time: 1:45
 */

//token 加密密钥
define('DATA_AUTH_KEY','NG#snl*&28&%^gf&8grugt574^vf98');

//cookie 路径
define('PATH','/');
//token 过期时间
define('EXPIRES',time()+3600*24*30);
//验证码有效时间
define('CODE_TIME',5*60);
//改密ticket有效时间
define('TICKET_TIME',5*60);
//注册防轰炸的限制时间
define('REG_LIMIT_TIME',24*60*60);
//注册防轰炸在限制时间内的限制次数
define('REG_LIMIT_NUM',5);
//改密防轰炸的限制时间
define('RESET_LIMIT_TIME',60);
//改密防轰炸在限制时间内的限制次数
define('RESET_LIMIT_NUM',0);

