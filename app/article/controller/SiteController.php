<?php
/**
 * JYmusic音乐管理系统 PHP version 5.4+ [未经授权严禁私自出售，否者承担法律责任]
 *
 * @version     2.0
 * @author      战神~~巴蒂 [jyuucn@163.com]
 * @license     LICENSE: http://jyuu.cn/license
 * @copyright   2014 - 2017 by JYmusic
 */

namespace app\article\controller;

use app\common\model\Site;

/**
 * 前台统一控制器
 */
class SiteController extends ArticleController
{
    public function index()
    {
        abort('404', 'The page does not exist');
        //return $this->fetch();
    }
    
    /**
     * @param mixed $name
     * @return \think\Response
     */
    public function read($type = '', $name = '')
    {
        $info = [];
        $map['status'] = 1;
        $map['appname'] = text_filter($type);
        if ((int)$name) {
            $map['id'] = $name;
            $info = Site::get($map);
        } elseif (ctype_alnum($name)) {
            $map['name'] = $name;
            $info = Site::get($map);
        }
        
        if (empty($info)) {
            abort(404,'The page you are viewing does not exist!');
        }
        
        $info = $info->parseData();
        $this->seoMeta($info);
        return $this->fetch('detail', ['data' => $info]);
    
    }

}
