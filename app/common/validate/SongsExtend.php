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
        'listen_url.require' => '请输入试听地址',
        'listen_url.max' => '试听地址最多255个字符',
        'listen_file_id.number' => '试听文件不正确',
        'listen_file_size.max' => '试听文件大小最多16个字符',
        'listen_bitrate.max' => '试听音质最多10个字符',
        'down_url.require' => '请输入下载地址',
        'down_url.url' => '下载地址最多255个字符',
        'down_url.max' => '下载地址最多255个字符',
        'down_file_id.number' => '下载文件不正确',
        'down_bitrate.max' => '下载音质最多10个字符',
        'disk_url.max' => '网盘地址最多255个字符',
        'disk_url.url' => '网盘地址格式错误',
        'disk_pass.max:10' => '网盘密码最多10个字符',
        'down_file_size.max' => '下载文件大小最多16个字符',
        'play_time.max' => '播放时长最多10个字符'
    ];
}
