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
use app\common\model\Server;

/**
 * 后台服务器组管理
 *
 * @package Channel
 */
class ServerController extends AdminController
{
    /**
     * 显示导航列表
     * @return \think\Response
     */
    public function index($id = 0)
    {
        $map = $this->getListMap('name');
        $list = $this->lists("server", $map);
        cookie('forward_url', $this->request->url());
        return $this->fetch('', ['_list' => $list]);
    }

    /**
     * 显示创建资源表单页.
     * @return \think\Response
     */
    public function create($pid = 0)
    {
        return $this->fetch();
    }

    /**
     * 保存新建的资源
     * @param \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $post   = $request->post();
        $result = $this->validate($post, 'server');

        if (true !== $result) {
            // 验证失败 输出错误信息
            $this->error($result);
        }
        $res = Server::create($post);
        if ($res) {
            cache('server_lists', null);
            $this->success('server[' . $res->name . ']Added successfully', cookie('forward_url'));
        } else {
            $this->error('Server added failed, please try again later');
        }
    }

    /**
     * 显示编辑资源表单页.
     * @param int  $id
     * @return \think\Response
     */
    public function edit($id = 0, $pid = 0)
    {
        if (!intval($id) || !$info = Server::get($id)) {
            $this->error('The server does not exist');
        }
        return $this->fetch('create', ['info' => $info]);
    }

    /**
     * 保存更新的资源
     * @param \think\Request $request
     * @param int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        $post   = $request->post();
        $result = $this->validate($post, 'Server.edit');

        if (true !== $result) {
            // 验证失败 输出错误信息
            $this->error($result);
        }
        $res = Server::update($post);
        if ($res) {
            cache('server_lists', null);
            $this->success('server[' . $res->name . ']Successfully modified', Cookie('forward_url'));
        } else {
            $this->error('Server modification failed. Please try again later');
        }
    }

    /**
     * 删除指定资源
     * @param int $id 要删除的数据ID
     * @return \think\Response
     */
    public function delete($id)
    {
        $channel = Server::get($id);
        if (false == $channel) {
            $this->error('Deleted server does not exist!');
        }

        if ($channel->delete()) {
            cache('server_lists', null);
            $this->success('The server was successfully deleted!');
        } else {
            $this->error('Server deleted failed, please try again later!');
        }
    }

    /**
     * 更改服务器状态
     * @param mixed $ids 要操作的数据ID
     * @param intger  $status Description
     * @return \think\Response
     */
    public function setStatus()
    {
        cache('server_lists', null);
        return $this->changeStatus('server');
    }
}
