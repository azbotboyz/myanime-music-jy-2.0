<?php
/**
 * JYmusic音乐管理系统 PHP version 5.6+
 *
 * @author    战神~~巴蒂 <jyuucn@163.com>
 * @version   2.0.0
 * @license   http://jyuu.cn [未经授权严禁私自出售，否者承担法律责任]
 * @copyright 2014 - 2017 JYmusic
 */

namespace app\admin\controller;

use think\Request;
use app\admin\model\Menu;

/**
 * 后台菜单控制器
 *
 * @package Menucontroller
 */
class MenuController extends AdminController
{
    /**
     * 显示菜单列表
     * @param mixed $pid 父ID
     * @return \think\Response
     */
    public function index($pid = 0)
    {
        //实例化菜单模型
        $map  = $this->getListMap();
        $Menu = new Menu;

        if (!empty($pid) && $parent = $Menu->get($pid)) {
            $this->assign('parent', $parent->getData());
        }

        $map['pid'] = $pid;    
        $list  = $this->lists('Menu', $map);

        if ($list) {
            $statusTexts    = config('status_text');
            $whetherTexts   = config('whether_text');
            $allMenu        = $Menu->getLine();
            foreach ($list as &$val) {
                if (isset($val['pid']) && isset($allMenu[$val['pid']])) {
                    $val['parent_title'] = $allMenu[$val['pid']]['title'];
                }
                $val['hide_text']   = $whetherTexts[$val['hide']];
                $val['is_dev_text'] = $whetherTexts[$val['is_dev']];
                $val['status_text'] = $statusTexts[$val['status']];
            }
        }
        cookie('forward_url', $this->request->url());
        $this->assign('list', $list);
        return $this->fetch();
    }

    /**
     * 显示创建资源表单页.
     * @return \think\Response
     */
    public function create()
    {
        $menuModel  = new Menu;
        $menus      = $menuModel->getTree();
        $this->assign('menus', $menus);
        return $this->fetch();
    }

    /**
     * 保存新建的资源
     * @param  \think\Request $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $post   = $request->post();
        $result = $this->validate($post, 'Menu');
        if (true !== $result) {
            // 验证失败 输出错误信息
            $this->error($result);
        }

        $menu = new Menu($post);
        if ($menu->allowField(true)->save()) {
            cache('admin_menu', null);
            $this->success('菜单添加成功', cookie('forward_url'));
        } else {
            $this->error('菜单添加失败，请稍后重试');
        }
    }

    /**
     * 显示编辑资源表单页.
     * @param  int $id
     * @return \think\Response
     */
    public function edit($id)
    {
        $menuModel  = new Menu;
        $info   = $menuModel::get($id)->getData();
        $menus  = $menuModel->getTree();
        $this->assign('menus', $menus);
        $this->assign('info', $info);
        return $this->fetch('create');
    }

    /**
     * 保存更新的资源
     * @param \think\Request $request 请求数据
     * @param int            $id      主键ID
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        $post   = $request->post();

        $result = $this->validate($post, 'Menu');
        if (true !== $result) {
            // 验证失败 输出错误信息
            $this->error($result);
        }
        $menu = new Menu();
        if ($menu->save($post, ['id' => $id])) {
            cache('admin_menu', null);
            $this->success('菜单更新成功！', cookie('forward_url'));
        } else {
            $this->error('菜单更新失败！');
        }

    }

    /**
     * 删除指定菜单
     *
     * @param int $id
     *
     * @return \think\Response
     */
    public function delete($id)
    {
        if (Menu::destroy($id)) {
            cache('admin_menu', null);
            $this->success('菜单删除成功');
        }

        $this->error('菜单删除失败！');
    }
}
