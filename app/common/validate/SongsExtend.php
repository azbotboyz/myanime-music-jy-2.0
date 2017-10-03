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
class SongsExtend extends Validate
{

    //验证规则
    protected $rule = [
        'listen_url' => 'require|max:255',
        'listen_file_id' => 'number',
        'listen_file_size' => 'max:16',
        'listen_bitrate' => 'max:10',
        'down_url' => 'max:255',
        'down_file_id' => 'number',
        'down_file_size' => 'max:16',
        'down_bitrate' => 'max:10',
        'disk_url' => 'max:255|url',
        'disk_pass' => 'max:10',
        'play_time' => 'max:10',
    ];

    //提示信息
    protected $message = [
        'listen_url.require' => 'Please enter the audition address',
        'Listen_url.max' => 'listen to address up to 255 characters',
        'Listen_file_id.number' => 'audition file is incorrect',
        'Listen_file_size.max' => 'audition file size up to 16 characters',
        'Listen_bitrate.max' => 'audition sound up to 10 characters',
        'Down_url.require' => 'Please enter the download address',
        'Down_url.url' => 'download address up to 255 characters',
        'Down_url.max' => 'download address up to 255 characters',
        'Down_file_id.number' => 'Download file is incorrect',
        'Down_bitrate.max' => 'download sound quality up to 10 characters',
        'Disk_url.max' => 'network address up to 255 characters',
        'Disk_url.url' => 'network disk address format error',
        'Disk_pass.max: 10' => 'network password up to 10 characters',
        'Down_file_size.max' => 'download file size up to 16 characters',
        'Play_time.max' => 'play time up to 10 characters'
    ];
}
