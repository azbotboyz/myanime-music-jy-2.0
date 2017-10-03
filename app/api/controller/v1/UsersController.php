<?php

/**
 * JYmusic音乐管理系统
 *
 * @version   2.0
 * @author    战神~~巴蒂 <jyuucn@163.com>
 * @license   http://jyuu.cn [未经授权严禁私自出售，否者承担法律责任]
 * @copyright 2014 - 2017 JYmusic
 */

namespace app\api\controller\v1;

use think\Request;
use app\common\model\Member;
use app\common\api\User as UserApi;

class UsersController extends ApiController
{
    /**
     * 显示资源列表
     * @return \think\Response
     */
    public function index($uid = 0)
    {
        $userInfo = [];
        //如果uid 为0读取当前用户
        if (!$uid = intval($uid)) {
            $uid = is_login();
        }
        if ($uid) {
            $userInfo = get_user_info($uid);
        }
        return $this->resJson($userInfo);
    }

    /**
     * 显示当前登录用户
     * @return \think\Response
     */
    public function active()
    {
        $uid = is_login();
        //检测自动登录
        if (!$uid && $autoUid = cookie('JYUUID')) {
            $uid = jy_decrypt($autoUid);
        }
        if (intval($uid)) {
            $model = new Member();
            return json([
                'code' => 0,
                'msg'  => 'Has logged',
                'uid'  => $uid,
                'result' => $model->parseData(get_user_info($uid))
            ], 200);
        }
        return json(['code' => 40004,'error'  => 'Not logged in'], 200);
    }

    public function login(Request $request)
    {
        if (!is_login()) {
            $data = $request->post();
            $api = new UserApi();
            if ($uid = $api->login($data['username'], $data['password'])) {
                if ($request->post('autologin') == 'on') {
                    $api->remember($uid);
                }
                return json([
                    'code' => 0,
                    'msg'  => 'login successful',
                    'url' => url('/user/account')
                ]);
            }
            $error = $api->getError();
            $error = !empty($error) ? $error : 'Login failed Please try again later!';
            return json(['code' => 40004, 'error'  => $error]);
        }
        return json(['code' => 40005, 'error'  => 'Signed in, please do not log in again']);
    }

    public function logout()
    {
        $api = new UserApi();
        if ($api->logout()) {
            return json([
                'code' => 0,
                'msg'  => 'Successfully log out',
                'url' => url('/')
            ]);
        }
        return json([
            'code' => 40004,
            'error'  => 'Exit login failed'
        ]);
    }

    /**
     * 注册一个新用户
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function save(Request $request)
    {
        if (!is_login()) {
            $data = $request->post();
            $api = new UserApi();
            $uid = $api->register($data);
            if ( $uid && $api->autoLogin($uid)) {
                return json([
                    'code' => 0,
                    'msg'  => 'registration success',
                    'url' => url('/user/account')
                ]);
            }
            $error = $api->getError();
            $error = !empty($error) ? $error : 'Registration failed Please try again later!';
            return json(['code' => 40004, 'error'  => $error]);
        }
        return json(['code' => 40005, 'error'  => 'Has been logged in and can not sign up for new users']);
    }
    
    /**
     * 显示指定的资源
     * @param  int $id
     * @return \think\Response
     */
    public function read($uid = 0)
    {
        $userInfo = [];
        //如果uid 为0读取当前用户
        if ((int)$uid) {
            $userInfo = get_user_info($uid);
        }
        return $this->resJson($userInfo);
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request $request
     * @param  int            $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        // $id2 = $request->param('id');
        // halt($id);
        // return json($result);
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request $request
     * @param  int            $id
     * @return \think\Response
     */
    public function delete(Request $request, $id)
    {
        //halt($id);
        //return json($result);
    }
}
