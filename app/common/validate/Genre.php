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
class Genre extends Validate
{
    //验证规则
    protected $rule = [
        'name' => 'require|max:40|unique:Genre',
        'cover_url'   => 'max:255',
        'sort' => 'number'
    ];

    //提示信息
    protected $message  = [
        'name.require'     => '音乐分类名称不能为空',
        'name.length'      => '音乐分类最大长度40个字符',
        'name.unique'      => '音乐分类名称已存在',
        'sort.number'       => '排序只能是数字',
        'cover_url.length'  => '封面URl最大长度255个字符',
    ];

    protected $scene = [
        'edit'  =>  [
            'name'=>'require|max:60'
        ]
    ];
}
