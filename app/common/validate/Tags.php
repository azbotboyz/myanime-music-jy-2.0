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
 * 标签验证器
 * @package Channel
 */
class Tags extends Validate
{
    //验证规则
    protected $rule = [
        'name' => 'require|max:60|unique:Tags',
        'alias'   => 'alphaNum|max:100',
        'hits' => 'number',
        'group' => 'number',
    ];

    //提示信息
    protected $message  = [
        'name.require'  => '标签名称不能为空',
        'name.max'      => '标签名称最大长度60个字符',
        'name.unique'      => '标签名称已存在',
        'alias.max'   => '标签别名最大长度100个字符',
        'alias.alphaNum'   => '标签别名只能为字母或数字',
        'hits.number'  => '点击量只能为数字',
        'group.number'  => '标签分组选择不正确'
    ];

    protected $scene = [
        'edit'  =>  ['name'=>'require|max:60']
    ];
}
