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

use app\common\model\Ranks;

class RanksController extends HomeController
{

    /**
     * 分类首页
     * @return \think\Response
     */
    public function index()
    {
        return $this->fetch();
    }

    /**
     * @param mixed $name
     * @return \think\Response
     */
    public function read($name = null)
    {
        $info          = [];
        $map['status'] = 1;
        if (is_numeric($name) && $name > 0) {
            $info = Ranks::get($map);
        } elseif (ctype_alnum($name)) {
            $map['alias'] = $name;
            $info         = Ranks::get($map);
        }

        if (empty($info)) {
            abort(404,'The list you are viewing does not exist!');
        }
        return $this->fetch('detail', ['data' => $info->parseData()]);
    }
}
