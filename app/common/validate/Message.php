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
 * 消息验证器
 * @package Channel
 */
class Message extends Validate
{
    //验证规则
    protected $rule = [
        'post_uid' => 'require|number|token',
        'to_uid'   => 'require|number|checkTo',
        'reply_id'   => 'number',
        'content'   => 'require|max:255',
    ];

    //提示信息
    protected $message  = [
        'post_uid.require'  => '发送者不能为空',
        'post_uid.number'   => '发送者不正确',
        'post_uid.token'   => '非法提交',
        'to_uid.require'    => '接收者不能为空',
        'to_uid.number'     => '接收者不正确',
        'to_uid.checkTo'     => '接收用户不存在或被禁用',
        'reply_id.number'     => '回复的内容不存在',
        'content.require'   => '请填写发送内容',
        'content.max'       => '内容长度最多255个字符',
    ];
    
    protected function checkTo($val)
    {
        if (db('member')->where('uid', $val)->value('nickname')) {
            return true;
        }
        return false;
    }
    
}
