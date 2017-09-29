<?php
// +-------------------------------------------------------------+
// | Copyright (c) 2014-2015 JYmusic音乐管理系统                 |
// +-------------------------------------------------------------
// | Author: 战神~~巴蒂 <378020023@qq.com> <http://www.jyuu.cn>  |
// +-------------------------------------------------------------+

namespace app\admin\controller;

use think\Request;
use app\common\model\Site;

class  SiteController extends AdminController {
    
    /**
     * 显示列表
     * @param string $type
     * @return \think\Response
     */
    public function index($type="")
    {
        $map = $this->getListMap('title');
        
        if ($type) {
            $map['appname']   = $type;
        }
        
        $list = $this->lists('Site',$map,'id desc');
        // 记录当前列表页的cookie
        Cookie('__forward__',$_SERVER['REQUEST_URI']);
        
		$this->assign('type', $type);
        $this->assign('_list', $list);
        $this->assign('meta_title', '关于网站');
        return $this->fetch();
	}
    
    
    /**
     * 显示创建资源表单页.
     * @return \think\Response
     */
    public function create($type = "")
    {
        $this->assign('type' , $type);
        $this->assign('meta_title' , '新增网站文档');
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
        $rule = [
            'title' => 'require|length:2,40|unique:Site',
            'name' => 'require|length:2,16|unique:Site',
            'content' => 'require',
            'appname'    => 'require',
        ];
    
        $msg = [
            'title.require'     => '标题已存在',
            'title.length'      => '标题长度2-40个字符',
            'name.length'      => '别名长度2-16个字符',
            'name.unique'      => '别名已存在',
            'content.require' => '请输入内容',
            'appname.require'    => '文档类型不能为空',
        ];
    
        $result = $this->validate($post, $rule, $msg);
        
        if (true !== $result) {
            // 验证失败 输出错误信息
            $this->error($result);
        }
        $res = Site::create($post);
        if ($res) {
            $this->success('标签[' . $res->title . ']创建成功');
        } else {
            $this->error('标签添加失败，请稍后重试');
        }
    }
    
    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        if (!intval($id) || !$info = Site::get($id)) {
            $this->error('文档不存在');
        }
        return $this->fetch('create',[
            'info' => $info,
            'type' => $info['appname']
        ]);
    }
    
    /**
     * 保存更新的资源
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request)
    {
        $post   = $request->post();
        $rule = [
            'title' => 'require|length:2,40',
            'name' => 'require|length:2,16',
            'content' => 'require',
            'appname'    => 'require',
        ];
    
        $msg = [
            'title.require'     => '标题已存在',
            'title.length'      => '标题长度2-40个字符',
            'name.length'      => '别名长度2-16个字符',
            'name.unique'      => '别名已存在',
            'content.require' => '请输入内容',
            'appname.require'    => '文档类型不能为空',
        ];
    
        $result = $this->validate($post, $rule, $msg);
        if (true !== $result) {
            // 验证失败 输出错误信息
            $this->error($result);
        }
        $res = Site::update($post);
        if ($res) {
            $this->success('[' . $res->title . ']修改成功', cookie('forward_url'));
        } else {
            $this->error('修改失败，请稍后重试');
        }
    }
    
    /**
     * 删除指定资源
     * @param int $id 要删除的数据ID
     * @return \think\Response
     */
    public function delete($id)
    {
        $info = Site::get($id);
        if (!$info) {
            $this->error('删除的文档不存在！');
        }
        if ($info->delete()) {
            $this->success('文档成功删除！');
        } else {
            $this->error('文档删除失败，请稍后重试！');
        }
    }
    
    /**
     * 更改状态
     * @return \think\Response
     */
    public function setStatus()
    {
        return $this->changeStatus('Site');
    }
}