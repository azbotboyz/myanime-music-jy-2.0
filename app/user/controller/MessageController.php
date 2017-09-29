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

use app\common\model\Message;
use think\Request;

//user 模块公共控制器
class MessageController extends UserController
{
    /**
     * 消息首页
     * @return \think\response
     */
    public function index()
    {
        $model = new Message();

        $list = $model->where('post_uid', $this->user['uid'])
            ->whereOr('to_uid')
            ->where('status', 1)
            ->order('id desc')
            ->paginate(20);

        foreach ($list as &$v) {
            if ($v->post_uid == $this->user['uid']) {
                $v->title = '你对 <a class="text-info" href="' .
                url('user/Index/read', ['uid' => $v->to_uid]) . '">' .
                $v->to_uname . ' </a>说：';
                $v->is_read = 1;
            } else {
                $v->title = '<a class="text-info" href="' .
                url('user/Index/read' . ['uid' => $v->post_uid]) . '">' .
                $v->post_uname . ' </a>对你说：';
            }
        }

        // 获取分页显示
        $page = $list->render();
        // 模板变量赋值
        $this->assign('list', $list);
        $this->assign('_page', $page);

        $title = '消息中心 - ' . config('web_site_title');
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

        $model = new Message();
        $info  = $model->where('status', 1)
            ->where('id', $id)
            ->find();

        if (!$info || (($info['post_uid'] != $this->user['uid']) && ($info['to_uid'] != $this->user['uid']))) {
            $this->error('你查看的信息不存在');
        }

        if ($info['post_uid'] == $this->user['uid']) {
            $info['title'] = '你对 <a class="text-info" href="' . url('user/Index/read', ['uid' => $info['to_uid']]) . '">' . $info['to_uname'] . ' </a>说：';
        } else {
            $info['title'] = '<a class="text-info" href="' . url('user/Index/read', ['uid' => $info['post_uid']]) . '">' . $info['post_uname'] . ' </a>对你说：';
            if ($info['is_read'] == 0) {
                $model->where('id', $id)->setField('is_read', 1);
            }
        }

        $title = '我的私信 - ' . config('web_site_title');
        $this->assign('meta_title', $title);
        $this->assign('data', $info);
        return $this->fetch();
    }

    /**
     * 系统通知
     * @return \think\response
     */
    public function create(Request $request)
    {
        if (isOffSpite('send_msg')) {
            return json(['code' => 40405, 'error' => '操作过于频繁，休息会再来操作！']);
        }

        $model = new Message();
        $post  = $request->post();

        if (isset($post['reply_id']) && intval($post['reply_id'])) {
            $msg = $model->field('post_uid,to_uid')
                ->where('id', $post['reply_id'])
                ->find();
            if (!$msg) {
                return json([
                    'code'  => 40404,
                    'error' => '你回复的消息不存在',
                ]);
            } else {
                $post['to_uid'] = $msg['post_uid'] == $this->user['uid'] ? $msg['to_uid'] : $msg['post_uid'];
            }
        }

        $post['post_uid'] = $this->user['uid'];
        $result           = $this->validate($post, 'Message');
        if (true !== $result) {
            return json([
                'code'  => 40030,
                'token' => $request->token(),
                'error' => $result,
            ]);
        }

        $model = new Message($post);
        if ($model->allowField(true)->save()) {
            return json([
                'code'   => 0,
                'msg'    => '消息发送成功',
                'result' => [
                    'content' => $model->content,
                ],
            ]);
        }

        return json([
            'code'  => 40500,
            'error' => '消息发送失败，请稍后重试',
            'token' => $request->token(),
        ]);
    }
}
