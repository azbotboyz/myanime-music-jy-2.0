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
 * 榜单验证器
 * @package Channel
 */
class Ranks extends Validate
{
    //验证规则
    protected $rule = [
        'name' => 'require|max:20|unique:Ranks',
        'alias'   => 'alphaNum|max:30',
        'is_sys' => 'number'
    ];

    //提示信息
    protected $message  = [
        'name.require'  => 'The list name can not be empty',
        'name.max'      => 'The maximum length of the list name is 20 characters',
        'name.name'      => 'The name of the list already exists',
        'alias.max'   => 'The maximum length of the list alias is 30 characters',
        'alias.alphaNum' => 'List aliases can only be letters or numbers',
        'is_sys.number'  => 'Parameter error',
    ];

    protected $scene = [
        'edit'  =>  ['name'=>'require|max:40']
    ];
}
