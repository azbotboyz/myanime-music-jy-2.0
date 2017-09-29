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
use app\common\model\SeoRule;

class SeoController extends AdminController
{
    /**
     * 显示SEO规则列表
     * @return \think\Response
     */
    public function index()
    {
        $map = $this->getListMap('title');
        $order = $this->request->param('order');
        if (!empty($order)) {
            $order = $this->request->param('field') . ' ' . $order;
        }
        $list = $this->lists("seoRule", $map, $order);
        Cookie('forward_url', $this->request->url());
        return $this->fetch('', ['_list' => $list]);
    }

    /**
     * 显示创建资源表单页
     * @return \think\Response
     */
    public function create()
    {
        return $this->fetch();
    }

    /**
     * 保存新建的资源
     *
     * @param \think\Request $request
     *
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id = 0)
    {
        if (!intval($id) || !$info = SeoRule::get($id)) {
            $this->error('规则不存在不存在');
        }
        return $this->fetch('create', ['info' => $info]);
    }

    /**
     * 保存更新的资源
     *
     * @param \think\Request $request
     * @return \think\Response
     */
    public function update(Request $request)
    {
        $post = $request->post();
        $result = $this->validate($post, 'SeoRule.edit');
        if (true !== $result) {
            // 验证失败 输出错误信息
            $this->error($result);
        }
        $res = SeoRule::update($post);
        if ($res) {
            cache('seo_rules', null);
            $this->success('SEO规则[' . $res->title . ']修改成功', Cookie('forward_url'));
        } else {
            $this->error('SEO规则修改失败，请稍后重试');
        }
    }
    
    /**
     * 删除指定资源
     * @param int $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }
}
