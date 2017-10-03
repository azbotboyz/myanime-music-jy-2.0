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
use app\services\Idcard;

/**
 * 标签验证器
 * @package Channel
 */
class AuthMusician extends Validate
{
    //验证规则
    protected $rule = [
        'artist_name' => 'require|max:60|checkArtistName|token',
        'type' => 'require|number',
        'realname' => 'require|max:20|checkUpdateName',
        'idcard' => 'require|length:15,18|isIdcard',
        'phone'   => 'require|isMobile',
        'idcard_img1' => 'require|max:255',
        'reason' => 'require|length:10,200'
    ];

    //提示信息
    protected $message  = [
        'artist_name.require'  => 'Please fill in the name of the artist',
        'artist_name.max'      => 'The name of the artist name exceeds the limit',
        'artist_name.checkArtistName'  => 'The artist name already exists',
        'artist_name.token'   => 'Illegal request',
        'type.require'  => 'Please select a certified artist type',
        'type.number'  => 'Choose the type of music is not correct',
        
        'realname.require'  => 'Please fill in your real name',
        'realname.max'      => 'Real name length exceeds limit',
        'realname.checkUpdateName'   => 'Sorry that the musician has been certified',
        'phone.require'   => 'Please fill in the contact phone',
        'phone.isMobile'   => 'Please fill in the correct contact number',
        'idcard.require'  => 'please enter your ID number',
        'idcard.length'  => 'Please enter a 15 or 18 ID number',
        'idcard_img1.require'  => 'Please upload your ID card positive photos',
        'idcard_img1.max'  => 'Please upload your ID card positive photos',
        'reason.require'  => 'Please fill in the certification reasons',
        'reason.max'  => 'Reason for length 10-200 characters'
    ];

    protected $scene = [
        'edit'  =>  ['name'=>'require|max:60']
    ];
    
    /**
     * 验证手机号是否正确
     */
    protected function isMobile($mobile)
    {
        if (!is_numeric($mobile)) {
            return false;
        }
        return preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#', $mobile) ? true : false;
    }
    
    /** 验证艺人是否存在 */
    protected function checkArtistName($val)
    {
        $id = db('artist')->where('name', $val)->value('id');
        if ($id) {
            return 'The artist name already exists';
        }
        return true;
    }
    
    /** 验证用户修改时用户名是否存在 */
    protected function checkUpdateName($val, $rule, $data)
    {
        $map['realname'] = $val;
        $map['idcard'] = $data['idcard'];
        $uid = db('auth_musician')->where($map)->value('uid');
        
        if ($uid && (intval($uid) !== intval($data['uid']))) {
            return false;
        }
        return true;
    }
    
    /** 验证身份证号码 */
    protected function isIdcard($val){
        $card = new Idcard();
        $card ->setId($val);
        return $card->isValidate()? true : 'ID number is incorrect!';
    }
}
