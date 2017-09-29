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

class FavController extends UserController
{
    /**
     * @return \think\response
     */
    public function index()
    {
        $title = '个人收藏- ' . config('web_site_title');
        return $this->fetch('', ['meta_title' => $title]);
    }
    
    /**
     * @return \think\response
     */
    public function songs()
    {
        $title = '音乐收藏 - ' . config('web_site_title');
        return $this->fetch('', ['meta_title' => $title]);
    }
    
    /**
     * @return \think\response
     */
    public function album()
    {
        $title = '专辑收藏 - ' . config('web_site_title');
        return $this->fetch('', ['meta_title' => $title]);
    }
    
    /**
     * @param Request $request
     * @return \think\response
     */
    public function artist(Request $request)
    {
        $title = '艺人收藏 - ' . config('web_site_title');
        return $this->fetch('', ['meta_title' => $title]);
    }
}
