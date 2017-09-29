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

use think\Cookie;
use app\common\api\User;
use app\common\api\Notice;
use app\common\model\MemberGroup;
use app\common\controller\BaseController;

//user 模块公共控制器
class UserController extends BaseController
{
    protected $user;

    public function _initialize()
    {
        parent::_initialize();

        //检测登录
        $uid = is_login();
        $api = new User();
        if (!$uid) {
            if (Cookie::has('JYUUID')) {
                $uid = jy_decrypt(Cookie::get('JYUUID'));
            }

            if (intval($uid) && $api->autoLogin($uid)) {
                define('UID', $uid);
            } else {
                $this->error('您还没有登录，请先登录！', url('user/Auth/login'));
            }
        } else {
            define('UID', $uid);
        }

        $this->user = $api->info($uid);
        //检测用户组
        $this->checkGroup();
        
        // 是否是超级管理员
        if (!defined('IS_ROOT')) {
            define('IS_ROOT', is_administrator());
        }
        $this->assign('user', $this->user);
        $this->assign('login_user', $this->user);
    }
    
    /**
     * 检测用户组
     */
    final function checkGroup()
    {
        $endTime = $this->user['group']['end_time'];
        if (($this->user['group']['id'] > 1) && ($endTime < time())) {
            //用户组到期移除用户组
            $this->user['group'] = (new MemberGroup)->removeUserIn($this->user['uid']);
            clear_user_info($this->user['uid']);
            $notice = (new Notice())->to($this->user['uid'])->title('用户组到期通知');
            $content = '你的所在用户组【'.$this->user['group']['name'].'】';
            $content .= '已于【'. time_format($endTime).'】到期';
            $notice->content($content)->send();
        }
    }
}
