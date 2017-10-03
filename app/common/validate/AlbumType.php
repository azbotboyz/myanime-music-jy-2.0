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
        'name.require'  => 'The album type name can not be empty',
        'name.max'      => 'The maximum length of the album type is 20 characters',
        'name.unique'      => 'The album type already exists',
        'alias.max'   => 'The maximum length of the album type is 30 characters',
        'alias.alpha'   => 'The album type can only be a letter or a number',
    ];

    protected $scene = [
        'edit'  =>  ['name'=>'require|max:20']
    ];
}
