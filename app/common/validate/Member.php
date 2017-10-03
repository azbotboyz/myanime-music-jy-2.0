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


//UcenterMember 验证器
class Member extends Validate{

	//验证规则
	protected $rule 			=   [
        'nickname'  => 'require|length:2,16|checkDenyNick|unique:Member',
        'sex'       => 'number|max:1',
        'birthday'  => 'date',
        'location'  => 'length:4,200',
        'qq'        => 'number|unique:Member',
        'hits'      => 'number|length:1,11',
        'score'     => 'number|length:1,11',
        'fans'      => 'number|length:1,11',
        'follows'   => 'number|length:1,11',
        'coin'      => 'number|length:1,11',
    ];
	
	//提示信息
    protected $message  		=   [
        'nickname.require' 	=> 'User nickname can not be empty',
        'nickname.length'  => 'User nickname length 2-16 characters between',
        'nickname.unique'   => 'User nickname is already occupied',
        'nickname.checkDenyNick' => 'Nickname is forbidden',
        'sex.number'  => 'Gender selection is incorrect',
        'sex.max'  => 'Gender selection is incorrect',
        'qq.number'  => 'Fill in the qq number is not correct',
        'qq.unique'  => 'qq number already exists',
        'birthday.date'  => 'Birthday format is not correct',
        'coin.number'  => 'Gold is not set correctly',
        'coin.length'  => 'Number of coins 1-11 between',
        'score.number'  => 'The number of points is not set correctly',
        'score.length'  => 'Number of coins 1-11 between',
        'fans.number'  => 'The number of fans is not set correctly', 
        'fans.length'  => 'Number of fans 1-11 between',
        'follows.number'  => 'The number of concerns is not set correctly',
    ];

    /*定义验证场景*/
    protected $scene = [
        'create' => [
            'nickanme',
            'sex'
        ],
        'edit' => [
            'nickname' => 'require|length:2,16|checkDenyNick|checkUpdateNickname',
            'qq' => 'number|checkUpdateQq',
            'sex',
            'hits',
            'score',
            'fans',
            'follows',
            'coin'
        ],
        'user_edit'=> [
            'nickname' => 'require|length:2,16|checkDenyNick|checkUpdateNickname|token',
            'qq' => 'number|checkUpdateQq',
            'sex',
        ],
        'login' => [
            'username' => 'require|length:4,30',
            'password',
            'verify'
        ],
    ];

    protected function checkDenyNick($val)
    {
        $char = config('site_ban_char');
        $char = explode(',', $char);
        return !in_array($val, $char);
    }

    /**
     * 验证用户修改时用户名是否存在
     */
    protected function checkUpdateNickname($value, $rule, $data)
    {
        $uid = db('Member')->where('nickname', $value)->value('uid');
        if ($uid && ($uid != $data['uid'])) {
            return false;
        }
        return true;
    }
    
    /**
     * 验证用户修改时qq是否存在
     */
    protected function checkUpdateQq($value, $rule, $data)
    {
        $uid = db('Member')->where('qq', $value)->value('uid');
        if ($uid && ($uid != $data['uid'])) {
            return false;
        }
        return true;
    }
	
}
