<?php
/**
 * JYmusic音乐管理系统 PHP version 5.4+
 *
 * @author    战神~~巴蒂 <jyuucn@163.com>
 * @version   2.0.0
 * @license   http://jyuu.cn [未经授权严禁私自出售，否者承担法律责任]
 * @copyright 2014 - 2017 JYmusic
 */

namespace app\common\validate;

use think\Validate;

/**
 * 验证器
 * @package Menu
 */
class Menu extends Validate
{
    //验证规则
    protected $rule = [
        'title' => 'require|length:2,30',
        'url'   => 'require|length:2,255',
    ];

    //提示信息
    protected $message  =[
        'title.require' => 'The title can not be blank',
        'title.between' => 'Title Length 2-30 characters between',
        'url.require'   => 'URL can not be empty',
        'url.between'   => 'URl length 6-255 characters between',
    ];
}
