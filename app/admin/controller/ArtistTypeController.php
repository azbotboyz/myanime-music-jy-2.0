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
use app\common\model\ArtistType;

class ArtistTypeController extends AdminController
{
    /**
     * 显示资源列表
     * @return \think\Response
     */
    public function index()
    {
        $map = $this->getListMap('name');
        $list = $this->lists("artist_type", $map);
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
        $result = $this->validate($post, 'ArtistType');

        if (true !== $result) {
            // 验证失败 输出错误信息
            $this->error($result);
        }
        $res = ArtistType::create($post);
        if ($res) {
            cache('artist_type_lists', null);
            $this->success('[' . $res->name . ']创建成功');
        } else {
            $this->error('艺人类型添加失败，请稍后重试');
        }
    }

    /**
     * 显示编辑资源表单页.
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        if (!intval($id) || !$info = ArtistType::get($id)) {
            $this->error('艺人类型不存在');
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
        $result = $this->validate($post, 'ArtistType.edit');

        if (true !== $result) {
            // 验证失败 输出错误信息
            $this->error($result);
        }
        $res = ArtistType::update($post);
        if ($res) {
            cache('artist_type_lists', null);
            $this->success('艺人类型[' . $res->name . ']修改成功', cookie('forward_url'));
        } else {
            $this->error('艺人类型修改失败，请稍后重试');
        }
    }

    /**
     * 删除指定资源
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        $info = ArtistType::get($id);
        if (false == $info) {
            $this->error('删除的艺人类型不存在！');
        }

        if ($info->delete()) {
            cache('artist_type_lists', null);
            $this->success('艺人类型成功删除！');
        } else {
            $this->error('艺人类型删除失败，请稍后重试！');
        }
    }

    /**
     * 更改艺人类型状态
     * @param mixed $ids 要操作的数据ID
     * @param intger  $status Description
     * @return \think\Response
     */
    public function setStatus()
    {
        cache('artist_type_lists', null);
        return $this->changeStatus('ArtistType');
    }
}
