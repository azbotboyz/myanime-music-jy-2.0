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

use think\cache;
use think\Request;
use app\common\controller\BaseController;

/**
 * Auth
 */
class IndexController extends BaseController
{
    protected $beforeActionList = [
        'user' => ['only' => 'index'],
    ];

    protected $user;

    protected $loginUser = null;

    /**
     * 用户页面
     * @return \think\Response
     */
    public function index()
    {
        $title = '会员广场 - ' . config('web_site_title');
        return $this->fetch('', ['meta_title' => $title]);
    }

    /**
     * 当前用户个人页面
     * @return \think\Response
     */
    public function read($uid = 0)
    {
        //获取用户信息
        $this->initUser($uid);
        $this->seoMeta($this->user);
        return $this->fetch('');
    }

    /**
     * 显示当前用户音乐
     * @return \think\Response
     */
    public function music($uid = 0)
    {
        $this->initUser($uid);
        $this->seoMeta($this->user, 'read');
        return $this->fetch('');
    }

    /**
     * 用户专辑页面
     * @param Request $request
     * @return \think\response
     */
    public function album($uid = 0)
    {
        $this->initUser($uid);
        $this->seoMeta($this->user, 'read');
        return $this->fetch('');
    }

    /**
     * 用户粉丝页面
     * @param  Request $request [description]
     * @return \think\Response
     */
    public function fans($uid = 0)
    {
        $this->initUser($uid);
        $this->seoMeta($this->user, 'read');
        return $this->fetch('');
    }

    /**
     * 用户访客页面
     * @param  Request $request [description]
     * @return \think\Response
     */
    public function visitor($uid = 0)
    {
        $this->initUser($uid);
        $this->seoMeta($this->user, 'read');
        return $this->fetch('');
    }

    /**
     * 初始化用户
     * @return [type] [description]
     */
    protected function initUser($uid = 0)
    {
        if (!$uid = intval($uid)) {
            $this->error('你查看的用户不存在！');
        }
    
        define('UID', is_login());
    
        $this->user = get_user_info($uid);
    
        if (UID > 0 && $uid != UID) {
            $this->loginUser = get_user_info(UID);
            $this->setVisitor();
        } else {
            $this->loginUser = $this->user;
        }

        $this->assign('login_user', $this->loginUser);
        $this->assign('user', $this->user);
    }

    protected function setVisitor()
    {
        //记录访问  缓存
        $visitor = [
            'uid'        => $this->loginUser['uid'],
            'avatar'     => $this->loginUser['avatar'],
            'sex'        => $this->loginUser['sex'],
            'nickname'   => $this->loginUser['nickname'],
            'visit_time' => time(),
        ];
        
        $visitors = file_cache('visitor_' .  $this->user['uid']);
        
        $visitors[$this->loginUser['uid']] = $visitor;
        
        if (count($visitors) >= 100) {
            //删除最后一个
            array_pop($visitors);
        }

        //缓存7天 7天无任何访问者 清除
        file_cache('visitor_' . $this->user['uid'], $visitors);
    }
}
