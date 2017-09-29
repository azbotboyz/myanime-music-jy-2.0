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
 * 服务器组验证器
 * @package Server
 */
class Server extends Validate
{
    //验证规则
    protected $rule = [
        'name' => 'require|max:50|unique:Server',
        'listen_url'   => 'require|max:255',
        'down_url'   => 'require|max:255',
        'image_url'   => 'require|max:255',
    ];

    //提示信息
    protected $message  = [
        'name.require'     => '服务器名称不能为空',
        'name.length'      => '服务器名称大长度50个字符',
        'name.unique'      => '服务器已存在',
        'listen_url.require'=> '试听URL不能为空',
        'listen_url.length' => '试听URl最大长度255个字符',
        'down_url.require'=> '下载URL不能为空',
        'down_url.length' => '下载URl最大长度255个字符',
        'image_url.require'=> '图片URL不能为空',
        'image_url.length' => '图片URl最大长度255个字符',
    ];

    protected $scene = [
        'edit'  =>  ['name'=>'require|max:50'],
    ];
}
