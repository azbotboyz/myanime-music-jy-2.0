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
        'post_uid.require'  => 'The sender can not be empty',
        'post_uid.number'   => 'The sender is not correct',
        'post_uid.token'   => 'Illegal submission',
        'to_uid.require'    => 'The receiver can not be empty',
        'to_uid.number'     => 'The recipient is not correct',
        'to_uid.checkTo'     => 'The receiving user does not exist or is disabled',
        'reply_id.number'     => 'The reply does not exist',
        'content.require'   => 'Please fill in the contents',
        'content.max'       => 'Content length up to 255 characters',
    ];
    
    protected function checkTo($val)
    {
        if (db('member')->where('uid', $val)->value('nickname')) {
            return true;
        }
        return false;
    }
    
}
