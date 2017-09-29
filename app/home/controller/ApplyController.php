<?php
/**
 * JYmusic音乐管理系统
 *
 * @version     2.0
 * @author      战神~~巴蒂 <jyuucn@163.com>
 * @license     http://jyuu.cn/license [未经授权严禁私自出售，否者承担法律责任]
 * @copyright   2014 - 2017 JYmusic
 */

namespace app\home\controller;

use think\Controller;

/**
 * 插件服务控制器
 * @author JYmusic <jyuucn@163.com>
 */
class ApplyController extends Controller
{

    public function index($addons, $controller = '', $action = '')
    {
        if (empty($addons)) {
            $this->error(lang('addon cannot name or action'));
        }
        $addons = ucfirst(text_filter($addons));

        // 获取类的命名空间
        $controller = !empty($controller) ? ucfirst(text_filter($controller)) : 'Index';
        $action     = !empty($action) ? strtolower(text_filter($action)) : 'index';
        $class      = "\\addons\\{$addons}\\controller\\{$controller}Controller";
        if (class_exists($class)) {
            $model = new $class();
            if ($model === false) {
                $this->error(lang('addon init fail'));
            }
            // 调用操作
            return call_user_func([$model, $action]);
        } else {
            $this->error(lang('Controller Class Not Exists'));
        }
    }
}
