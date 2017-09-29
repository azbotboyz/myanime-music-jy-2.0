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
        'title.require' => '标题不能为空',
        'title.between' => '标题长度2-30个字符之间',
        'url.require'   => 'URL不能为空',
        'url.between'   => 'URl长度6-255个字符之间',
    ];
}
