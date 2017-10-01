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
            $this->success('List[' . $res->name . ']Create success');
        } else {
            $this->error('The list has failed. Please try again later');
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
            $this->error('The list does not exist');
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
            $this->success('List[' . $res->name . ']Successfully modified', cookie('forward_url'));
        } else {
            $this->error('The list changes failed. Please try again later');
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
            $this->error('Deleted list does not exist!');
        }

        if ($model->is_sys) {
            $this->error('Default list can only be modified or disabled can not be deleted!');
        }

        if ($model->delete()) {
            cache('Ranks_lists', null);
            $this->success('The list was successfully deleted!');
        } else {
            $this->error('List deleted failed, please try again later!');
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
