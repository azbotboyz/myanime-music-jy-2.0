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
        $this->assign('meta_title', 'About the site');
        return $this->fetch();
	}
    
    
    /**
     * 显示创建资源表单页.
     * @return \think\Response
     */
    public function create($type = "")
    {
        $this->assign('type' , $type);
        $this->assign('meta_title' , 'Add a website document');
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
            'title.require'     => 'The title already exists',
            'title.length'      => 'Title Length 2-40 characters',
            'name.length'      => 'Alias ​​length 2-16 characters',
            'name.unique'      => 'Alias ​​already exists',
            'content.require' => 'Please enter the content',
            'appname.require'    => 'The document type can not be empty',
        ];
    
        $result = $this->validate($post, $rule, $msg);
        
        if (true !== $result) {
            // 验证失败 输出错误信息
            $this->error($result);
        }
        $res = Site::create($post);
        if ($res) {
            $this->success('label[' . $res->title . ']Create success');
        } else {
            $this->error('Tag added failed, please try again later');
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
            $this->error('The document does not exist');
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
            'title.require'     => 'The title already exists',
            'title.length'      => 'Title Length 2-40 characters',
            'name.length'      => 'Alias ​​length 2-16 characters',
            'name.unique'      => 'Alias ​​already exists',
            'content.require' => 'Please enter the content',
            'appname.require'    => 'The document type can not be empty',
        ];
    
        $result = $this->validate($post, $rule, $msg);
        if (true !== $result) {
            // 验证失败 输出错误信息
            $this->error($result);
        }
        $res = Site::update($post);
        if ($res) {
            $this->success('[' . $res->title . ']Successfully modified', cookie('forward_url'));
        } else {
            $this->error('The change failed. Please try again later');
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
            $this->error('Deleted document does not exist!');
        }
        if ($info->delete()) {
            $this->success('Document deleted successfully!');
        } else {
            $this->error('Document deleted failed, please try again later!');
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