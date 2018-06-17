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

//数据库容器
$container = $app->getContainer();
$container['database'] = function () {
    return new Medoo([
        'database_type' => DATABASE_TYPE,
        'database_name' => DATABASE_NAME,
        'server' => SERVER,
        'username' => USERNAME,
        'password' => PASSWORD,
        'charset' => CHARSET,
        'port' => PORT,
        'check_interval' => CHECK_INTERVAL
    ]);
};

//todo: 预留 调用common进行api记录监控


//路由
//login登录 创建token
$app->get('/login', function ($request, $response) {
    //创建token的post路由
    $code = isset($request->getParsedBody()["code"]) ? $request->getParsedBody()["code"] : null;
    $c_login = new stApp\controller\Login($code);
    setcookie(TOKEN_NAME, $c_login->getToken(), EXPIRES, PATH);
    return $response->withStatus($c_login->getStatus());
});




//获取轮播图
$app->get('/img/sliders', function ($request, $response) {
    $sliders = new zzxApp\controller\Sliders();
    return $response->withStatus($sliders->getStatus());
});

//通知相关路由组
$app->group('/notice', function () {
    $this->get('/lastest', function ($request, $response) {
        if (!$request->hasHeader('cookie') || !isset($_COOKIE[TOKEN_NAME])) {
            return $response->withStatus(412)->write('Precondition Failed');
        }
        $token = $_COOKIE[TOKEN_NAME];
        $mtn = new zzxApp\controller\MyLastestNotice($token, $this->database);
        return $response->withStatus($mtn->getStatus());
    });

    //我出租的物品通知
    $this->get('/thing', function ($request, $response) {
        if (!$request->hasHeader('cookie') || !isset($_COOKIE[TOKEN_NAME])) {
            return $response->withStatus(412)->write('Precondition Failed');
        }
        $token = $_COOKIE[TOKEN_NAME];
        $mtn = new zzxApp\controller\MyThingNotice($token, $this->database);
        return $response->withStatus($mtn->getStatus());
    });

    //我租用的物品通知
    $this->get('/renting', function ($request, $response) {
        if (!$request->hasHeader('cookie') || !isset($_COOKIE[TOKEN_NAME])) {
            return $response->withStatus(412)->write('Precondition Failed');
        }
        $token = $_COOKIE[TOKEN_NAME];
        $mtn = new zzxApp\controller\MyRentingNotice($token, $this->database);
        return $response->withStatus($mtn->getStatus());
    });

    //我的愿望通知
    $this->get('/wish', function ($request, $response) {
        if (!$request->hasHeader('cookie') || !isset($_COOKIE[TOKEN_NAME])) {
            return $response->withStatus(412)->write('Precondition Failed');
        }
        $token = $_COOKIE[TOKEN_NAME];
        $mtn = new zzxApp\controller\MyWishNotice($token, $this->database);
        return $response->withStatus($mtn->getStatus());
    });


});//notice

