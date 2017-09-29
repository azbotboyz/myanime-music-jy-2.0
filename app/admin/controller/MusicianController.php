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
use app\common\api\Notice;
use app\common\model\Artist;
use app\common\model\Member;
use app\common\model\AuthMusician;

class MusicianController extends AdminController
{
    /**
     * 显示资源列表
     * @return \think\Response
     */
    public function index()
    {
        $map = $this->getListMap('name', 'id', 'all');
       
        $list = $this->lists("auth_musician", $map);
        cookie('forward_url', $this->request->url());
        return $this->fetch('', ['_list' => $list]);
    }
    
    /**
     * 审核音乐人
     * @param Request $request
     * @param int $id
     * @return mixed
     */
    public function audit(Request $request, $id = 0)
    {
        if (empty($id) || !$musician = AuthMusician::get($id)) {
            $this->error('审核的音乐人不存在');
        }
    
        if ($musician->status != 2) {
            $this->error('音乐人不处于待审状态');
        }
        
        if ($request->isPost()) {
    
            $post = $this->request->post();
            $status = $post['status'];
            $musician->status = $status;
            
            if ($musician->save()) {
                //设置会员音乐人状态
                $user = Member::field('is_musician,avatar,location,uid')
                    ->where('uid', $musician->uid)->find();
                $notice = (new Notice())->to($musician->uid)->title('音乐人审核通知');
                if ($status == 1) {
                    $data =  [
                        'uid' => $musician->uid,
                        'name' => $musician->artist_name,
                        'type_id' => $musician->type,
                        'cover_url' => $user->avatar,
                        'region' => $user->location,
                    ];
                    Artist::create($data);
                    $user->is_musician = 1;
                    $user->save();
                    //清空用户缓存
                    clear_user_info($musician->uid);
                    
                    $content = '你申请的音乐人成功通过审核';
                } else {
                    $content = '你申请的音乐人未通过审核';
                    if (!empty($post['back_info'])) {
                        $content .= ',' . $post['back_info'];
                    }
                }
                
                $result = $notice->content($content)->send();
                if (true !== $result) {
                    $this->success('音乐[' . $musician->artist_name . ']审核成功,但是通知发送失败：' . $result, cookie('forward_url'));
                }
                $this->success('音乐[' . $musician->artist_name . ']审核成功', cookie('forward_url'));
            }
            $this->error('审核修改失败，请稍后重试');
        } else {
            return $this->fetch('', ['info' => $musician]);
        }
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
        $result = $this->validate($post, 'AuthMusician');
        if (true !== $result) {
            // 验证失败 输出错误信息
            $this->error($result);
        }
        $res = AuthMusician::create($post);
        if ($res) {
            $this->success('认证音乐人[' . $res->realname . ']创建成功');
        } else {
            $this->error('认证音乐人添加失败，请稍后重试');
        }
    }

    /**
     * 显示编辑资源表单页.
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        if (!intval($id) || !$info = AuthMusician::get($id)) {
            $this->error('认证音乐人不存在');
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
        $result = $this->validate($post, 'AuthMusician.edit');
        if (true !== $result) {
            // 验证失败 输出错误信息
            $this->error($result);
        }
        $res = AuthMusician::update($post);
        if ($res) {
            $this->success('认证音乐人[' . $res->realname . ']修改成功', cookie('forward_url'));
        } else {
            $this->error('认证音乐人修改失败，请稍后重试');
        }
    }

    /**
     * 删除指定资源
     * @param int $id 要删除的数据ID
     * @return \think\Response
     */
    public function delete($id)
    {
        $model = AuthMusician::get($id);
        
        if (false == $model) {
            $this->error('删除的认证音乐人不存在！');
        }
        
        if ($model->delete()) {
            //删除音乐人
            $artist = new Artist();
            $artist->where('uid', $model->uid)->delete();
            $member = new Member();
            $member->where('uid', $model->uid)->setField('is_musician', 0);
            clear_user_info($model->uid);
            $this->success('认证音乐人成功删除！');
        } else {
            $this->error('认证音乐人删除失败，请稍后重试！');
        }
    }

    /**
     * 更改认证音乐人状态
     * @return \think\Response
     */
    public function setStatus()
    {
        $status = $this->request->param('status');
        $ids = $this->request->param('id/a');
        $model = new AuthMusician();
        $map['id'] = ['in', $ids];
        
        if (!$model->where($map)->save('status', $status)) {
            $where['uid'] = $model->where($map)->column('uid');
            
            $member = new Member();
            $member->where($where)->setField('is_musician', $status);
            cache('sys_user_info_list', null);
            
            $artist = new Artist();
            $artist->where($where)->setField('status', $status);
            $this->error('操作的音乐人不存在！');
        } else {
            $this->error('操作失败,请稍后重试！');
        }
        
        
    }
}
