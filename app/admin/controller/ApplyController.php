<?php
/**
 * JYmusic音乐管理系统
 *
 * @version     2.0
 * @author      战神~~巴蒂 <jyuucn@163.com>
 * @license     http://jyuu.cn/license [未经授权严禁私自出售，否者承担法律责任]
 * @copyright   2014 - 2017 JYmusic
 */

namespace app\admin\controller;

/**
 * 插件服务控制器
 * @author JYmusic <jyuucn@163.com>
 */
class ApplyController extends AdminController
{
    
    
    public function _empty($addons, $controller = '', $action = '')
    {
       //$this->re
        
        $path = $this->request->path();
        $path  =  explode('/', $path);
        
        if (!isset($path[1])) {
            $this->error(lang('addon cannot name or action'));
        }
   
        $addons = ucfirst(text_filter($path[1]));
        
        // 获取类的命名空间
        $controller = isset($path[2]) ? ucfirst(text_filter($path[2])) : 'Index';
        $action     = isset($path[3]) ? strtolower(text_filter($path[3])) : 'index';
        $class      = "\\addons\\{$addons}\\controller\\{$controller}Controller";
        $this->request->route([
            'addons' => $addons,
            'controller' => $controller,
            'action' => $action
        ]);
        
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
