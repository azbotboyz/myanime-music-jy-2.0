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
            $this->error('The auditionist does not exist');
        }
    
        if ($musician->status != 2) {
            $this->error('The musician is not in a pending state');
        }
        
        if ($request->isPost()) {
    
            $post = $this->request->post();
            $status = $post['status'];
            $musician->status = $status;
            
            if ($musician->save()) {
                //设置会员音乐人状态
                $user = Member::field('is_musician,avatar,location,uid')
                    ->where('uid', $musician->uid)->find();
                $notice = (new Notice())->to($musician->uid)->title('Audience review notification');
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
                    
                    $content = 'The artist you applied has successfully passed the audit';
                } else {
                    $content = 'The artist you applied did not pass the audit';
                    if (!empty($post['back_info'])) {
                        $content .= ',' . $post['back_info'];
                    }
                }
                
                $result = $notice->content($content)->send();
                if (true !== $result) {
                    $this->success('music[' . $musician->artist_name . ']check success,But the notification failed to send:' . $result, cookie('forward_url'));
                }
                $this->success('music[' . $musician->artist_name . ']check success', cookie('forward_url'));
            }
            $this->error('Audit changes failed. Please try again later');
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
            $this->success('Certified musicians[' . $res->realname . ']Create success');
        } else {
            $this->error('Certified musician added failed, please try again later');
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
            $this->error('Certified musicians do not exist');
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
            $this->success('Certified musicians[' . $res->realname . ']Successfully modified', cookie('forward_url'));
        } else {
            $this->error('Certified musician changes failed, please try again later');
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
            $this->error('Deleted certified musicians do not exist!');
        }
        
        if ($model->delete()) {
            //删除音乐人
            $artist = new Artist();
            $artist->where('uid', $model->uid)->delete();
            $member = new Member();
            $member->where('uid', $model->uid)->setField('is_musician', 0);
            clear_user_info($model->uid);
            $this->success('Certified musician successfully deleted!');
        } else {
            $this->error('Certified musician deleted failed, please try again later!');
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
            $this->error('The operating musician does not exist!');
        } else {
            $this->error('operation failed,Please try again later!');
        }
        
        
    }
}
