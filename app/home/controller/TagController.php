<?php
/**
 * JYmusic音乐管理系统
 *
 * @version     2.0
 * @author      战神~~巴蒂 <jyuucn@163.com>
 * @license     http://jyuu.cn [未经授权严禁私自出售，否者承担法律责任]
 * @copyright   2014 - 2017 JYmusic
 */

namespace app\home\controller;

use app\common\model\Tags;

class TagController extends HomeController
{

    /**
     * 风格标签页
     * @return \think\Response
     */
    public function index()
    {
        $this->seoMeta();
        return $this->fetch();
    }

    /**
     * 风格标签详情页
     * @param mixed $name
     * @return \think\Response
     */
    public function read($name = null)
    {
        $info          = [];
        $map['status'] = 1;
        
        $model = new Tags();
        if (is_numeric($name) && $name > 0) {
            $map['id'] = $name;
            $info      = $model->get($map);
        } elseif (ctype_alnum($name)) {
            $info  = $model->where('alias', $name)->find();
        } elseif (preg_match("/[\x7f-\xff]/", $name)) {
            $info = $model->where('name', $name)->find();
        }
        if (empty($info)) {
            abort(404, 'The style tag you are viewing does not exist!');
        }

        $info = $info->parseData();
        $this->seoMeta($info);
        return $this->fetch('detail', ['data' => $info]);
    }
}
