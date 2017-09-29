<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace app\admin\model;
use think\Model;

/**
 * 用户模型
 */

class Member extends Model {

    /**
     * 登录指定用户
     * @param  integer $uid 用户ID
     * @return boolean      ture-登录成功，false-登录失败
     */
    public function login($uid){
        /* 检测是否在当前应用注册 */
        $user = $this->get($uid)->data;

        if(!$user || 1 != $user['status']) {
            $this->error = '用户不存在或已被禁用！'; //应用级别禁用
            return false;
        }

        //记录行为
        //action_log('user_login', 'member', $uid, $uid);

        /* 登录用户 */
        $this->autoLogin($user);
        return true;
    }

    /**
     * 注销当前用户
     * @return void
     */
    public function logout(){
        session('user_auth', null);
        session('user_auth_sign', null);
    }

    /**
     * 自动登录用户
     * @param  array $user 用户信息数组
     */
    private function autoLogin($user){
        /* 更新登录信息 */
        $data = [
            'login'           => array('exp', '`login`+1'),
            'last_login_time' => time(),
            'last_login_ip'   => get_client_ip(1),
        ];
        $this->save($data,['uid'=>$user['uid']]);

        /* 记录登录SESSION和COOKIES */
        $auth = [
            'uid'             => $user['uid'],
            'username'        => $user['nickname'],
            'last_login_time' => $user['last_login_time'],
        ];

        session('user_auth', $auth);
        session('user_auth_sign', data_auth_sign($auth));

    }

    public function getNickName($uid){
        return $this->where(array('uid'=>(int)$uid))->value('nickname');
    }

    // birthday修改器
    protected function setBirthdayAttr($time){
        return strtotime($time);
    }

    //读取器
    protected function getBirthdayAttr($birthday){
        return date('Y-m-d H:i',$birthday);
    }

    protected function getRegTimeAttr($time){
        return date('Y-m-d H:i', $time);
    }

    protected function getlastLoginTimeAttr($time){
        return date('Y-m-d H:i', $time);
    }

    protected function getRegIpAttr($ip){
        return long2ip($ip);
    }

    protected function getlastLoginIpAttr($ip){
        return long2ip($ip);
    }

    public function getStatusAttr($status){
        $statusText = [-1=>'删除',0=>'禁用',1=>'正常',2=>'未激活'];
        return $statusText[$status];
    }


}
