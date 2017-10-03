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
 * 用户组验证器
 * @package Channel
 */
class MemberGroup extends Validate
{
    //验证规则
    protected $rule = [
        'name' => 'require|max:60|unique:Member',
        'alias'   => 'alphaNum|max:100'
    ];

    //提示信息
    protected $message  = [
        'name.require'  => 'The label name can not be empty',
        'name.max'      => 'The label name has a maximum length of 60 characters',
        'name.unique'      => 'The tag name already exists',
        'alias.max'   => 'The label alias has a maximum length of 100 characters',
        'alias.alphaNum'   => 'Tag aliases can only be letters or numbers'
    ];

    protected $scene = [
        'edit'  =>  ['name'=>'require|max:60']
    ];
}
