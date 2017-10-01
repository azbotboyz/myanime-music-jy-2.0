<?php
/**
 * JYmusic音乐管理系统
 *
 * @version     2.0
 * @author      战神~~巴蒂 [jyuucn@163.com]
 * @license     http://jyuu.cn [未经授权严禁私自出售，否者承担法律责任]
 * @copyright   2014 - 2017 JYmusic
 */

namespace app\admin\controller;

use think\Request;
use app\common\model\MemberGroup;

class UserGroupController extends AdminController
{
    /**
     * 显示资源列表
     * @return \think\Response
     */
    public function index()
    {
        $map = $this->getListMap('name');
        $list = $this->lists("member_group", $map);
        cookie('forward_url', $this->request->url());
        return $this->fetch('', ['_list' => $list]);
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
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $post   = $request->post();
        $result = $this->validate($post, 'Tags');

        if (true !== $result) {
            // 验证失败 输出错误信息
            $this->error($result);
        }
        $res = MemberGroup::create($post);
        if ($res) {
            cache('user_group_lists', null);
            $this->success('user group[' . $res->name . ']Create success');
        } else {
            $this->error('User group added failed, please try again later');
        }
    }

    /**
     * 显示编辑资源表单页.
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        if (!intval($id) || !$info = MemberGroup::get($id)) {
            $this->error('The user group does not exist');
        }
        return $this->fetch('create', ['info' => $info]);
    }

    /**
     * 保存更新的资源
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        $post   = $request->post();
        $result = $this->validate($post, 'MemberGroup.edit');

        if (true !== $result) {
            // 验证失败 输出错误信息
            $this->error($result);
        }
        $res = MemberGroup::update($post);
        if ($res) {
            cache('user_group_lists', null);
            $this->success('user group[' . $res->name . ']Successfully modified', cookie('forward_url'));
        } else {
            $this->error('User group failed to change, please try again later');
        }
    }

    /**
     * 删除指定资源
     * @param int $id 要删除的数据ID
     * @return \think\Response
     */
    public function delete($id)
    {
        if (1 == $id) {
            $this->error('The current user group is not allowed to delete!');
        }

        $model = MemberGroup::get($id);
        if (false == $model) {
            $this->error('Deleted user group does not exist!');
        }

        if ($model->delete()) {
            cache('member_group_lists', null);
            $this->success('User group deleted successfully!');
        } else {
            $this->error('User group deleted failed, please try again later!');
        }
    }

    /**
     * 更改用户组状态
     * @param mixed $ids 要操作的数据ID
     * @param intger  $status Description
     * @return \think\Response
     */
    public function setStatus()
    {
        cache('member_group_lists', null);
        return $this->changeStatus('MemberGroup');
    }
}
