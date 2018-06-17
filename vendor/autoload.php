<?php

// autoload.php @generated by Composer

require_once __DIR__ . '/composer/autoload_real.php';

//配置文件常量
require '/config/params.php';
require '/config/msg.php';
require '/config/sms.php';
require '/config/img.php';
require '/config/admin.php';
require '/config/database_info.php';

//数据库微框架
require '/zzxApp/Medoo/Medoo.php';
spl_autoload_register(function ($classname) {
        require($classname . ".php");
});

return ComposerAutoloaderInitef1eb871add08a79c9c8fece2650a333::getLoader();


