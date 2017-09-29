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
        'name.require'     => '艺人名称不能为空',
        'name.length'      => '艺人名称大长度40个字符',
        'type_id.number'=> '所属类型不正确',
        'alias.alphaNum'=> '索引只能为字母或数字',
        'alias.max'=> '索引只能为单个字符',
        'uid.number' => '所属用户不存在',
        'hits.number' => '点击量只能位数字',
        'favtimes.number' => '收藏数只能位数字',
        'likes.number' => '喜欢数只能位数字',
        'pub_time.length' => '发布日期不正确',
    ];
}
