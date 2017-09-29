<?php
/**
 * JYmusic音乐管理系统
 *
 * @version   2.0
 * @author    战神~~巴蒂 <jyuucn@163.com>
 * @license   http://jyuu.cn [未经授权严禁私自出售，否者承担法律责任]
 * @copyright 2014 - 2017 JYmusic
 */

namespace app\api\controller\v1;

class IndexController extends ApiController
{
    public function index()
    {
        return json(['code' => 40404, 'error' => '404 not found'], 404);
    }
}
