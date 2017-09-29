<?php
/**
 * JYmusic音乐管理系统
 *
 * @version     2.0
 * @author      战神~~巴蒂 <jyuucn@163.com>
 * @license     http://jyuu.cn [未经授权严禁私自出售，否者承担法律责任]
 * @copyright   2014 - 2017 JYmusic
 */
namespace app\user\controller;

use think\Request;
use app\common\model\Member;
use app\common\api\User as UsrApi;

class RelationController extends UserController
{
    /**
     * @return \think\response
     */
    public function index()
    {
        $title = '我的关系 - ' . config('web_site_title');
        return $this->fetch('fans', ['meta_title' => $title]);
    }
    
    /**
     * @return \think\response
     */
    public function fans()
    {
        $title = '我的粉丝 - ' . config('web_site_title');
        return $this->fetch('', ['meta_title' => $title]);
    }
    
    /**
     * @return \think\response
     */
    public function follow()
    {
        $title = '我的关注 - ' . config('web_site_title');
        return $this->fetch('', ['meta_title' => $title]);
    }
}
