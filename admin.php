<?php
/**
 * JYmusic音乐管理系统 PHP version 5.4+   应用入口文件 
 *
 * @version     2.0
 * @author      战神~~巴蒂 [jyuucn@163.com]
 * @license     LICENSE: http://jyuu.cn/license [未经授权严禁私自出售，否者承担法律责任]
 * @copyright   2014 - 2017 by JYmusic
 */
if (version_compare(PHP_VERSION, '5.4.0', '<')){
    die('require PHP > 5.4.0 !');
}

// 检测是否安装
if (!is_file(__DIR__ . '/config/database.php')) {
    header('Location: ./install.php');
    exit;
}

// 定义应用目录
define('APP_PATH', __DIR__ . '/app/');

define('RUNTIME_PATH', __DIR__ . '/storage/runtime/');

// 加载框架基础文件
require __DIR__ . '/core/base.php';

// 绑定当前入口文件到admin模块
\think\Route::bind('admin');

// 关闭admin模块的路由
\think\App::route(false);

// 执行应用
\think\App::run()->send();