//物品相关路由组
$app->group('/thing', function () {
    //物品搜索
    $this->get('/search', function ($request, $response) {
        //获取get参数
        $content = isset($request->getQueryParams()["content"]) ? $request->getQueryParams()["content"] : null;
        $content = urldecode($content);
        $last_id = isset($request->getQueryParams()["last_id"]) ? $request->getQueryParams()["last_id"] : null;
        $cst = new zzxApp\controller\SearchThing($content, $last_id, $this->database);
        return $response->withStatus($cst->getStatus());

    });
    //获取分区物品列表
    $this->get('', function ($request, $response) {
        $last_id = isset($request->getQueryParams()["last_id"]) ? $request->getQueryParams()["last_id"] : null;
        $zonePm = isset($request->getQueryParams()["zone"]) ? $request->getQueryParams()["zone"] : null;
        $zonePm = urldecode($zonePm);
        //var_dump($zonePm);
        //多范围的拓展 要求传入数组
        unset($zone);
        $zone = array($zonePm);
        $cgt = new zzxApp\controller\GetThing($zone, $last_id, $this->database);
        return $response->withStatus($cgt->getStatus());

    });

    //获取物品详细内容 分为公共请求和私有请求
    $this->get('/{id}', function ($request, $response, array $args) {
        $id = (!empty($args['id'])) ? $args['id'] : null;
        // 实现由id获取物品详情 公共请求 和 token机制的私有请求
        if ($request->hasHeader('cookie') && !empty($_COOKIE[TOKEN_NAME])) {
            $token = $_COOKIE[TOKEN_NAME];
        } else {
            $token = null;
        }

        $cgtd = new zzxApp\controller\GetThingDetail($id, $this->database, $token);
        return $response->withStatus($cgtd->getStatus());
    });

    //发布物品 需要token的私有请求
    $this->post('', function ($request, $response) {
        if (!$request->hasHeader('cookie') || !isset($_COOKIE[TOKEN_NAME])) {
            return $response->withStatus(412)->write('Precondition Failed');
        } else {
            $token = $_COOKIE[TOKEN_NAME];
            //图片上传 可选

            $name = isset($request->getParsedBody()["name"]) ? $request->getParsedBody()["name"] : null;
            $zone = isset($request->getParsedBody()["zone"]) ? $request->getParsedBody()["zone"] : null;
            $instruction = isset($request->getParsedBody()["instruction"]) ? $request->getParsedBody()["instruction"] : null;
            $deposit = isset($request->getParsedBody()["deposit"]) ? $request->getParsedBody()["deposit"] : null;
            $rental = isset($request->getParsedBody()["rental"]) ? $request->getParsedBody()["rental"] : null;
            $address = isset($request->getParsedBody()["address"]) ? $request->getParsedBody()["address"] : null;
            $tenancy_begin = isset($request->getParsedBody()["tenancy_begin"]) ? $request->getParsedBody()["tenancy_begin"] : null;
            $tenancy_end = isset($request->getParsedBody()["tenancy_end"]) ? $request->getParsedBody()["tenancy_end"] : null;
            $uploadedFiles = $request->getUploadedFiles();
//                var_dump($uploadedFiles);
            $thingInfoAr = compact('name', 'zone', 'instruction', 'deposit', 'rental', 'address', 'tenancy_begin', 'tenancy_end');
            $cpt = new zzxApp\controller\PostThing($thingInfoAr, $token, $this->database);
            return $response->withStatus($cpt->getStatus());

        }
    });

    //编辑物品信息
    $this->post('/{tid}', function ($request, $response, array $args) {
        $tid = (!empty($args['tid'])) ? $args['tid'] : null;

        if (!$request->hasHeader('cookie') || !isset($_COOKIE[TOKEN_NAME])) {
            return $response->withStatus(412)->write('Precondition Failed');
        } else {
            $token = $_COOKIE[TOKEN_NAME];
            $name = isset($request->getParsedBody()["name"]) ? $request->getParsedBody()["name"] : null;
            $zone = isset($request->getParsedBody()["zone"]) ? $request->getParsedBody()["zone"] : null;
            //图片不允许变更
            $instruction = isset($request->getParsedBody()["instruction"]) ? $request->getParsedBody()["instruction"] : null;
            $deposit = isset($request->getParsedBody()["deposit"]) ? $request->getParsedBody()["deposit"] : null;
            $rental = isset($request->getParsedBody()["rental"]) ? $request->getParsedBody()["rental"] : null;
            $address = isset($request->getParsedBody()["address"]) ? $request->getParsedBody()["address"] : null;
            $tenancy_begin = isset($request->getParsedBody()["tenancy_begin"]) ? $request->getParsedBody()["tenancy_begin"] : null;
            $tenancy_end = isset($request->getParsedBody()["tenancy_end"]) ? $request->getParsedBody()["tenancy_end"] : null;

            $thingInfoAr = compact('name', 'zone', 'instruction', 'deposit', 'rental', 'address', 'tenancy_begin', 'tenancy_end');
            $cet = new zzxApp\controller\EditThing($tid, $thingInfoAr, $token, $this->database);
            return $response->withStatus($cet->getStatus());

        }
    });

    //收藏物品 物主不允许收藏自己的
    $this->post('/{tid}/fav', function ($request, $response, array $args) {
        $tid = (!empty($args['tid'])) ? $args['tid'] : null;
        if (!$request->hasHeader('cookie') || !isset($_COOKIE[TOKEN_NAME])) {
            return $response->withStatus(412)->write('Precondition Failed');
        } else {
            $token = $_COOKIE[TOKEN_NAME];
            $cft = new zzxApp\controller\FavThing($tid, $token, $this->database);
            return $response->withStatus($cft->getStatus());
        }
    });


    //用户自己删除下架自己的物品
    $this->post('/{tid}/del', function ($request, $response, array $args) {
        $tid = (!empty($args['tid'])) ? $args['tid'] : null;
        if (!$request->hasHeader('cookie') || !isset($_COOKIE[TOKEN_NAME])) {
            return $response->withStatus(412)->write('Precondition Failed');
        } else {
            $token = $_COOKIE[TOKEN_NAME];
            $cdt = new zzxApp\controller\DelThing($tid, $token, $this->database);
            return $response->withStatus($cdt->getStatus());
        }
    });


    //查看用户的物品列表 我发布的物品 已完成
    $this->get('/mine/', function ($request, $response) {
        if (!$request->hasHeader('cookie') || !isset($_COOKIE[TOKEN_NAME])) {
            return $response->withStatus(412)->write('Precondition Failed');
        } else {
            $token = $_COOKIE[TOKEN_NAME];
            $cmt = new zzxApp\controller\MyThing($token, $this->database);
            return $response->withStatus($cmt->getStatus());
        }

    });

    //查看用户的物品列表 我正在租用的 已完成
    $this->get('/renting/', function ($request, $response) {
        if (!$request->hasHeader('cookie') || !isset($_COOKIE[TOKEN_NAME])) {
            return $response->withStatus(412)->write('Precondition Failed');
        } else {
            $token = $_COOKIE[TOKEN_NAME];
            $cmr = new zzxApp\controller\MyRenting($token, $this->database);
            return $response->withStatus($cmr->getStatus());
        }
    });
    //查看用户的物品列表 我收藏的
    $this->get('/fav/', function ($request, $response) {
        if (!$request->hasHeader('cookie') || !isset($_COOKIE[TOKEN_NAME])) {
            return $response->withStatus(412)->write('Precondition Failed');
        } else {
            $token = $_COOKIE[TOKEN_NAME];
            $cmr = new zzxApp\controller\MyFavThing($token, $this->database);
            return $response->withStatus($cmr->getStatus());
        }
    });
    // 获取联系方式 物主--租借者
    $this->get('/{tid}/contact', function ($request, $response, array $args) {
        $tid = (!empty($args['tid'])) ? $args['tid'] : null;
        if (!$request->hasHeader('cookie') || !isset($_COOKIE[TOKEN_NAME])) {
            return $response->withStatus(412)->write('Precondition Failed');
        } else {
            $token = $_COOKIE[TOKEN_NAME];
            $ctc = new zzxApp\controller\ThingContact($tid, $token, $this->database);
            return $response->withStatus($ctc->getStatus());
        }
    });

    // 租用物品
    $this->post('/{tid}/rent', function ($request, $response, array $args) {
        $tid = (!empty($args['tid'])) ? $args['tid'] : null;
        if (!$request->hasHeader('cookie') || !isset($_COOKIE[TOKEN_NAME])) {
            return $response->withStatus(412)->write('Precondition Failed');
        }
        $token = $_COOKIE[TOKEN_NAME];
        $crt = new zzxApp\controller\RentThing($tid, $token, $this->database);
        return $response->withStatus($crt->getStatus());

    });

    // 提醒归还
    $this->post('/{tid}/remind', function ($request, $response, array $args) {
        $tid = (!empty($args['tid'])) ? $args['tid'] : null;
        if (!$request->hasHeader('cookie') || !isset($_COOKIE[TOKEN_NAME])) {
            return $response->withStatus(412)->write('Precondition Failed');
        }
        $token = $_COOKIE[TOKEN_NAME];
        $crdt = new zzxApp\controller\RemindThing($tid, $token, $this->database);
        return $response->withStatus($crdt->getStatus());

    });

    //确认归还
    $this->post('/{tid}/return', function ($request, $response, array $args) {
        $tid = (!empty($args['tid'])) ? $args['tid'] : null;
        if (!$request->hasHeader('cookie') || !isset($_COOKIE[TOKEN_NAME])) {
            return $response->withStatus(412)->write('Precondition Failed');
        }
        $token = $_COOKIE[TOKEN_NAME];
        $crnt = new zzxApp\controller\ConfirmReturn($tid, $token, $this->database);
        return $response->withStatus($crnt->getStatus());

    });


});//thing

