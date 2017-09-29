<?php
/**
 * JYmusic音乐管理系统
 *
 * @version     2.0
 * @author      战神~~巴蒂 <jyuucn@163.com>
 * @license     http://jyuu.cn [未经授权严禁私自出售，否者承担法律责任]
 * @copyright   2014 - 2017 JYmusic
 */

namespace app\common\model;

use think\Model;

class Message extends Model
{
    protected $autoWriteTimestamp = true;
    
    protected $updateTime = false;
    
    protected $auto = ['post_uname', 'to_uname'];
    
    /** 过滤内容 */
    protected function setContentAttr($val)
    {
        return text_filter($val);
    }
    
    /** 自动写入发送者 */
    protected function setPostUnameAttr($val, $data)
    {
        return (new Member())->where('uid', $data['post_uid'])->value('nickname');
    }
    
    /** 自动写入接收者 */
    protected function setToUnameAttr($val, $data)
    {
        return (new Member())->where('uid', $data['to_uid'])->value('nickname');
    }
}
