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

use app\common\model\Article;
use think\Request;

/**
 * 后台内容控制器
 */
class ArticleController extends AdminController
{

    /**
     * 分类文档列表页
     * @param integer $cate 分类id
     * @param integer $pos  推荐标志
     * @return \think\Response
     */
    public function index($cate = null, $pos = null)
    {
        $map = $this->getListMap('title');
        if (!empty($cate)) {
            $map['category_id'] = $cate;
        }

        if (!empty($pos)) {
            $map[] = ['exp', "position & {$pos} = {$pos}"];
        }

        $order = $this->request->param('order');
        if (!empty($order)) {
            $order = $this->request->param('field') . ' ' . $order;
        }

        cookie('forward_url', $this->request->url());
        $list = $this->lists("article", $map, $order);
        return $this->fetch('', [
            '_list'  => $list,
            'cate'   => $cate,
            'curpos' => $pos,
        ]);
    }

    /**
     * 显示创建文档表单页.
     * @return \think\response
     */
    public function create($cate = 0)
    {
        $info['category_id'] = $cate;
        return $this->fetch('', ['info' => $info]);
    }

    /**
     * 文档新增页面初始化
     */
    public function save(Request $request)
    {
        $post = $request->post();
        $this->validInfo($post);
        $content = $post['content'];
        unset($post['content']);

        $info = Article::create($post);
        if ($info) {
            if ($info->content()->save($content)) {
                $this->success('[' . $info->title . ']Create success', cookie('forward_url'));
            }
            $info->delete();
        }
        $this->error('Article creation failed, please try again later');
    }

    /**
     * 文档编辑页面初始化
     */
    public function edit(Request $request, $id = 0)
    {
        if (empty($id)) {
            $this->error('Parameter can not be empty!');
        }
        // 获取详细数据
        $data = Article::get($id, 'content');
        if (!$data) {
            $this->error('The edited article does not exist');
        }

        $this->meta_title = 'Edit the document';
        return $this->fetch('create', ['info' => $data]);
    }

    /**
     * 更新一条数据
     */
    public function update(Request $request)
    {
        $post = $request->post();
        $this->validInfo($post);
        $content = $post['content'];
        unset($post['content']);
        $info = Article::update($post);
        if ($info || $info->content()->update($content)) {
            $this->success('[' . $info->title . ']update completed', cookie('forward_url'));
        }
        $this->error('Article update failed, please try again later');
    }

    /**
     * 待审核列表
     */
    public function examine()
    {
    }

    /**
     * 回收站列表
     */
    public function recycle()
    {
        $map['status'] = -1;
        $list          = $this->lists('article', $map, 'update_time desc');
        return $this->fetch('', ['list' => $list]);
    }

    /**
     * 写文章时自动保存至草稿箱
     */
    public function autoSave(Request $request)
    {
        $post = $request->post();
        //触发自动保存的字段
        $save_list = ['name', 'title', 'description', 'position', 'cover_id', 'deadline', 'create_time', 'content'];
        foreach ($save_list as $value) {
            if (!empty($data[$value])) {
                $if_save = true;
                break;
            }
        }
        $this->validInfo($post, 'Save draft failed:');
        
        $content = $post['content'];
        unset($post['content']);
        $post['status'] = 3;

        if (!empty($post['id'])) {
            $info = Article::update($post);
            if ($info || $info->content()->update($content)) {
                $this->success(
                    'Save the draft successfully',
                    '',
                    ['id' => $info->id]
                );
            }
        } else {
            if ($info = Article::create($post)) {
                if ($info->content()->save($content)) {
                    $this->success(
                        'Save the draft successfully',
                        '',
                        ['id' => $info->id]
                    );
                }
                $info->delete();
            }
        }
        $this->error('Save draft failed');
    }

    /**
     * 通用验证
     * @param  [type] $post
     * @param  string $title
     * @return mixed
     */
    protected function validInfo($post, $title = '')
    {
        $result = $this->validate($post, 'Article');
        if (true !== $result) {
            // 验证失败 输出错误信息
            $this->error($title . $result);
        }

        $result = $this->validate($post['content'], 'ArticleContent');
        if (true !== $result) {
            // 验证失败 输出错误信息
            $this->error($title . $result);
        }
        return true;
    }

    /**
     * 草稿箱
     */
    public function draftBox()
    {
        $map  = ['status' => 3];
        $list = $this->lists('article', $map);
        return $this->fetch('', ['list' => $list]);
    }

    /**
     * 还原被删除的数据
     */
    public function permit()
    {
        /*参数过滤*/
        $ids = $this->request->param('id/a');
        if (empty($ids)) {
            $this->error('Please select the data to be operated');
        }
        /*拼接参数并修改状态*/
        $map = [];
        if (is_array($ids)) {
            $map['id'] = ['in', $ids];
        } elseif (is_numeric($ids)) {
            $map['id'] = $ids;
        }

        if (Article::where($map)->update(['status' => 1])) {
            $this->success('Successful recovery');
        } else {
            $this->success('Recovery failed, please try again later');
        }
    }

    /**
     * 清空回收站
     */
    public function clear()
    {
        $ids = $this->request->param('id/a');
        if (empty($ids)) {
            $this->error('Please select the data to be operated');
        }
        $map = [];
        if (is_array($ids)) {
            $map['id'] = ['in', $ids];
        } elseif (is_numeric($ids)) {
            $map['id'] = $ids;
        }
        if (Article::where($map)->delete()) {
            $this->success('Empty Recycle Bin Success!');
        } else {
            $this->error('Empty Recycle Bin failed!');
        }
    }
    
    /**
     * 设置指定状态
     * @param  int $id
     * @return \think\response
     */
    public function setStatus()
    {
        return $this->changeStatus('article');
    }
}
