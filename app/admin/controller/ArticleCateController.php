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
use app\common\model\ArticleCategory;

class ArticleCateController extends AdminController
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index($id = 0)
    {
        $list = (new ArticleCategory())->getTree(intval($id));
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
     * @return \think\Response
     */
    public function create($pid = 0)
    {
        if (intval($pid) > 0) {
            $parent = ArticleCategory::get($pid);
            $this->assign('parent', $parent);
        }
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
        $result = $this->validate($post, 'ArticleCategory');
        if (true !== $result) {
            // 验证失败 输出错误信息
            $this->error($result);
        }

        $res = ArticleCategory::create($post);
        if ($res) {
            ArticleCategory::clear('article_cate');
            $this->success('Information classification[' . $res->title . ']Added successfully', cookie('forward_url'));
        } else {
            $this->error('Message classification added failed, please try again later');
        }
    }

    /**
     * 显示编辑资源表单页.
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id = 0, $pid = 0)
    {
        $info = ArticleCategory::get($id);
        if (!intval($id) || !$info) {
            $this->error('Information classification does not exist');
        }
        if (intval($pid)) {
            $parent = ArticleCategory::get($pid);
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
        $result = $this->validate($post, 'ArticleCategory');
        if (true !== $result) {
            // 验证失败 输出错误信息
            $this->error($result);
        }
        $res = ArticleCategory::update($post);
        if ($res) {
            Cache::clear('article_cate');
            $this->success('Information classification[' . $res->title . ']修改成功', Cookie('forward_url'));
        } else {
            $this->error('Message classification failed, please try again later');
        }
    }

    /**
     * 删除指定资源
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        $cate = ArticleCategory::get($id);
        if (false == $cate) {
            $this->error('Deleted information class does not exist!');
        }

        //判断该分类下有没有子分类，有则不允许删除
        $child = ArticleCategory::where('pid', $id)->field('id')->select();

        if (!empty($child)) {
            $this->error('Please delete the subcategories under this category first');
        }

        //判断该分类下有没有内容
        $articleList = db('article')->where('category_id', $id)->field('id')->select();

        if (!empty($articleList)) {
            $this->error('Please delete the posts in that category (contains the Recycle Bin)');
        }

        if ($cate->delete()) {
            Cache::clear('article_cate');
            $this->success('Successfully deleted information class!');
        } else {
            $this->error('Music classification deleted failed, please try again later!');
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
        Cache::clear('article_cate');
        return $this->changeStatus('ArticleCategory');
    }

    /**
     * 操作分类初始化
     * @param string $type
     * @author huajie <banhuajie@163.com>
     */
    public function operate($type = 'move')
    {
        //检查操作参数
        if (strcmp($type, 'move') == 0) {
            $operate = 'mobile';
        } elseif (strcmp($type, 'merge') == 0) {
            $operate = 'merge';
        } else {
            $this->error('Parameter error!');
        }
        $from = intval(I('get.from'));
        empty($from) && $this->error('Parameter error!');

        //获取分类
        $map  = array('status' => 1, 'id' => array('neq', $from));
        $list = M('Category')->where($map)->field('id,pid,title')->select();

        //移动分类时增加移至根分类
        if (strcmp($type, 'move') == 0) {
            //不允许移动至其子孙分类
            $list = tree_to_list(list_to_tree($list));

            $pid = M('Category')->getFieldById($from, 'pid');
            $pid && array_unshift($list, array('id' => 0, 'title' => 'Root classification'));
        }

        $this->assign('type', $type);
        $this->assign('operate', $operate);
        $this->assign('from', $from);
        $this->assign('list', $list);
        $this->meta_title = $operate . 'classification';
        $this->display();
    }

    /**
     * 移动分类
     * @author huajie <banhuajie@163.com>
     */
    public function move()
    {
        $to   = I('post.to');
        $from = I('post.from');
        $res  = M('Category')->where(array('id' => $from))->setField('pid', $to);
        if ($res !== false) {
            $this->success('Category moves successfully!', U('index'));
        } else {
            $this->error('Category move failed!');
        }
    }

    /**
     * 合并分类
     */
    public function merge()
    {
        $to    = I('post.to');
        $from  = I('post.from');
        $Model = M('Category');

        //检查分类绑定的模型
        $from_models = explode(',', $Model->getFieldById($from, 'model'));
        $to_models   = explode(',', $Model->getFieldById($to, 'model'));
        foreach ($from_models as $value) {
            if (!in_array($value, $to_models)) {
                $this->error('Please bind to target classification' . get_document_model($value, 'title') . '模型');
            }
        }

        //检查分类选择的文档类型
        $from_types = explode(',', $Model->getFieldById($from, 'type'));
        $to_types   = explode(',', $Model->getFieldById($to, 'type'));
        foreach ($from_types as $value) {
            if (!in_array($value, $to_types)) {
                $types = C('DOCUMENT_MODEL_TYPE');
                $this->error('Please bind the target to the document type:' . $types[$value]);
            }
        }

        //合并文档
        $res = M('Document')->where(array('category_id' => $from))->setField('category_id', $to);

        if ($res !== false) {
            //删除被合并的分类
            $Model->delete($from);
            $this->success('Merge success!', U('index'));
        } else {
            $this->error('Merge classification failed!');
        }

    }
}
