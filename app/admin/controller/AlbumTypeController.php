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
use app\common\model\AlbumType;

class AlbumTypeController extends AdminController
{
    /**
     * 显示资源列表
     * @return \think\Response
     */
    public function index()
    {
        $map = $this->getListMap('name');
        $list = $this->lists("album_type", $map);
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
        $result = $this->validate($post, 'AlbumType');

        if (true !== $result) {
            // 验证失败 输出错误信息
            $this->error($result);
        }
        $res = AlbumType::create($post);
        if ($res) {
            cache('album_type_lists', null);
            $this->success('[' . $res->name . ']Create success');
        } else {
            $this->error('The album type has failed. Please try again later');
        }
    }

    /**
     * 显示编辑资源表单页.
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        if (!intval($id) || !$info = AlbumType::get($id)) {
            $this->error('The album type does not exist');
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
        $result = $this->validate($post, 'AlbumType.edit');

        if (true !== $result) {
            // 验证失败 输出错误信息
            $this->error($result);
        }
        $res = AlbumType::update($post);
        if (AlbumType::update($post)) {
            cache('album_type_lists', null);
            $this->success('Album type[' . $res->name . ']Successfully modified', cookie('forward_url'));
        } else {
            $this->error('The album type has failed. Please try again later');
        }
    }

    /**
     * 删除指定资源
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        $info = AlbumType::get($id);
        if (false == $info) {
            $this->error('Deleted album type does not exist!');
        }

        if ($info->delete()) {
            cache('album_type_lists', null);
            $this->success('The album type was successfully deleted!');
        } else {
            $this->error('Album type deletion failed, please try again later!');
        }
    }

    /**
     * 更改专辑类型状态
     * @param mixed $ids 要操作的数据ID
     * @param intger  $status Description
     * @return \think\Response
     */
    public function setStatus()
    {
        cache('album_type_lists', null);
        return $this->changeStatus('AlbumType');
    }
}
