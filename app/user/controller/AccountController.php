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

use app\common\model\Member;
use app\common\model\MemberGroup;
use app\common\model\MemberGroupLink;

//个人账户控制器

class AccountController extends UserController
{
    /**
     * 个人账户首页
     * @return \think\Response
     */
    public function index()
    {
        $title = '我的账户- ' . config('web_site_title');
        $this->assign('meta_title', $title);
        return $this->fetch();
    }
    
    /**
     * 充值界面
     * @return \think\Response
     */
    public function charge()
    {
        $title = '账户充值 - ' . config('web_site_title');
        $this->assign('meta_title', $title);
        return $this->fetch();
    }
    
    /**
     * 购买用户组
     * @return \think\Response
     */
    public function upgrade()
    {
        $title = '升级用户组 - ' . config('web_site_title');
        $this->assign('meta_title', $title);
        $list = (new MemberGroup())->getList();
        unset($list[1]);
        return $this->fetch('', ['list' => $list]);
    }
    
    /**
     * 更新用户组
     * @return \think\Response
     */
    public function updateGroup()
    {
        $post = $this->request->post();
        $id = $post['pay_group'];
        
        if (!intval($id)) {
            return $this->retErr(40030, '用户组选择不正确');
        }
        $list = (new MemberGroup())->getList();
        
        if (!isset($list[$id])) {
            return $this->retErr(40030, '所选用户组不存在');
        }
        
        $group = $list[$id];
        $payType = $post['pay_type'];
        if (!isset($group['rule'][$payType])) {
            return $this->retErr(40030, '所选用户组购买类型不正确');
        }
        
        $needCoin = $group['rule'][$payType];
        if ($this->user['coin'] < $needCoin) {
            return $this->retErr(40030, '抱歉金币不足无法购买');
        }
        
        $times = [
            'year_coin' => 365*86400,
            'half_year_coin' => 183*86400,
            'month_coin' => 30*86400
        ];
        
        $linKModel = new MemberGroupLink();
        
        $uid = $this->user['uid'];
        if($linKModel->addUserTo($uid, $id, $times[$payType])) {
            //扣除用户金币
            (new Member())->where('uid', $uid)->setDec('coin', $needCoin);
            clear_user_info($uid);
            cache('user_group_lists', null);
            return $this->retSucc('购买成功');
        
        } else {
            return $this->retErr(40500, '操作失败请稍后重试');
        }
    }
}
