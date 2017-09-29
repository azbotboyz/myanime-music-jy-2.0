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
use app\common\model\Config;

class ConfigController extends AdminController
{
    /**
     * 显示网站设置
     * @int  $id
     */
    public function index($group = 1)
    {
        $map            = $this->getSearchMap();
        $map['status']  = 1;
        if (intval($group)) {
            $map['group']   = $group;
        }

        $list   = $this->lists("Config", $map, 'sort,id');
        cookie('forward_url', $this->request->url());
        $this->assign('list', $list);
        $this->assign('group_id', $group);
        return $this->fetch();
    }

    /**
     * 显示创建配置
     *
     * @return \think\Response
     */
    public function create()
    {

    }

    /**
     * 保存新建的配置
     *
     * @param  \think\Request $request
     *
     * @return \think\Response
     */
    public function save(Request $request)
    {

    }

    /**
     * 显示指定的资源
     *
     * @param  int $id
     *
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int $id
     *
     * @return \think\Response
     */
    public function edit($id)
    {

    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request $request
     * @param  int            $id
     *
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {

    }


    /**
     * 删除指定资源
     *
     * @param  int $id
     *
     * @return \think\Response
     */
    public function delete($id)
    {
        if (Config::destroy($id)) {
            $this->success('配置项删除成功');
        }

        $this->error('配置项删除失败！');
    }
}
