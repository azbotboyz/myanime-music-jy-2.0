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

//user 模块公共控制器
class SettingController extends UserController
{
    /**
     * @return \think\response
     */
	public function index(){
		$title = '设置资料- '. config('web_site_title');
		return $this->fetch('', ['meta_title' => $title]);
    }
    
    /**
     * @return \think\response
     */
	public function avatar(){
		$title = '设置头像- '. config('web_site_title');
        return $this->fetch('', ['meta_title' => $title]);
	}
    
    /**
     * @return \think\response
     */
	public function vip(){
		$title = '购买VIP- '. config('web_site_title');
		return $this->fetch('', ['meta_title' => $title]);
	}
    
    /**
     * @param Request $request
     * @return \think\response|\think\response\Json
     */
	public function update(Request $request)
    {
        if (!allow_send_set(UID, 'profile')) {
            return $this->retErr(40014);
        }
        
        $post   = $request->post();
        $post['uid'] = UID;
        $result = $this->validate($post, 'Member.edit');
    
        if (true !== $result) {
            // 验证失败 输出错误信息
            return $this->retErr(40004, $result);
        }

        $user = new Member();
        // 过滤post数组中的非数据表字段数据
        $res = $user->allowField(true)->save($post,['uid' => UID]);
        if ($res) {
            clear_user_info(UID);
            set_user_send(UID, 'profile');
            return $this->retSucc('信息修改成功');
        } else {
            return $this->retErr(40500, '信息修改失败，请稍后重试');
        }
    }
    
    /**
     * @param Request $request
     * @return \think\response
     */
    public function changePwd(Request $request)
    {
        if (!allow_send_set(UID, 'password')) {
            $this->retErr(40014);
        }
        
        $post   = $request->post();
        
        $api = new UsrApi();
        $result = $api->updateInfo($post, UID, $post['oldpassword'], true);
        if (!$result) {
            // 验证失败 输出错误信息
            $error = $api->getError();
            $error = !empty($error) ? $error : '密码修改失败，请稍后重试';
            return $this->retErr(40004, $error);
        } else {
            set_user_send(UID, 'password');
            return $this->retSucc('密码修改成功');
        }
    }
}
