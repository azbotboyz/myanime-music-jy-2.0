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
class SeoRule extends Validate
{
    //验证规则
    protected $rule = [
        'title' => 'require|max:200|unique:SeoRule',
        'title_rule'   => 'require|max:200',
        'keywords_rule' => 'require|max:300',
        'description_rule' => 'require|max:300',
    ];

    //提示信息
    protected $message  = [
        'title.require'  => 'The rule title can not be empty',
        'title.max'      => 'Rule Title Maximum length of 200 characters',
        'title.unique'      => 'The rule title already exists',
        'title_rule.require'   => 'The title rule can not be empty',
        'title_rule.max'   => 'The title rule has a maximum length of 200 characters',
        'keywords_rule.require'   => 'Keyword rules can not be empty',
        'keywords_rule.max'   => 'The maximum length of the keyword rule is 200 characters',
        'description_rule.require'   => 'The description rule can not be empty',
        'description_rule.max'   => 'Description Rule Maximum length of 200 characters',
    ];

    protected $scene = [
        'edit'  =>  [
            'title'=>'require|max:200',
            'title_rule'   => 'require|max:200',
            'keywords_rule' => 'require|max:300',
            'description_rule' => 'require|max:300',
        ]
    ];
}
