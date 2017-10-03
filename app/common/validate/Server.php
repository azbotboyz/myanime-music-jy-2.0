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
        'name.require'     => 'The server name can not be empty',
        'name.length'      => 'The server name is 50 characters long',
        'name.unique'      => 'The server already exists',
        'listen_url.require'=> 'Audition URL can not be empty',
        'listen_url.length' => 'Audition URl maximum length of 255 characters',
        'down_url.require'=> 'The download URL can not be empty',
        'down_url.length' => 'Download URl maximum length of 255 characters',
        'image_url.require'=> 'Image URL can not be empty',
        'image_url.length' => 'Image URl maximum length of 255 characters',
    ];

    protected $scene = [
        'edit'  =>  ['name'=>'require|max:50'],
    ];
}
