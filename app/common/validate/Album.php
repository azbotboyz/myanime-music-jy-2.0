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
 * 专辑验证器
 * @package Server
 */
class Album extends Validate
{
    //验证规则
    protected $rule = [
        'name' => 'require|max:80|checkName',
        'type_id'   => 'number',
        'artist_id'   => 'number',
        'add_uid'   => 'number',
        'hits'   => 'number',
        'favtimes'   => 'number',
        'likes'   => 'number',
        'pub_time'   => 'max:120',
    ];

    //提示信息
    protected $message  = [
        'name.require'     => '专辑名称不能为空',
        'name.length'      => '专辑名称大长度80个字符',
        'artist_id.number'=> '所属艺术家不存在',
        'add_uid.number' => '所属用户不存在',
        'hits.number' => '点击量只能位数字',
        'favtimes.number' => '收藏数只能位数字',
        'likes.number' => '喜欢数只能位数字',
        'pub_time.length' => '发布日期不正确',
    ];
    
    /*定义验证场景*/
    protected $scene = [
        'user_create' => [
            'name' =>'require|max:80|token|checkName',
            'type_id'   => 'require|number',
            'artist_id',
            'add_uid',
            'hits',
            'favtimes',
            'likes',
            'pub_time'   => 'max:120'
        ]
    ];
    
    /**
     * 检测名称是否存在
     * @param $name
     * @param $rule
     * @param $data
     * @return bool|string
     */
    protected function checkName($name, $rule, $data)
    {
        //当歌手不同并且用户不同视为不同音乐
        $map['name'] = text_filter($name);
        $map['status'] = ['>', 0];
        
        if (isset($data['artist_id']) && !empty($data['artist_id'])) {
            $map['artist_id'] = $data['artist_id'];
        }
        
        
        $id = db('album')->where($map)->value('id');
        if (empty($id)) {
            return true;
        }
        
        if (isset($data['id']) && $id == $data['id']) {
            return true;
        }
        return '专辑已存在';
    }
}
