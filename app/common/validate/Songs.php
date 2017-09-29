<?php
/**
 * JYmusic音乐管理系统 PHP version 5.4+
 *
 * @author    战神~~巴蒂 <jyuucn@163.com>
 * @version   2.0.0
 * @license   http://jyuu.cn [未经授权严禁私自出售，否者承担法律责任]
 * @copyright 2014 - 2017 JYmusic
 */
namespace app\common\validate;

use think\Validate;

//验证器
class Songs extends Validate
{

    //验证规则
    protected $rule = [
        'name' => 'require|max:120|checkName',
        'genre_id' => 'number',
        'artist_id' => 'number',
        'album_id' => 'number',
        'up_uid' => 'number',
        'rank_id' => 'number',
        'digg' => 'number',
        'bury' => 'number',
        'server_id' => 'number',
        'download' => 'number',
        'rater' => 'number',
        'listens' => 'number',
        'favtimes' => 'number',
        'likes' => 'number',
        'score' => 'number',
        'coin' => 'number',
        'sing' => 'length:2,100',
        'lyrics' => 'length:2,100',
        'composer' => 'length:2,100',
        'midi' => 'length:2,100',
        'mix' => 'length:2,100'
    ];

    //提示信息
    protected $message = [
        'name.require' => '请输入音乐名称',
        'name.max' => '音乐名称最多120个字符',
        'name.checkName' => '音乐名称已存在',
        'name.token' => '非法请求',
        'genre_id.require' => '请输选择歌曲分类',
        'genre_id.number' => '选择的分类不正确',
        'artist_id.require' => '请选择所属艺人',
        'artist_id.number' => '选择的艺人不正确',
        'album_id.require' => '请选择所属专辑',
        'album_id.number' => '选择的专辑不正确',
        'up_uid.require' => '选择上传的用户',
        'up_uid.number' => '选择的用户不正确',
        'digg.number' => '顶次数只能为数字',
        'bury.number' => '踩次数只能为数字',
        'server_id.number' => '选择的服务器不正确',
        'rank_id.number' => '选择榜单不正确',
        'download.number' => '下载次数只能为数字',
        'rater.number' => '评分只能为数字',
        'listens.number' => '试听次数只能为数字',
        'favtimes.number' => '收藏次数只能为数字',
        'likes.number' => '收藏次数只能为数字',
        'coin.number' => '所需金币只能为数字',
        'score.number' => '所需积分只能为数字',
        'sing.require' => '请填写原唱',
        'sing.length' => '音乐原唱字符长度为2-100个之间',
        'lyrics.require' => '请填写词作者',
        'lyrics.length' => '词作者字符长度为2-100个之间',
        'composer.require' => '请填写作曲',
        'composer.length' => '作曲字符长度为2-100个之间',
        'midi.require' => '请填写编曲',
        'midi.length' => '编曲字符长度为2-100个之间',
        'mix.require' => '请填写混编',
        'mix.length' => '混编字符长度为2-100个之间',
    ];

    /*定义验证场景*/
    protected $scene = [
        'user_create' => [
            'name' => 'require|max:120|checkName|token',
            'genre_id' => 'require|number',
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
        $map['status'] = ['>', -1];
        $map['up_uid'] = $data['up_uid'];
        
        if (isset($data['artist_id']) && !empty($data['artist_id'])) {
            $map['artist_id'] = $data['artist_id'];
        }
        
        if (isset($data['sing']) && !empty($data['sing'])) {
            $map['sing'] = $data['sing'];
        }

        $id = db('songs')->where($map)->value('id');
        if (empty($id)) {
            return true;
        }
        
        if (isset($data['id']) && $id == $data['id']) {
            return true;
        }
        
        return '歌曲名称已存在';
    }
}
