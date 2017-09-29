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
use app\common\model\Album;

class AlbumController extends AdminController
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

        $list = $this->lists("album", $map, $order);
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
        $list          = $this->lists("album", $map, 'id desc', $field, '',  100);
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
        $result = $this->validate($post, 'Album');

        if (true !== $result) {
            // 验证失败 输出错误信息
            $this->error($result);
        }
        $res = Album::create($post);
        if ($res) {
            cache('album_lists', null);
            $this->success('专辑[' . $res->name . ']创建成功');
        } else {
            $this->error('专辑添加失败，请稍后重试');
        }
    }

    /**
     * 显示指定的资源
     * @return \think\Response
     */
    public function read()
    {
        if ($this->request->isPost()) {
            $map = $this->getSearchMap('name');
            $map['status'] = 1;
            $list = $this->lists("album", $map, 'id desc', 'id,name', '', 100);
            $return['code'] = 0;
            if ($list) {
                $return['code'] = 1;
            }
            $return['data'] = $list;
            return json($return, 200);
        }

        $list = $this->lists("album", ['status'=>1], 'id desc', 'id,name', '', 100);
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
        if (!intval($id) || !$info = Album::get($id)) {
            $this->error('专辑不存在');
        }
        
        return $this->fetch('create', ['info' => $info->getData()]);
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
        $result = $this->validate($post, 'Album.edit');

        if (true !== $result) {
            // 验证失败 输出错误信息
            $this->error($result);
        }
        $res = Album::update($post);
        if ($res) {
            cache('album_lists', null);
            $this->success(
                '专辑[' . $res->name . ']修改成功',
                cookie('forward_url')
            );
        } else {
            $this->error('专辑修改失败，请稍后重试');
        }
    }

    /**
     * 删除指定资源
     * @param int $id 要删除的数据ID
     * @return \think\Response
     */
    public function delete($id)
    {
        
        $Album = Album::get($id);
        if (false == $Album) {
            $this->error('删除的专辑不存在！');
        }

        //判断该分类下有没有内容
        $songsList = db('songs')->where('album_id', 'in', $id)
            ->limit(99)
            ->field('id')
            ->select();
        if (!empty($songsList)) {
            $this->error('请先删除/转移专辑下的歌曲（包含回收站）');
        }

        if ($Album->delete()) {
            cache('album_lists', null);
            $this->success('专辑成功删除！');
        } else {
            $this->error('专辑删除失败，请稍后重试！');
        }
    }

    /**
     * 更改专辑状态
     * @return \think\Response
     */
    public function setStatus()
    {
        cache('album_lists', null);
        return $this->changeStatus('Album');
    }
}
