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
        'name.require'     => 'Album name can not be empty',
        'name.length'      => 'The album name is 80 characters long',
        'artist_id.number'=> 'The artist does not exist',
        'add_uid.number' => 'The user does not exist',
        'hits.number' => 'Traffic can only be digitally',
        'favtimes.number' => 'Collections can only be digitized',
        'likes.number' => 'Like the number of only digital',
        'pub_time.length' => 'The release date is incorrect',
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
