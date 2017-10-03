<?php
/**
 * JYmusic音乐管理系统 PHP version 5.4+
 *
 * @version     2.0
 * @author      战神~~巴蒂 <jyuucn@163.com>
 * @license     http://jyuu.cn/license [未经授权严禁私自出售，否者承担法律责任]
 * @copyright   2014 - 2017 JYmusic
 */

namespace app\home\controller;

use think\Db;

class SearchController extends HomeController
{

    /**
     * 首页展示
     * @return \think\Response
     */
    public function index()
    {
        if (isOffSpite('search')) {
            abort(404, 'Search is too frequent! The');
        }

        $keys = text_filter($this->request->get('keys'));
        $type = $this->request->param('type');
        $type = (int) $type ? $type : 1;

        if (strstr($keys, '/') || empty($keys)) {
            return $this->fetch();
        }

        switch ($type) {
            case 1:
                if (is_numeric($keys)) {
                    $map = ['id' => ['like', "%{$keys}%"]];
                } else {
                    $map = ['name|artist_name' => ['like', "%{$keys}%"]];
                }
                $field = 'id,name,genre_id,cover_url,artist_id,artist_name,album_id,album_name,up_uid,up_uname,download,favtimes,listens';
                $name  = 'songs';
                $title = "music";
                break;
            case 2:
                if (is_numeric($keys)) {
                    $map = ['id' => ['like', "%{$keys}%"]];
                } else {
                    $map = ['name' => ['like', "%{$keys}%"]];
                }
                $field = 'id,name,alias,type_id,type_name,cover_url,hits,favtimes';
                $name  = 'artist';
                $title = "artist";
                break;
            case 3:
                if (is_numeric($keys)) {
                    $map = ['id' => ['like', "%{$keys}%"]];
                } else {
                    $map = ['name' => ['like', "%{$keys}%"]];
                }
                $field = 'id,name,type_id,type_name,artist_id,artist_name,add_uid,add_uname,cover_url,hits,favtimes';
                $name  = 'album';
                $title = "Album";
                break;
            case 4:
                if (is_numeric($keys)) {
                    $map = ['id' => ['like', "%{$keys}%"]];
                } else {
                    $map = ['nickname' => ['like', "%{$keys}%"]];
                }
                $field = 'uid,nickname,avatar,follows,fans,artist_id,sex,login,hits,location';
                $name  = 'member';
                $title = "user";
                break;
            default:
                $this->error('Please enter your search keywords');
                break;
        }
        $map['status'] = 1;
        $list          = Db::name($name)->where($map)->field($field)->paginate(20);

        if ($list) {
            $page = $list->render();
            $this->assign('_page', $page);
            $this->assign('_total', $list->total());
        }

        $this->SeoMeta();
        $this->assign('list', $list);
        $this->assign('title', $title);
        $this->assign('type', $type);
        $this->assign('keys', $keys);
        return $this->fetch($name);
    }
}
