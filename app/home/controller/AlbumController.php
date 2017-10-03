<?php
/**
 * JYmusic音乐管理系统
 *
 * @version     2.0
 * @author      战神~~巴蒂 [jyuucn@163.com]
 * @license     http://jyuu.cn [未经授权严禁私自出售，否者承担法律责任]
 * @copyright   JYmusic 2014 - 2017
 */

namespace app\home\controller;

use app\common\model\Album;
use app\common\model\AlbumType;

class AlbumController extends HomeController
{

    public function index()
    {
        $this->seoMeta();
        return $this->fetch();
    }

    /**
     * @param string $id
     * @return string
     */
    public function read($id = "")
    {
        $info          = [];
        $map['status'] = 1;
        if ((int) $id) {
            $map['id'] = $id;
            $info      = Album::get($map);
        }
        if (empty($info)) {
            abort(404,'The album you are viewing does not exist');
        }

        $info = $info->parseData();
        $this->seoMeta($info);
        return $this->fetch('detail', ['data' => $info]);
    }

    /**
     * @param string $name
     * @return string
     */
    public function type($name = "")
    {

        $info          = [];
        $map['status'] = 1;
        if ((int) $name) {
            $map['id'] = $name;
            $info      = AlbumType::get($map);
        } elseif (ctype_alnum($name)) {
            $map['alias'] = $name;
            $info         = AlbumType::get($map);
        }

        if (empty($info)) {
            abort(404,'The category you are viewing does not exist!');
        }
        $info = $info->parseData();
        $this->seoMeta($info);
        return $this->fetch('', ['data' => $info]);
    }

}
