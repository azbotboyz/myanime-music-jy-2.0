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

class MessageController extends AdminController
{
    /**
     * 显示SEO规则列表
     * @return \think\Response
     */
    public function index()
    {
        $order = $this->request->param('order');
        if (!empty($order)) {
            $order = $this->request->param('field') . ' ' . $order;
        }

        $map['status'] = ['gt', -1];
        $list = $this->lists("message", $map, $order);
        Cookie('forward_url', $this->request->url());
        return $this->fetch('', ['_list' => $list]);
    }

    /**
     * 显示创建资源表单页
     * @return \think\Response
     */
    public function create()
    {
        //
    }

    /**
     * 保存新建的资源
     *
     * @param \think\Request $request
     *
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的资源
     *
     * @param \think\Request $request
     * @param int            $id
     *
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定资源
     *
     * @param int $id
     *
     * @return \think\Response
     */
    public function delete()
    {
        $ids = $this->request->param('id/a');
        if (empty($ids)) {
            $this->error('请选择要操作的数据');
        }
        $map = [];
        if (is_array($ids)) {
            $map['id'] = ['in', $ids];
        } elseif (is_numeric($ids)) {
            $map['id'] = $ids;
        }
        if (Message::where($map)->delete()) {
            $this->success('消息删除成功！');
        } else {
            $this->error('消息删除失败！');
        }
    }

    /**
     * 设置指定状态
     * @param  int $id
     * @return \think\response
     */
    public function setStatus()
    {
        return $this->changeStatus('article');
    }
}