//愿望相关路由组
$app->group('/wish', function () {
    $this->get('', function ($request, $response) {
        $last_id = isset($request->getQueryParams()["last_id"]) ? $request->getQueryParams()["last_id"] : null;
        //var_dump($zonePm);
        //多范围的拓展 要求传入数组
        $cgaw = new zzxApp\controller\GetWish($last_id, $this->database);
        return $response->withStatus($cgaw->getStatus());
    });

    //获取愿望详细内容 分为公共请求和私有请求
    $this->get('/{wid}', function ($request, $response, array $args) {
        $wid = (!empty($args['wid'])) ? $args['wid'] : null;
        if ($request->hasHeader('cookie') && !empty($_COOKIE[TOKEN_NAME])) {
            $token = $_COOKIE[TOKEN_NAME];
        } else {
            $token = null;
        }
        $cgtd = new zzxApp\controller\GetWishDetail($wid, $this->database, $token);
        return $response->withStatus($cgtd->getStatus());
    });

    //发布愿望
    $this->post('', function ($request, $response) {
        if (!$request->hasHeader('cookie') || !isset($_COOKIE[TOKEN_NAME])) {
            return $response->withStatus(412)->write('Precondition Failed');
        } else {
            $token = $_COOKIE[TOKEN_NAME];
            $name = isset($request->getParsedBody()["name"]) ? $request->getParsedBody()["name"] : null;
            $eval = isset($request->getParsedBody()["eval"]) ? $request->getParsedBody()["eval"] : null;
            $address = isset($request->getParsedBody()["address"]) ? $request->getParsedBody()["address"] : null;
            $instruction = isset($request->getParsedBody()["instruction"]) ? $request->getParsedBody()["instruction"] : null;
            $wishInfo = compact('name', 'eval', 'address', 'instruction');
            $cpw = new zzxApp\controller\PostWish($wishInfo, $token, $this->database);
            return $response->withStatus($cpw->getStatus());
        }

    });

    //删除愿望
    $this->post('/{wid}/del', function ($request, $response, array $args) {
        $wid = (!empty($args['wid'])) ? $args['wid'] : null;
        if (!$request->hasHeader('cookie') || !isset($_COOKIE[TOKEN_NAME])) {
            return $response->withStatus(412)->write('Precondition Failed');
        } else {
            $token = $_COOKIE[TOKEN_NAME];
            $cdw = new zzxApp\controller\DelWish($wid, $token, $this->database);
            return $response->withStatus($cdw->getStatus());
        }
    });
    //查看用户的物品列表 我发布的愿望
    $this->get('/mine/', function ($request, $response) {
        if (!$request->hasHeader('cookie') || !isset($_COOKIE[TOKEN_NAME])) {
            return $response->withStatus(412)->write('Precondition Failed');
        } else {
            $token = $_COOKIE[TOKEN_NAME];
            $cmw = new zzxApp\controller\MyWish($token, $this->database);
            return $response->withStatus($cmw->getStatus());
        }
    });

    // 获取联系方式 愿望主--领取者 愿望id token
    $this->get('/{wid}/contact', function ($request, $response, array $args) {
        $wid = (!empty($args['wid'])) ? $args['wid'] : null;
        if (!$request->hasHeader('cookie') || !isset($_COOKIE[TOKEN_NAME])) {
            return $response->withStatus(412)->write('Precondition Failed');
        } else {
            $token = $_COOKIE[TOKEN_NAME];
            $cwc = new zzxApp\controller\WishContact($wid, $token, $this->database);
            return $response->withStatus($cwc->getStatus());
        }
    });
    // 领取愿望
    $this->post('/{wid}/pick', function ($request, $response, array $args) {
        $wid = (!empty($args['wid'])) ? $args['wid'] : null;
        if (!$request->hasHeader('cookie') || !isset($_COOKIE[TOKEN_NAME])) {
            return $response->withStatus(412)->write('Precondition Failed');
        } else {
            $token = $_COOKIE[TOKEN_NAME];
            $cpw = new zzxApp\controller\PickWish($wid, $token, $this->database);
            return $response->withStatus($cpw->getStatus());

        }
    });


});//wish

