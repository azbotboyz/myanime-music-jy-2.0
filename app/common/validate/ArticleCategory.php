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
        'title.require'  => 'Category title can not be empty',
        'title.max'      => 'The maximum length of the classification header is 80 characters',
        'title.unique'      => 'The classification says it already exists',
        'alias.max'   => 'Classification The maximum length of the alias is 100 characters',
        'alias.alphaNum'   => 'Category aliases can only be letters or numbers',
        'pid.number'      => 'Superior classification does not exist',
        'meta_title.max'   => 'SEO title maximum length of 50 characters',
        'keywords.max:200'   => 'SEO keyword maximum length of 200 characters',
        'description.max'   => 'SEO describes the maximum length of 200 characters',
    ];

    protected $scene = [
        'edit'  =>  ['title'=>'require|max:60']
    ];
}
