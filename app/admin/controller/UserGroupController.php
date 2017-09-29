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
            $this->success('用户组[' . $res->name . ']创建成功');
        } else {
            $this->error('用户组添加失败，请稍后重试');
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
            $this->error('用户组不存在');
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
            $this->success('用户组[' . $res->name . ']修改成功', cookie('forward_url'));
        } else {
            $this->error('用户组修改失败，请稍后重试');
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
            $this->error('当前用户组不允许删除！');
        }

        $model = MemberGroup::get($id);
        if (false == $model) {
            $this->error('删除的用户组不存在！');
        }

        if ($model->delete()) {
            cache('member_group_lists', null);
            $this->success('用户组成功删除！');
        } else {
            $this->error('用户组删除失败，请稍后重试！');
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
