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
    $c_login = new \stApp\controller\Login($code);
    setcookie(TOKEN_NAME, $c_login->getToken(), EXPIRES, PATH);
    return $response->withStatus($c_login->getStatus());
});

//处理笔记内容
$app->group('/note',function () {

    //插入新数据
    $this->post('', function ($request, $response) {
        $text = isset($request->getParsedBody()['text']) ? $request->getParsedBody()["text"] : null;
        $c_note = new \stApp\controller\PostNote($text);
        return $response->withStatus($c_note->getStatus());
    });

    //勾选已完成
    $this->post('/finished/{nid}', function ($request, $response, array $args) {
        $nid = isset($args['nid']) ? $args['nid'] : null;
        $c_note = new \stApp\controller\EditNote();
        $c_note->editStatus($nid, NOTE_STATUS_FINISHED);
        return $response->withStatus($c_note->getStatus());

    });

    //勾选未完成
    $this->post('/unfinished/{nid}', function ($request, $response, array $args) {
        $nid = isset($args['nid']) ? $args['nid'] : null;
        $c_note = new \stApp\controller\EditNote();
        $c_note->editStatus($nid, NOTE_STATUS_UNFINISHED);
        return $response->withStatus($c_note->getStatus());
    });

    //置顶 top置顶note
    $this->post('/top/{nid}', function ($request, $response, array $args) {
        $nid = isset($args['nid']) ? $args['nid'] : null;
        $c_note = new \stApp\controller\EditNote();
        $c_note->top($nid, NOTE_IS_TOP);
        return $response->withStatus($c_note->getStatus());
    });

    //取消置顶 common普通note
    $this->post('/common/{nid}', function ($request, $response, array $args) {
        $nid = isset($args['nid']) ? $args['nid'] : null;
        $c_note = new \stApp\controller\EditNote();
        $c_note->top($nid, NOTE_NOT_TOP);
        return $response->withStatus($c_note->getStatus());
    });

    //todo 删除


});

$app->run();

