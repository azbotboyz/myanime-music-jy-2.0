<?php
/**
 * JYmusic音乐管理系统
 *
 * @version     2.0
 * @author      战神~~巴蒂 <jyuucn@163.com>
 * @license     http://jyuu.cn [未经授权严禁私自出售，否者承担法律责任]
 * @copyright   2014 - 2017 JYmusic
 */

namespace app\user\controller;

use app\common\model\Notice;
use app\common\model\Message;

//系统通知控制器
class NoticeController extends UserController
{
    /**
     * 系统通知
     * @return \think\response
     */
    public function index()
    {
        $model = new Notice();
        $list = $model->where('uid', $this->user['uid'])
            ->where('status', 1)
            ->order('id desc')
            ->paginate(20);

        // 获取分页显示
        $page = $list->render();
        // 模板变量赋值
        $this->assign('list', $list);
        $this->assign('_page', $page);
    
        $title = '系统通知 - ' . config('web_site_title');
        $this->assign('meta_title', $title);
        return $this->fetch();
    }

    /**
     * 消息详细
     * @param  integer $id
     * @return \think\response
     */
    public function read($id = 0)
    {
        if (!intval($id)) {
            abort('404', '参数错误');
        }
        $model = new Notice();
        $info = $model->where('id', $id)
            ->where('uid', $this->user['uid'])
            ->find();
        
        if (!$info) {
            $this->error('你查看的信息不存在');
        }
        
        if ($info['is_read'] == 0) {
            $model->where('id', $id)->setField('is_read',1);
        }
        
        $title = '系统通知 - ' . config('web_site_title');
        $this->assign('meta_title', $title);
        $this->assign('data', $info);
        return $this->fetch();
    }
}
