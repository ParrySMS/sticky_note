<?php

require './vendor/autoload.php';

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Medoo\Medoo;

//跨域设置 上线后应关闭
header("Access-Control-Allow-Credentials:true");
header("Access-Control-Allow-Methods:POST,GET");
header("Content-type: text/html; charset=utf-8");
date_default_timezone_set("Asia/Shanghai");

//todo:debug模式 记得上线前关掉
$config = [
    'settings' => [
        'displayErrorDetails' => true
    ],
];

$app = new \Slim\App($config);
//自动遍历参数集
$pm_check = new \stApp\common\PmCheck();

//路由区域

//login登录 创建token
$app->post('/login', function ($request, $response) {
    $code = isset($request->getParsedBody()["code"]) ? $request->getParsedBody()["code"] : null;
    $c_login = new stApp\controller\Login($code);
    setcookie(TOKEN_NAME, $c_login->getToken(), EXPIRES, PATH);
    return $response->withStatus($c_login->getStatus());
});

//处理笔记内容
$app->group('/note',function ($request, $response) {



    $this->post('', function ($request, $response,$token) {
        //接收数据
        $note = isset($request->getParsedBody()["note"]) ? $request->getParsedBody()["note"] : null;
        $c_note = //todo 实现插入的控制器



    });


});




$app->run();

