<?php
/**
 * JYmusic音乐管理系统
 *
 * @version     2.0
 * @author      战神~~巴蒂 [jyuucn@163.com]
 * @license     http://jyuu.cn [未经授权严禁私自出售，否者承担法律责任]
 * @copyright   JYmusic 2014 - 2017
 */

namespace app\admin\controller;

use think\Request;
use app\common\model\Member;
use app\common\model\MemberGroup;
use app\common\model\UcenterMember;
use app\common\api\User as UserApi;
use app\common\model\MemberGroupLink;

class UserController extends AdminController
{
    /**
     * 显示用户列表
     * @return \think\Response
     */
    public function index()
    {
        $map        = [];
        $textStatus = config('text_status');
        $keys       = trim($this->request->param('keys'));
        if (!empty($keys)) {
            if (is_numeric($keys)) {
                $map['a.id|m.nickname'] = ['like', '%' . $keys . '%'];
            } else {
                $map['m.nickname'] = ['like', '%' . (string) $keys . '%'];
            }
            $status = 'all';
        } else {
            $status = $this->request->param('status', 'success');
            $map['a.id'] = ['neq', 1];
        }

        $map['a.status'] = $textStatus[$status];

        $order = $this->request->param('order');
        if (!empty($order)) {
            $order = $this->request->param('field') . ' ' . $order;
        }

        $join = ['__MEMBER__ m', 'a.id = m.uid'];
        $list = $this->lists("ucenter_member", $map, $order, '', $join);
        cookie('forward_url', $this->request->url());
        return $this->fetch('', [
            '_list'  => $list,
            'status' => $status,
        ]);
    }

    /**
     * 显示创建资源表单页.
     * @return \think\Response
     */
    public function create()
    {
        return $this->fetch();
    }

    /**
     * 保存新建的资源
     * @param  \think\Request $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $post   = $request->post();
        $result = $this->validate($post, 'UcenterMember.create');
        if (true !== $result) {
            // 验证失败 输出错误信息
            $this->error($result);
        }
        //如果没有修改密码 删除密码字段
        if (empty($post['password'])) {
            unset($post['password']);
        }
        $member             = $post['member'];
        $member['nickname'] = $member['nickname'] ?: $post['username'];
        unset($post['member']);

        $result = $this->validate($member, 'Member');
        if (true !== $result) {
            // 验证失败 输出错误信息
            $this->error($result);
        }

        $user = UcenterMember::create($post);
        if ($user) {
            if ($user->member()->save($member)) {
                $this->success('user[' . $user->member->nickname . ']Create success', cookie('forward_url'));
            }
            $user->delete();
        }
        $this->error('User created failed, please try again later');
    }

    /**
     * 显示编辑资源表单页.
     * @param  int $id
     * @return \think\Response
     */
    public function edit($id)
    {
        if (!intval($id)) {
            $this->error('ID parameter is incorrect');
        }
        $info = UcenterMember::get($id, 'member');

        if (!$info) {
            $this->error('User does not exist');
        }
        return $this->fetch('create', [
            'info' => $info->toArray(),
            'userGroup' => (new MemberGroup)->getUserIn($info['id'])
        ]);
    }

    /**
     * 保存更新的资源
     * @param  \think\Request $request
     * @param  int            $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        $post   = $request->post();
        $result = $this->validate($post, 'UcenterMember.admin_edit');
        if (true !== $result) {
            // 验证失败 输出错误信息
            $this->error($result);
        }

        //如果没有修改密码 删除密码字段
        if (isset($post['password']) && empty($post['password'])) {
            unset($post['password']);
        }
        $member = $post['member'];
        unset($post['member']);
        $result = $this->validate($member, 'Member.edit');
        if (true !== $result) {
            // 验证失败 输出错误信息
            $this->error($result);
        }
        $user = UcenterMember::update($post);
        if ($user && $user->member()->update($member)) {
            $this->success('用户[' . $user->member->nickname . ']Successfully modified', cookie('forward_url'));
        }
        $this->error('User modification failed, please try again later');
    }

    /**
     * 删除指定资源
     * @return \think\Response
     */
    public function delete()
    {
        $ids = !empty($ids) ? $ids : $this->request->param('id/a');
        if (empty($ids)) {
            $this->error('Please select the data to be operated');
        }

        $map['id'] = ['in', $ids];
        if ($res = UcenterMember::where($map)->delete()) {
            $res = Member::where(['uid' => ['in', $ids]])->delete();
        }
        if ($res) {
            $this->success('User deleted successfully');
        } else {
            $this->error('User deletion failed');
        }
    }

    /**
     * 更改用户状态
     * @return void
     */
    public function setStatus()
    {
        $ids    = !empty($ids) ? $ids : $this->request->param('id/a');
        $status = !empty($status) ? $status : $this->request->param('status');

        if (empty($ids)) {
            $this->error('Please select the data to be operated');
        }
        $map['id'] = ['in', $ids];

        if (is_numeric($status)) {
            if ($res = UcenterMember::where('id', 'in', $ids)->setField('status', $status)) {
                $res = Member::where('uid', 'in', $ids)->setField('status', $status);
            }
            if ($res) {
                $this->success('User status changed successfully');
            } else {
                $this->error('User status changes are deleted');
            }
        }
        $this->error('Status change failed');
    }

    /**
     * 添加到用户组
     */
    public function togroup(Request $request, $uid = 0)
    {
        if($request->isPost()) {
            $post =  $request->post();
            $model = new MemberGroupLink;
            $lenTime = !empty($post['custom_len_time'])? $post['custom_len_time'] : $post['len_time'];
            $res = $model->addUserTo($post['uid'], $post['group_id'], $lenTime*34*3600 );
            if ($res) {
                clear_user_info($post['uid']);
                $this->success('Set up successfully');
            } else {
                $this->error('Setup failed');
            }
        } else {
            if (!intval($uid)) {
                $this->error('ID parameter is incorrect');
            }
            $info = UcenterMember::get($uid, 'member');

            if (!$info) {$this->error('User does not exist');}
            return $this->fetch('', [
                'info' => $info,
                'userGroup' => (new MemberGroup)->getUserIn($info['id'])
            ]);
        }
    }
    
    /**
     * 修改管理员资料
     * @return mixed
     */
    public function profile(Request $request)
    {
        $api = new UserApi();
        
        if ($request->isPost()) {
            $data = $request->post();
            
            if ($api->updateInfo($data, UID, $data['oldpassword'], true)){
                $this->success('Successfully modified');
            } else {
                $error = $api->getError();
                $error = !empty($error)? $error : 'Data failed!';
                $this->error($error);
            }
        
        } else {
            $info = $api->info(UID);
            return $this->fetch('', ['info' => $info ]);
        }
    }

    /**
     * 退出登录
     */
    public function logout()
    {
        //$this->success('退出成功！', url('login'));
        if (is_login()) {
            (new UserApi)->logout();
            $this->success('Exit succeed!', url('login'));
        } else {
            $this->redirect('login');
        }
    }
}
