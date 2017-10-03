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
        'name.require'     => 'The music classification name can not be empty',
        'name.length'      => 'The maximum length of the music classification is 40 characters',
        'name.unique'      => 'The music classification name already exists',
        'sort.number'       => 'Sorting can only be a number',
        'cover_url.length'  => 'Cover URl maximum length of 255 characters',
    ];

    protected $scene = [
        'edit'  =>  [
            'name'=>'require|max:60'
        ]
    ];
}
