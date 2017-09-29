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

use think\Request;
use app\common\controller\BaseController;

class ApiController extends BaseController
{
    public function _initialize()
    {
        parent::_initialize();
        //验证来路
        /*$request = Request::instance();
        $referer = parse_url($request->header('referer'));
        $domain = $referer['scheme'] .'//'. $referer['host'];*/
        
    }
}
