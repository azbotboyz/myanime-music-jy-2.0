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
 * 专辑类型验证器
 * @package Channel
 */
class AlbumType extends Validate
{
    //验证规则
    protected $rule = [
        'name' => 'require|max:20|unique:AlbumType',
        'alias'   => 'alphaNum|max:30',
    ];

    //提示信息
    protected $message  = [
        'name.require'  => '专辑类型名称不能为空',
        'name.max'      => '专辑类型最大长度20个字符',
        'name.unique'      => '专辑类型已存在',
        'alias.max'   => '专辑类型最大长度30个字符',
        'alias.alpha'   => '专辑类型只能为字母或数字',
    ];

    protected $scene = [
        'edit'  =>  ['name'=>'require|max:20']
    ];
}
