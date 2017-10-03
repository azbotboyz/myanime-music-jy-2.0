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
 * 艺人验证器
 * @package Server
 */
class Artist extends Validate
{
    //验证规则
    protected $rule = [
        'name' => 'require|max:40',
        'type_id'   => 'number',
        'alias'   => 'alphaNum|max:60',
        'uid'   => 'number',
        'hits'   => 'number',
        'favtimes'   => 'number',
        'likes'   => 'number',
        'pub_time'   => 'max:200',
    ];

    //提示信息
    protected $message  = [
        'name.require'     => 'The artist name can not be empty',
        'name.length'      => 'Artist name long length 40 characters',
        'type_id.number'=> 'The type is not correct',
        'alias.alphaNum'=> 'Indexes can only be letters or numbers',
        'alias.max'=> 'Indexes can only be single characters',
        'uid.number' => 'The user does not exist',
        'hits.number' => 'Traffic can only be digitally',
        'favtimes.number' => 'Collections can only be digitized',
        'likes.number' => 'Like the number of only digital',
        'pub_time.length' => 'The release date is incorrect',
    ];
}
