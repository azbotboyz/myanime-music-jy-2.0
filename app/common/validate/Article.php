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
        'title.require'  => '标题不能为空',
        'title.max'      => '标题最大长度80个字符',
        'name.max'   => '标示最大长度100个字符',
        'name.alphaNum'   => '标示只能为字母或数字',
        'category_id.require'      => '请选择所属分类',
        'category_id.number'      => '分类不存在',
        'view.number'   => '浏览量只能为数字',
        'description.max'   => '描述最大长度140个字符',
        'deadline.regex'   => '截止日期格式不合法,请使用"年-月-日 时:分"格式,全部为数字',
    ];

    protected $scene = [
        'edit'  =>  ['title'=>'require|max:60']
    ];
}
