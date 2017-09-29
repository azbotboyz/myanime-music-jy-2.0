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
 * 资讯分类验证器
 * @package Channel
 */
class ArticleCategory extends Validate
{
    //验证规则
    protected $rule = [
        'title' => 'require|max:80|unique:ArticleCategory',
        'alias'   => 'alphaNum|max:100',
        'meta_title'   => 'max:50',
        'keywords'   => 'max:200',
        'description'   => 'max:200',
        'pid' => 'number',
    ];

    //提示信息
    protected $message  = [
        'title.require'  => '分类标题不能为空',
        'title.max'      => '分类标题最大长度80个字符',
        'title.unique'      => '分类称已存在',
        'alias.max'   => '分类别名最大长度100个字符',
        'alias.alphaNum'   => '分类别名只能为字母或数字',
        'pid.number'      => '上级分类称不存在',
        'meta_title.max'   => 'SEO标题最大长度50个字符',
        'keywords.max:200'   => 'SEO关键字最大长度200个字符',
        'description.max'   => 'SEO描述最大长度200个字符',
    ];

    protected $scene = [
        'edit'  =>  ['title'=>'require|max:60']
    ];
}
