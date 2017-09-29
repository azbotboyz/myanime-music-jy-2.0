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

use think\Cache;
use think\Request;
use app\common\model\Genre;

class GenreController extends AdminController
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index($id = 0)
    {
        $list = (new Genre())->getTree(intval($id));
        cookie('forward_url', $this->request->url());
        $this->assign('tree', $list);
        return $this->fetch();
    }

    /**
     * 显示分类列表树.
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
            $parent = Genre::get($pid);
            $this->assign('parent', $parent);
        }
        return $this->fetch();
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $post   = $request->post();
        $result = $this->validate($post, 'Genre');
        if (true !== $result) {
            // 验证失败 输出错误信息
            $this->error($result);
        }
        //halt(new Channel($post));
        $res = Genre::create($post);
        if ($res) {
            Cache::clear('genre');
            $this->success('音乐分类[' . $res->name . ']添加成功', cookie('forward_url'));
        } else {
            $this->error('音乐分类添加失败，请稍后重试');
        }
    }

    /**
     * 显示分类页.
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id = 0, $pid = 0)
    {
        if (!intval($id) || !$info = Genre::get($id)) {
            $this->error('音乐分类不存在');
        }

        if (intval($pid)) {
            $parent = Genre::get($pid);
            $this->assign('parent', $parent);
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
        $result = $this->validate($post, 'Genre.edit');
        if (true !== $result) {
            // 验证失败 输出错误信息
            $this->error($result);
        }
        $res = Genre::update($post);
        if ($res) {
            //更新后检测名称是否更新
            Cache::clear('genre');
            $this->success('音乐分类[' . $res->name . ']修改成功', Cookie('forward_url'));
        } else {
            $this->error('音乐分类修改失败，请稍后重试');
        }
    }

    /**
     * 删除指定资源
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        $genre = Genre::get($id);
        if (false == $genre) {
            $this->error('删除的音乐分类不存在！');
        }

        //判断该分类下有没有子分类，有则不允许删除
        $child = Genre::where('pid', $id)->field('id')->find();

        if (!empty($child)) {
            $this->error('请先删除该分类下的子分类');
        }

        //判断该分类下有没有内容
        $articleList = db('songs')->where('genre_id', $id)->field('id')->find();

        if (!empty($articleList)) {
            $this->error('请先删除/转移该分类下的歌曲（包含回收站）');
        }

        if ($genre->delete()) {
            Cache::clear('genre');
            $this->success('音乐分类成功删除！');
        } else {
            $this->error('音乐分类删除失败，请稍后重试！');
        }
    }

    /**
     * 更改分类状态
     * @param mixed $ids 要操作的数据ID
     * @param intger  $status Description
     * @return \think\Response
     */
    public function setStatus()
    {
        Cache::clear('genre');
        return $this->changeStatus('genre');
    }
}
