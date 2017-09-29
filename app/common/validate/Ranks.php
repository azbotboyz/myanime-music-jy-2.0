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
        'name.require'  => '榜单名称不能为空',
        'name.max'      => '榜单名称最大长度20个字符',
        'name.name'      => '榜单名称已存在',
        'alias.max'   => '榜单别名最大长度30个字符',
        'alias.alphaNum' => '榜单别名只能为字母或数字',
        'is_sys.number'  => '参数错误',
    ];

    protected $scene = [
        'edit'  =>  ['name'=>'require|max:40']
    ];
}
