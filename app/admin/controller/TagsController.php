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
use app\common\model\Tags;

class TagsController extends AdminController
{
    /**
     * 显示资源列表
     * @return \think\Response
     */
    public function index()
    {

        $map = $this->getListMap('name');
        $group= $this->request->param('group', '');
        
        if (!empty($group)) {
            $map['group'] = 'other' == $group ? 0 : $group;
        }
        $order = $this->request->param('order');
        if (!empty($order)) {
            $order = $this->request->param('field') . ' ' . $order;
        }
        $list = $this->lists("tags", $map, $order);
        Cookie('forward_url', $this->request->url());
        $groupList = config('tag_group');
        $groupList[0] = '其它';
        return $this->fetch('', [
            '_list' => $list,
            'group' => $group,
            'groupList' => $groupList,
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
        $res = Tags::create($post);
        if ($res) {
            cache('tags_tree_list', null);
            $this->success('label[' . $res->name . ']Create success');
        } else {
            $this->error('Tag added failed, please try again later');
        }
    }

    /**
     * 显示指定的资源
     * @param  int  $id
     * @return \think\Response
     */
    public function read()
    {
        if ($this->request->isPost()) {
            $map = $this->getSearchMap('name');
            $map['status'] = 1;
            $list = $this->lists("Tags", $map, 'id desc', 'id,name', '', 100);
            $return['code'] = 0;
            if ($list) {
                $return['code'] = 1;
            }
            $return['data'] = $list;
            return json($return, 200);
        }
        $list = $this->lists("Tags", ['status'=>1], 'id desc', 'id,name', '', 100);
        return $this->fetch('', ['_list' => $list]);
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        if (!intval($id) || !$info = Tags::get($id)) {
            $this->error('The label does not exist');
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
        $result = $this->validate($post, 'Tags.edit');
        if (true !== $result) {
            // 验证失败 输出错误信息
            $this->error($result);
        }
        $res = Tags::update($post);
        if ($res) {
            cache('tags_tree_list', null);
            $this->success('label[' . $res->name . ']Successfully modified', cookie('forward_url'));
        } else {
            $this->error('Tag revision failed, please try again later');
        }
    }

    /**
     * 删除指定资源
     * @param int $id 要删除的数据ID
     * @return \think\Response
     */
    public function delete($id)
    {
        $info = Tags::get($id);
        if (!$info) {
            $this->error('Deleted label does not exist!');
        }
        if ($info->delete()) {
            cache('tags_lists', null);
            $this->success('Tag successfully deleted!');
        } else {
            $this->error('Label deletion failed, please try again later!');
        }
    }

    /**
     * 更改标签状态
     * @param mixed $ids 要操作的数据ID
     * @param intger  $status Description
     * @return \think\Response
     */
    public function setStatus()
    {
        cache('tags_tree_list', null);
        return $this->changeStatus('Tags');
    }
}
