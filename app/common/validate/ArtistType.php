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
 * 艺人类型验证器
 * @package Channel
 */
class ArtistType extends Validate
{
    //验证规则
    protected $rule = [
        'name' => 'require|max:20|unique:ArtistType',
        'alias'   => 'alphaNum|max:30',
    ];

    //提示信息
    protected $message  = [
        'name.require'  => 'The label name can not be empty',
        'name.max'      => 'The label name has a maximum length of 60 characters',
        'alias.max'   => 'The label alias has a maximum length of 100 characters',
        'alias.alpha'   => 'Tag aliases can only be letters or numbers',
    ];

    protected $scene = [
        'edit'  =>  ['name'=>'require|max:60']
    ];
}
