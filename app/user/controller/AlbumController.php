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

class AlbumController extends UserController
{
    /**
     * @return \think\response
     */
    public function index()
    {
        $title = '我的专辑 - ' . config('web_site_title');
        return $this->fetch('', ['meta_title' => $title]);
    }
    
    /**
     * @return \think\response
     */
    public function create()
    {
        $title = '创建专辑 - ' . config('web_site_title');
        return $this->fetch('', ['meta_title' => $title]);
    }
    
    /**
     * @return \think\response
     */
    public function edit()
    {
        $title = '编辑专辑 - ' . config('web_site_title');
        return $this->fetch('', ['meta_title' => $title]);
    }
    
    /**
     * 写入专辑
     * @param Request $request
     * @return \think\response
     */
    public function save(Request $request)
    {
        $post   = $request->post();
        $result = $this->validate($post, 'Album.user_create');
    
        if (true !== $result) {
            // 验证失败 输出错误信息
            return json(['code' =>40030, 'error' => $result]);
        }
        
        $album = new Album($post);
        $album->add_uid = $this->user['uid'];
        $album->add_uname = $this->user['nickname'];
        
        if ($album->allowField(true)->save()) {
            cache('album_lists', null);
            clear_user_info($this->user['uid']);
            return json(['code' =>0, 'msg' => '专辑[' . $album->name . ']创建成功']);
        } else {
            return json(['code' =>40500, 'error' => '专辑添加失败，请稍后重试']);
        }
    }
    
    /**
     * 更新专辑
     * @return \think\response
     */
    public function update()
    {

    }
    
}