//用户相关路由组
$app->group('/user', function () {
    //todo 把注册用户合并成一个类
    $this->post('/register', function ($request, $response) {
        $account = isset($request->getParsedBody()["account"]) ? $request->getParsedBody()["account"] : null;
        $pw = isset($request->getParsedBody()["pw"]) ? $request->getParsedBody()["pw"] : null;
        $stuNum = isset($request->getParsedBody()["stuNum"]) ? $request->getParsedBody()["stuNum"] : null;
        $name = isset($request->getParsedBody()["name"]) ? $request->getParsedBody()["name"] : null;
        $regInfo = compact('account', 'pw', 'name', 'stuNum');
        $creg = new zzxApp\controller\Register($this->database);
        $creg->postUser($regInfo);
        return $response->withStatus($creg->getStatus());
    });

    //todo 待检查 由sign发送短信 注册验证码
    $this->post('/register/sms', function ($request, $response) {
        $sign = isset($request->getParsedBody()["sign"]) ? $request->getParsedBody()["sign"] : null;
        $creg = new zzxApp\controller\Register($this->database);
        $creg->sentCode($sign);
        return $response->withStatus($creg->getStatus());
    });

    //凭借验证码激活用户
    $this->post('/activate', function ($request, $response) {
        $sign = isset($request->getParsedBody()["sign"]) ? $request->getParsedBody()["sign"] : null;
        $code = isset($request->getParsedBody()["code"]) ? $request->getParsedBody()["code"] : null;
        $cau = new zzxApp\controller\Register($this->database);
        $cau->activate($sign,$code);
        return $response->withStatus($cau->getStatus());
    });
    // 查看用户的个人信息 条件 未完善也能看
    $this->get('', function ($request, $response) {
        if (!$request->hasHeader('cookie') || empty($_COOKIE[TOKEN_NAME])) {
            return $response->withStatus(412)->write('Precondition Failed');
        }
        $token = $_COOKIE[TOKEN_NAME];
        $cgtd = new zzxApp\controller\GetUser($token, $this->database);
        return $response->withStatus($cgtd->getStatus());
    });

    // 编辑个人信息 只允许编辑微信号
    $this->post('/edit', function ($request, $response) {
        if (!$request->hasHeader('cookie') || empty($_COOKIE[TOKEN_NAME])) {
            return $response->withStatus(412)->write('Precondition Failed');
        }
        $token = $_COOKIE[TOKEN_NAME];
        $wx = isset($request->getParsedBody()["wx"]) ? $request->getParsedBody()["wx"] : null;
        $userPartInfo = compact('wx');
        //共用完善接口的 根据数组长度
        $cpui = new zzxApp\controller\PostUserInfo($userPartInfo, $token, $this->database);
        return $response->withStatus($cpui->getStatus());
    });

    //完善个人信息 postuserInfo  只能完善一次
    $this->post('', function ($request, $response) {
        if (!$request->hasHeader('cookie') || empty($_COOKIE[TOKEN_NAME])) {
            return $response->withStatus(412)->write('Precondition Failed');
        }
        $token = $_COOKIE[TOKEN_NAME];

        $college = isset($request->getParsedBody()["college"]) ? $request->getParsedBody()["college"] : null;
        $sex = isset($request->getParsedBody()["sex"]) ? $request->getParsedBody()["sex"] : null;
        $wx = isset($request->getParsedBody()["wx"]) ? $request->getParsedBody()["wx"] : null;
        $address = isset($request->getParsedBody()["address"]) ? $request->getParsedBody()["address"] : null;
        $card = isset($request->getParsedBody()["card"]) ? $request->getParsedBody()["card"] : null;

        $userPartInfo = compact('college', 'sex', 'wx', 'address', 'card');
        $cpui = new zzxApp\controller\PostUserInfo($userPartInfo, $token, $this->database);
        return $response->withStatus($cpui->getStatus());
    });

    // 忘记密码 查用户 返回sign=uid+signCode+codetime
    $this->post('/forget', function ($request, $response) {
        $account = isset($request->getParsedBody()["account"]) ? $request->getParsedBody()["account"] : null;
        $name = isset($request->getParsedBody()["name"]) ? $request->getParsedBody()["name"] : null;
        $stuNum = isset($request->getParsedBody()["stuNum"]) ? $request->getParsedBody()["stuNum"] : null;
        $resetInfo = compact('account', 'name', 'stuNum');
        $cr = new zzxApp\controller\Reset($this->database);
        $cr->getSign($resetInfo);
        return $response->withStatus($cr->getStatus());
    });

    //todo 待检查 由sign发送短信 改密验证码
    $this->post('/forget/sms', function ($request, $response) {
        $sign = isset($request->getParsedBody()["sign"]) ? $request->getParsedBody()["sign"] : null;
        $cr = new zzxApp\controller\Reset($this->database);
        $cr->sentCode($sign);
        return $response->withStatus($cr->getStatus());
    });

    // 由sign查code有效性 有效发放ticket
    $this->post('/code', function ($request, $response) {
        $code = isset($request->getParsedBody()["code"]) ? $request->getParsedBody()["code"] : null;
        $sign = isset($request->getParsedBody()["sign"]) ? $request->getParsedBody()["sign"] : null;
        $cr = new zzxApp\controller\Reset($this->database);
        $cr->getTicket($sign, $code);
        return $response->withStatus($cr->getStatus());
    });

    // 根据sign ticket pw 重置用户
    $this->post('/reset', function ($request, $response) {
        $pw = isset($request->getParsedBody()["pw"]) ? $request->getParsedBody()["pw"] : null;
        $ticket = isset($request->getParsedBody()["ticket"]) ? $request->getParsedBody()["ticket"] : null;
        $sign = isset($request->getParsedBody()["sign"]) ? $request->getParsedBody()["sign"] : null;
        $cr = new zzxApp\controller\Reset($this->database);
        $cr->changePw($ticket, $sign, $pw);
        return $response->withStatus($cr->getStatus());
    });
    // 登录后提交建议反馈
    $this->post('/feedback', function ($request, $response) {
        if (!$request->hasHeader('cookie') || empty($_COOKIE[TOKEN_NAME])) {
            return $response->withStatus(412)->write('Precondition Failed');
        }
        $token = $_COOKIE[TOKEN_NAME];
        $content = isset($request->getParsedBody()["content"]) ? $request->getParsedBody()["content"] : null;
        $cfb = new zzxApp\controller\Feedback($this->database);
        $cfb->userPost($token,$content);
        return $response->withStatus($cfb->getStatus());
    });

});//user

