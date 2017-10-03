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
        'name.require' => 'Please enter the music name',
        'Name.max' => 'music name up to 120 characters',
        'Name.checkName' => 'Music name already exists'
        'Name.token' => 'illegal request',
        'Genre_id.require' => 'Please enter the song classification'
        'Genre_id.number' => 'selected category is not correct',
        'Artist_id.require' => 'Please choose the artist'
        'Artist_id.number' => 'selected artist is not correct'
        'Album_id.require' => 'Please select your album',
        'Album_id.number' => 'selected album is not correct',
        'Up_uid.require' => 'Select Uploaded User'
        'Up_uid.number' => 'selected user is incorrect',
        'Digg.number' => 'top number can only be number',
        'Bury.number' => 'step number can only be number',
        'Server_id.number' => 'selected server is incorrect',
        'Rank_id.number' => 'select list is incorrect',
        'Download.number' => 'downloads can only be numbers'
        'Rater.number' => 'Score only for numbers',
        'Listens.number' => 'audits can only be numbers',
        'Favtimes.number' => 'collections can only be numbers',
        'Likes.number' => 'Collections can only be numbers',
        'Coin.number' => 'required gold coins can only be numbers'
        'Score.number' => 'The required points can only be numbers'
        'Sing.require' => 'Please fill in the original song',
        'Sing.length' => 'music original character length between 2-100',
        'Lyrics.require' => 'Please fill in the word author',
        'Lyrics.length' => 'word author character length between 2-100',
        'Composer.require' => 'Please fill in the composition',
        'Composer.length' => 'Compose character length between 2-100',
        'Midi.require' => 'Please fill in arranger'
        'Midi.length' => 'Arranged character length between 2-100'
        'Mix.require' => 'Please fill in the mix'
        'Mix.length' => 'mixed character length between 2-100',
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
        
        return 'The song name already exists';
    }
}
