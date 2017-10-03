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
 * 资讯验证器
 * @package Channel
 */
class Article extends Validate
{
    //验证规则
    protected $rule = [
        'title' => 'require|max:80',
        'name'   => 'alphaNum|max:100',
        'category_id'   => 'require|number',
        'keywords'   => 'max:200',
        'description'   => 'max:140',
        'view' => 'number',
        'deadline'=>'regex:/^\d{4,4}-\d{1,2}-\d{1,2}(\s\d{1,2}:\d{1,2}(:\d{1,2})?)?$/',
    ];

    //提示信息
    protected $message  = [
        'title.require'  => 'The title can not be blank',
        'title.max'      => 'The maximum length of the title is 80 characters',
        'name.max'   => 'Mark the maximum length of 100 characters',
        'name.alphaNum'   => 'Markings can only be letters or numbers',
        'category_id.require'      => 'Please choose your category',
        'category_id.number'      => 'Classification does not exist',
        'view.number'   => 'Pageviews can only be numbers',
        'description.max'   => 'Description Maximum length 140 characters',
        'deadline.regex'   => 'The deadline is not legal, please use the "Year - Month - Day: Minute" format, all numbers',
    ];

    protected $scene = [
        'edit'  =>  ['title'=>'require|max:60']
    ];
}