$app->group('/admin', function () {
    // 管理员登录
    $this->post('/login', function ($request, $response) {
        $account = isset($request->getParsedBody()["account"]) ? $request->getParsedBody()["account"] : null;
        $pw = isset($request->getParsedBody()["pw"]) ? $request->getParsedBody()["pw"] : null;
        $cct = new zzxApp\admin\controller\CreateToken($account, $pw, $this->database);
        setcookie(ADMIN_TOKEN_NAME, $cct->getToken(), ADMIN_EXPIRES, PATH);
        return $response->withStatus($cct->getStatus());
    });
    // 管理员展示物品
    $this->get('/thing', function ($request, $response) {
        if (!$request->hasHeader('cookie') || empty($_COOKIE[ADMIN_TOKEN_NAME])) {
            return $response->withStatus(412)->write('Precondition Failed');
        }
        $token = $_COOKIE[ADMIN_TOKEN_NAME];
        $act = new zzxApp\admin\controller\Thing($token,$this->database);
        $act->getAllThing();
        return $response->withStatus($act->getStatus());
    });
    // 管理员下架物品
    $this->post('/thing/{tid}/del', function ($request, $response,array $args) {
        $tid = (!empty($args['tid'])) ? $args['tid'] : null;
        if (!$request->hasHeader('cookie') || empty($_COOKIE[ADMIN_TOKEN_NAME])) {
            return $response->withStatus(412)->write('Precondition Failed');
        }
        $token = $_COOKIE[ADMIN_TOKEN_NAME];
        $act = new zzxApp\admin\controller\Thing($token,$this->database);
        $act->deleteThing($tid);
        return $response->withStatus($act->getStatus());
    });

});//admin

$app->run();

