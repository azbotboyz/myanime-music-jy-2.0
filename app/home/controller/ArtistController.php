<?php
/**
 * JYmusic音乐管理系统
 *
 * @version   2.0
 * @author    战神~~巴蒂 [jyuucn@163.com]
 * @license   http://jyuu.cn [未经授权严禁私自出售，否者承担法律责任]
 * @copyright JYmusic 2014 - 2017
 */

namespace app\home\controller;

use app\common\model\Artist;

class ArtistController extends HomeController
{
    /**
     * @return \think\Responsed
     */
    public function index()
    {
        $this->seoMeta();
        return $this->fetch();
    }
    
    /**
     * @param int $id
     * @return \think\Response
     */
    public function read($id = 0)
    {
        $info = [];
        $map['status'] = 1;
        if ((int)$id) {
            $map['id'] = $id;
            $info = Artist::get($map);
        }
        
        if (empty($info)) {
            abort(404,'You see the singer does not exist!');
        }
        
        $info = $info->parseData();
        $this->seoMeta($info);
        return $this->fetch('detail', ['data' => $info]);
    }
    
    /**
     * @param int $id
     * @return \think\Response
     */
    public function songs($id = 0)
    {
        $info = [];
        if ((int)$id) {
            $map['id'] = $id;
            $info = Artist::get($map);
        }
    
        if (empty($info)) {
            abort(404,'你查看的歌手不存在！');
        }
        
        $info = $info->parseData();
        $this->seoMeta($info);
        return $this->fetch('', ['data' => $info]);
    }
    
    /**
     * @param int $id
     * @return \think\Response
     */
    public function albums($id = 0)
    {
        $info = [];
        if ((int)$id) {
            $map['id'] = $id;
            $info = Artist::get($map);
        }
    
        if (empty($info)) {
            abort(404,'You see the singer does not exist!');
        }
        $info = $info->parseData();
        $this->seoMeta($info);
        return $this->fetch('', ['data' => $info]);
    }
}
