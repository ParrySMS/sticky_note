<?php

require './vendor/autoload.php';

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Medoo\Medoo;

//跨域设置 上线后应关闭
// 指定允许其他域名访问
header('Access-Control-Allow-Origin:*');
header("Access-Control-Allow-Credentials:true");
header("Access-Control-Allow-Methods:POST,GET,DELETE");
header("Content-type: text/html; charset=utf-8");
// 响应头设置
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

//登录
$app->post('/login', function (Request $request, Response $response) {
    $code = isset($request->getParsedBody()['code']) ? $request->getParsedBody()['code'] : null;
    $c_login = new \stApp\controller\Login($code);
    setcookie(TOKEN_NAME, $c_login->getToken(), EXPIRES, PATH);
    return $response->withStatus($c_login->getStatus());
});

//获取jssdk配置信息
$app->post('/jssdk',function (Request $request, Response $response) {
    $url = isset($request->getParsedBody()['url']) ? $request->getParsedBody()['url'] : null;
    $c_jssdk = new \stApp\controller\JSSDK($url);
    return $response->withStatus($c_jssdk->getStatus());
});

/** 处理笔记内容路由组
 *  获取全部 插入 勾选 置顶 删除 编辑
 */
$app->group('/note',function () {

    //获取首页的全部用户note
    $this->get('', function ($request, $response) {
        $c_note = new \stApp\controller\GetNote();
        $c_note->init();
        return $response->withStatus($c_note->getStatus());
    });

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

    //删除
    $this->post('/delete/{nid}', function ($request, $response, array $args) {
        $nid = isset($args['nid']) ? $args['nid'] : null;
        $c_note = new \stApp\controller\EditNote();
        $c_note->delete($nid);
        return $response->withStatus($c_note->getStatus());
    });

    // 使用delete方法简化的路由
    $this->delete('/{nid}', function ($request, $response, array $args) {
        $nid = isset($args['nid']) ? $args['nid'] : null;
        $c_note = new \stApp\controller\EditNote();
        $c_note->delete($nid);
        return $response->withStatus($c_note->getStatus());
    });

    //编辑text
    $this->post('/{nid}', function ($request, $response, array $args) {
        $text = isset($request->getParsedBody()['text']) ? $request->getParsedBody()["text"] : null;
        $nid = isset($args['nid']) ? $args['nid'] : null;
        $c_note = new \stApp\controller\EditNote();
        $c_note->edit($nid,$text);
        return $response->withStatus($c_note->getStatus());
    });
});



$app->run();

