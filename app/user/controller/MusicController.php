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

use think\Request;
use app\common\model\Album;
use app\common\model\Songs;

class MusicController extends UserController
{
    /**
     * 个人音乐页面
     * @return \think\response
     */
    public function index()
    {
        $title = '我的音乐 - ' . config('web_site_title');
        return $this->fetch('', ['meta_title' => $title]);
    }

    /**
     * 个人音乐待审页面
     * @return \think\response
     */
    public function audit()
    {
        $title = '审核的音乐 - ' . config('web_site_title');
        return $this->fetch('', ['meta_title' => $title]);
    }
    
    /**
     * 个人音乐驳回页面
     * @return \think\response
     */
    public function back()
    {
        $title = '驳回列表 - ' . config('web_site_title');
        return $this->fetch('', ['meta_title' => $title]);
    }

    /**
     * 个人音乐下载页面
     * @return \think\response
     */
    public function down()
    {
        $title = '我的下载 - ' . config('web_site_title');
        return $this->fetch('', ['meta_title' => $title]);
    }

    /**
     * 创建歌曲
     * @return \think\response
     */
    public function create()
    {
        if(config('only_musician_upload') && !$this->user['is_musician']) {
            $this->error('你还没有认证音乐人，请先认证！！', 'user/Musician/auth', '', 5);
        }
        
        //获取当前用户的专辑
        $albums = Album::where('add_uid', UID)->field('id,name')->select();
        $this->meta_title = '分享音乐 - ' . config('web_site_title');
        return $this->fetch('share', ['albums' => $albums]);
    }
    
    /**
     * 编辑歌曲
     * @param int $id
     * @return \think\response
     */
    public function edit($id = 0)
    {
        if (!intval($id)) {
            $this->error('参数错误');
        }
    
        $model = new Songs();
        $map['id'] = $id;
        $map['status'] = 0;
        $map['up_uid'] =$this->user['uid'];
        
        $song = $model->where($map)
            ->field('id,name,genre_id,cover_id,cover_url,artist_id,artist_name,album_id,album_name')
            ->with(['extend' => function($query){
                $query->field('mid,listen_url,introduce,server_id,listen_file_id');
            }])
            ->find();
        
        if (!$song) {
            $this->error('音乐不存在');
        }
        
        $info = $song->getData();
        $info = array_merge($info, $song->extend->getData());
        
        //获取当前用户的专辑
        $albums = Album::where('add_uid', UID)->field('id,name')->select();
        $this->meta_title = '编辑音乐 - ' . config('web_site_title');
        return $this->fetch('share', ['albums' => $albums, 'data' => $info]);
    }
    
    /**
     * 保存创建的歌曲
     * @param Request $request
     * @return \think\response
     */
    public function save(Request $request)
    {
        if(config('only_musician_upload') && !$this->user['is_musician']) {
            return json(['code' => 40403, 'error' => '你还没有认证音乐人，请先认证']);
        }
        
        $post = $request->post();
        $post['up_uid'] = $this->user['uid'];
        
        $result = $this->validate($post, 'Songs.user_create');
        if (true !== $result) {
            return json(['code' => 40030, 'error' => $result]);
        }
        $extend = $post['extend'];
        $result = $this->validate($extend, 'SongsExtend');
        if (true !== $result) {
            return json(['code' => 40030, 'error' => $result]);
        }
        $post['status'] = 2;
        $songs = new Songs($post);
        if ($songs->allowField(true)->save()) {
            if ($songs->extend()->save($extend)) {
                return json([
                    'code' => 0,
                    'msg' =>'音乐[' . $songs->name . ']添加成功，请等待审核！',
                    'url' => url('user/Music/audit')
                ]);
            }
            $songs->delete();
        }
        return json(['code' => 40500, 'msg' => '添加失败，请稍后重试']);
    }
    
    /**
     * 更新歌曲
     * @param Request $request
     * @return \think\response
     */
    public function update(Request $request)
    {
        $post = $request->post();
        $map['up_uid'] = $post['up_uid'] = $this->user['uid'];
        $id = $post['id'];
        
        if (empty($id)) {
            return json(['code' => 40004, 'error' => '参数错误']);
        }
    
        $result = $this->validate($post, 'Songs.user_create');
    
        if (true !== $result) {
            return json(['code' => 40030, 'error' => $result]);
        }
        $extend = $post['extend'];
        $result = $this->validate($extend, 'SongsExtend');
        if (true !== $result) {
            return json(['code' => 40030, 'error' => $result]);
        }
    
        $model = new Songs();
        $map['id'] = $id;
        $map['status'] = 0;
        
        if (empty($id) || !$song = $model->where($map)->find()) {
            return json(['code' => 40404, 'error' => '你编辑的音乐不存在']);
        }
        $post['status'] = 2;
        $data = $model->checkUpdateField($post, $song);
        $extend['mid'] = $data['id'];
        $res = $model->isUpdate(true)->allowField(true)->save($data);
        $res2 = $model->extend()->update($extend);
    
        if ($res || $res2) {
            return json([
                'code' => 0,
                'msg' =>'音乐[' . $model->name . ']修改成功，请等待审核！',
                'url' => url('user/Music/audit')
            ]);
        }

        return json(['code' => 40500, 'msg' => '音乐修改失败，请稍后重试']);
    }
}
