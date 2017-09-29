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
 * 资讯内容验证器
 * @package Channel
 */
class ArticleContent extends Validate
{
    //验证规则
    protected $rule = [
        'content' => 'require',
        'template'   => 'require',
    ];

    //提示信息
    protected $message  = [
        'content.require'  => '资讯内容不能为空',
        'template.require'      => '模板不能为空',
    ];
}
