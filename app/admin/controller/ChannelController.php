<?php
/**
 * JYmusic音乐管理系统 PHP version 5.4+
 *
 * @author    战神~~巴蒂 <jyuucn@163.com>
 * @version   2.0.0
 * @license   http://jyuu.cn [未经授权严禁私自出售，否者承担法律责任]
 * @copyright 2014 - 2017 JYmusic
 */

namespace app\admin\controller;

use think\Request;
use app\common\model\Channel;

/**
 * 后台导航管理
 *
 * @package Channel
 */
class ChannelController extends AdminController
{
    /**
     * 显示导航列表
     * @param int $id
     * @return \think\Response
     */
    public function index($id = 0)
    {
        $list   = (new Channel())->getTree(intval($id));
        cookie('forward_url', $this->request->url());
        $this->assign('tree', $list);
        return $this->fetch();
    }

    /**
     * 显示导航列表树.
     * @return \think\Response
     */
    public function tree($tree)
    {
        $this->assign('tree', $tree);
        return $this->fetch('tree');
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create($pid = 0)
    {
        if (intval($pid) > 0) {
            $parent = Channel::get($pid);
            $this->assign('parent', $parent);
        }
        return $this->fetch();
    }

    /**
     * 保存新建的资源
     *
     * @param \think\Request  $request
     *
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $post   = $request->post();
        $result = $this->validate($post, 'Channel');
        if (true !== $result) {
            // 验证失败 输出错误信息
            $this->error($result);
        }
        //halt(new Channel($post));
        $res = Channel::create($post);
        if ($res) {
            $this->success('导航[' . $res->title . ']添加成功', Cookie('forward_url'));
        } else {
            $this->error('导航添加失败，请稍后重试');
        }
    }

    /**
     * 显示编辑资源表单页.
     * @param int  $id
     * @return \think\Response
     */
    public function edit($id = 0 , $pid = 0)
    {
        $info = Channel::get($id);
        if (!intval($id) || !$info) {
            $this->error('导航不存在');
        }

        if (intval($pid)) {
            $parent = Channel::get($pid);
            $this->assign('parent', $parent);
        }
        return $this->fetch('create', ['info'=>$info]);
    }

    /**
     * 保存更新的资源
     *
     * @param \think\Request $request
     * @param int  $id
     *
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        $post   = $request->post();
        $result = $this->validate($post, 'Channel');
        if (true !== $result) {
            // 验证失败 输出错误信息
            $this->error($result);
        }
        $res = Channel::update($post);
        if ($res) {
            $this->success('导航[' . $res->title . ']修改成功', Cookie('forward_url'));
        } else {
            $this->error('导航修改失败，请稍后重试');
        }
    }

    /**
     * 删除指定资源
     *
     * @param int $id 要删除的数据ID
     *
     * @return \think\Response
     */
    public function delete($id)
    {
        $channel  =   Channel::get($id);
        if (false == $channel) {
            $this->error('删除的导航不存在！');
        }

        if ($channel->delete()) {
            $this->success('导航成功删除！');
        } else {
            $this->error('导航删除失败，请稍后重试！');
        }
    }

    /**
     * 更改导航状态
     * @param mixed $ids 要操作的数据ID
     * @param intger  $status Description
     * @return \think\Response
     */
    public function setStatus()
    {
        return $this->changeStatus('channel');
    }
}
