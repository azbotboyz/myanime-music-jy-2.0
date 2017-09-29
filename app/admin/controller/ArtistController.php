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
use app\common\model\Artist;

class ArtistController extends AdminController
{
    /**
     * 显示资源列表
     * @return \think\Response
     */
    public function index()
    {

        $type = $this->request->param('_type');
        if ($type == 'query') {
            return $this->select();
        }

        $map = $this->getListMap('name');
        $type = $this->request->param('type', '');
        if (!empty($type)) {
            $map['type_id'] = 'other' == $type ? 0 : $type;
        }

        $order = $this->request->param('order');
        if (!empty($order)) {
            $order = $this->request->param('field') . ' ' . $order;
        }

        $list = $this->lists("artist", $map, $order);
        cookie('forward_url', $this->request->url());
        return $this->fetch('', ['_list' => $list, 'type' => $type]);
    }

    /**
     * 显示创建资源表单页.
     * @return \think\Response
     */
    private function select()
    {
        $map['status'] = 1;
        $field         = 'id,name';
        $list          = $this->lists("artist", $map, 'id desc', $field, 100);
        if ($list) {
            $return['code'] = 1;
            $return['data'] = $list;
        } else {
            $return['code'] = 0;
        }
        return json($return, 200);
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
        $result = $this->validate($post, 'Artist');

        if (true !== $result) {
            // 验证失败 输出错误信息
            $this->error($result);
        }
        $res = Artist::create($post);
        if ($res) {
            cache('Artist_lists', null);
            $this->success('艺人[' . $res->name . ']添加成功');
        } else {
            $this->error('艺人添加失败，请稍后重试');
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
            $list = $this->lists("artist", $map, 'id desc', 'id,name', '', 100);
            $return['code'] = 0;
            if ($list) {
                $return['code'] = 1;
            }
            $return['data'] = $list;
            return json($return, 200);
        }
        $list = $this->lists("artist", ['status'=>1], 'id desc', 'id,name', '', 100);
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
        if (!intval($id) || !$info = Artist::get($id)) {
            $this->error('艺人不存在');
        }
        return $this->fetch('create', ['info' => $info->getData()]);
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        $post   = $request->post();
        $result = $this->validate($post, 'Artist.edit');

        if (true !== $result) {
            // 验证失败 输出错误信息
            $this->error($result);
        }
        $res = Artist::update($post);
        if ($res) {
            cache('Artist_lists', null);
            $this->success('艺人[' . $res->name . ']修改成功', cookie('forward_url'));
        } else {
            $this->error('艺人修改失败，请稍后重试');
        }
    }

    /**
     * 删除指定资源
     * @param int $id 要删除的数据ID
     * @return \think\Response
     */
    public function delete($id)
    {
        $Artist = Artist::get($id);
        if (false == $Artist) {
            $this->error('删除的艺人不存在！');
        }

        if ($Artist->delete()) {
            cache('Artist_lists', null);
            $this->success('艺人成功删除！');
        } else {
            $this->error('艺人删除失败，请稍后重试！');
        }
    }

    /**
     * 更改艺人状态
     * @param mixed $ids 要操作的数据ID
     * @param intger  $status Description
     * @return \think\Response
     */
    public function setStatus()
    {
        cache('Artist_lists', null);
        return $this->changeStatus('Artist');
    }
}
