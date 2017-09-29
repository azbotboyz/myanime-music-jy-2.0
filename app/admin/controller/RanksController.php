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
use app\common\model\Ranks;

class RanksController extends AdminController
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
        
        $list = $this->lists("Ranks", $map, 'sort asc');
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
        $result = $this->validate($post, 'Ranks');

        if (true !== $result) {
            // 验证失败 输出错误信息
            $this->error($result);
        }
        $res = Ranks::create($post);
        if ($res) {
            cache('ranks_lists', null);
            //写入榜单规则
            $res->rule    = '{"rank_id" : "'.$res->id.'"}';
            $res->save();
            $this->success('榜单[' . $res->name . ']创建成功');
        } else {
            $this->error('榜单添加失败，请稍后重试');
        }
    }

    /**
     * 显示编辑资源表单页.
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        if (!intval($id) || !$info = Ranks::get($id)) {
            $this->error('榜单不存在');
        }
        return $this->fetch('create', ['info' => $info]);
    }

    /**
     * 保存更新的资源
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function update(Request $request)
    {
        $post   = $request->post();
        $result = $this->validate($post, 'Ranks.edit');

        if (true !== $result) {
            // 验证失败 输出错误信息
            $this->error($result);
        }
        $res = Ranks::update($post);
        if ($res) {
            cache('ranks_lists', null);
            $this->success('榜单[' . $res->name . ']修改成功', cookie('forward_url'));
        } else {
            $this->error('榜单修改失败，请稍后重试');
        }
    }

    /**
     * 删除指定资源
     * @param int $id 要删除的数据ID
     * @return \think\Response
     */
    public function delete($id)
    {
        $model = Ranks::get($id);
        if (false == $model) {
            $this->error('删除的榜单不存在！');
        }

        if ($model->is_sys) {
            $this->error('默认榜单只能修改或禁用无法删除！');
        }

        if ($model->delete()) {
            cache('Ranks_lists', null);
            $this->success('榜单成功删除！');
        } else {
            $this->error('榜单删除失败，请稍后重试！');
        }
    }
    
    /**
     * 更改榜单状态
     * @return \think\Response
     */
    public function setStatus()
    {
        cache('ranks_lists', null);
        return $this->changeStatus('Ranks');
    }
    
}
