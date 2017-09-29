<?php
/**
 * JYmusic音乐管理系统 PHP version 5.4+   应用入口文件
 *
 * @version     2.0
 * @author      战神~~巴蒂 <jyuucn@163.com>
 * @license     LICENSE: http://jyuu.cn/license [未经授权严禁私自出售，否者承担法律责任]
 * @copyright   2014 - 2017 by JYmusic
 */

if (version_compare(PHP_VERSION, '5.4.0', '<')) {
    die('require PHP > 5.4.0 !');
}

// 检测是否安装
if (!is_file(__DIR__ . '/config/database.php')) {
    header('Location: ./install.php');
    exit;
}

// [ 应用入口文件 ]
define('APP_PATH', __DIR__ . '/app/');

define('RUNTIME_PATH', __DIR__ . '/storage/runtime/');

// 加载框架基础文件
require __DIR__ . '/core/base.php';

//加载视图配置文件
$viewConf = require __DIR__ . '/config/view.php';

if ($viewConf['use_wap_view'] && \think\Request::instance()->isMobile()) {
    define('THEM_TYPE', 'wap');
} else {
    define('THEM_TYPE', 'web');
}

// 执行应用
\think\App::run()->send();
