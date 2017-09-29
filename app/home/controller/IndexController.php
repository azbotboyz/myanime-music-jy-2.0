<?php
/**
 * JYmusic音乐管理系统 PHP version 5.4+
 *
 * @version     2.0
 * @author      战神~~巴蒂 <jyuucn@163.com>
 * @license     http://jyuu.cn/license [未经授权严禁私自出售，否者承担法律责任]
 * @copyright   2014 - 2017 JYmusic
 */

namespace app\home\controller;

use think\Request;

class IndexController extends HomeController
{

    /**
     * 首页展示
     * @return \think\Response
     */
    public function index()
    {
        return $this->fetch();
    }
   
    public function miss(Request $request)
    {
        $path = $request->path();
        if (empty($path)) {
            return $this->fetch('index');
        }
        
        $tpl = rtrim($request->path(), '/');
        $suffix = config('template.view_suffix')?: 'html';
        $tplPath = config('template.view_path') . str_replace('/', config('template.view_depr'), $tpl) . '.' . $suffix;
 
        if (is_file($tplPath)) {
            return $this->fetch($tplPath);
        }
        abort(500, '页面不存在');
    }
}
