<?php
/**
 * JYmusic音乐管理系统-听客项目 PHP version 5.6+
 *
 * @version   2.0
 * @author    战神~~巴蒂 <jyuucn@163.com>
 * @license   http://jyuu.cn/license [未经授权严禁私自出售，否者承担法律责任]
 * @copyright 2014 - 2017 JYmusic
 */

namespace app\admin\Controller;

use think\Config;
use think\Controller;
use app\common\api\User;
use think\captcha\Captcha;

/**
 * 后台登录控制器
 */
class LoginController extends Controller
{
    /**
     * 后台用户登录
     * @return \think\Response
     */
    public function index()
    {
        if (is_login()) {
            $this->redirect('index/index');
        }

        $this->assign('meta_title', '管理员登录');
        return $this->fetch();
    }

    /**
     * 后台用户登录
     * @param string $username 用户名
     * @param string $password 用户密码
     * @return void
     */
    public function access()
    {
        $post   = $this->request->post();
        $result = $this->validate($post, 'UcenterMember.admin_login');
        if (true !== $result) {
            // 验证失败 输出错误信息
            $this->error($result);
        }

        /* 调用API登录接口登录 */
        $api = new User();
        if ($api->login($post['username'], $post['password'])) { //登录成功
            $this->success('登录成功！', url('index/index'));
        }
        $this->error($api->getError());
    }

    public function captcha()
    {
        $captcha = new Captcha((array)Config::get('captcha'));
        return $captcha->entry();
    }
}
