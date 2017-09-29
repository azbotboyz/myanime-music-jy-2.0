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
        'nickname.require' 	=> '用户昵称不能为空',
        'nickname.length'  => '用户昵称长度2-16个字符之间',
        'nickname.unique'   => '用户昵称已被占用',
        'nickname.checkDenyNick' => '昵称被禁止使用',
        'sex.number'  => '性别选择不正确',
        'sex.max'  => '性别选择不正确',
        'qq.number'  => '填写的qq号码不正确',
        'qq.unique'  => 'qq号码已存在',
        'birthday.date'  => '生日格式不正确',
        'coin.number'  => '金币设置不正确',
        'coin.length'  => '金币数量1-11位之间',
        'score.number'  => '积分数量设置不正确',
        'score.length'  => '金币数量1-11位之间',
        'fans.number'  => '粉丝数量设置不正确', 
        'fans.length'  => '粉丝数量1-11位之间',
        'follows.number'  => '关注数量设置不正确',
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
