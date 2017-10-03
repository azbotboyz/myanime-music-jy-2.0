<?php
/**
 * JYmusic音乐管理系统
 *
 * @version     2.0
 * @author      战神~~巴蒂 [jyuucn@163.com]
 * @license     http://jyuu.cn [未经授权严禁私自出售，否者承担法律责任]
 * @copyright   2014 - 2017 JYmusic
 */

namespace app\common\validate;

use think\Validate;

/**
 * 导航验证器
 * @package Channel
 */
class Channel extends Validate
{
    //验证规则
    protected $rule = [
        'title' => 'require|max:30',
        'url'   => 'require|max:255',
        'sort' => 'number'
    ];

    //提示信息
    protected $message  = [
        'title.require'     => 'The title can not be blank',
        'title.length'      => 'The maximum length of the title is 30 characters',
        'url.require'       => 'URL can not be empty',
        'url.length'        => 'URl has a maximum length of 255 characters',
        'sort.number'       => 'Sorting can only be a number',
    ];
}
