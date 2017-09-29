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

use app\common\model\AuthMusician;
use think\Request;

//音乐人控制器
class MusicianController extends UserController
{
    /**
     * 音乐人首页
     * @return \think\response
     */
    public function index()
    {
        $title = '音乐人 - ' . config('web_site_title');
        $this->assign('meta_title', $title);
        return $this->fetch();
    }

    /**
     * 音乐人个人主页
     * @return \think\response
     */
    public function read()
    {

    }

    /**
     * 认证首页
     * @return \think\response
     */
    public function auth()
    {
        $title = '认证音乐人 - ' . config('web_site_title');
        //判断是否正在审核中
        $model    = new AuthMusician();
        $musician = $model->where(['uid' => $this->user['uid']])->find();
    
        $this->assign('meta_title', $title);
        return $this->fetch('create', ['musician' => $musician]);
    }

    /**
     * 音乐人编辑
     * @return \think\response
     */
    public function edit()
    {
        abort('404', '页面不存在');
    }

    /**
     * 保存认证音乐人
     * @param Request $request
     * @return \think\response
     */
    public function save(Request $request)
    {
        $post        = $request->post();
        $post['uid'] = $this->user['uid'];
        $result      = $this->validate($post, 'AuthMusician');
        if (true !== $result) {
            return json([
                'code'  => 40030,
                'error' => $result,
                'token' => $request->token(),
            ]);
        }

        $model = new AuthMusician($post);
        if ($model->allowField(true)->save()) {
            return json([
                'code' => 0,
                'msg'  => '音乐人认证成功，请等待审核！',
                'url'  => url('user/Musician/auth'),
            ]);
        }
        return json([
            'code'  => 40500,
            'error'   => '音乐人认证失败，请稍后重试',
            'token' => $request->token(),
        ]);
    }

    /**
     * 更新音乐人
     * @param Request $request
     * @return \think\response
     */
    public function update(Request $request)
    {

    }
}
