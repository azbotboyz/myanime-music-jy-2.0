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
class UcenterMember extends Validate
{
    protected $uid = 0;

    //验证规则
    protected $rule = [
        'username' => 'require|length:4,16|checkDenyMember|unique:UcenterMember',
        'password' => 'require|length:5,32',
        'repassword'=>'require|confirm:password',
        'email' => 'email|length:6,40|checkDenyEmail|unique:UcenterMember',
        'mobile' => 'isMobile|checkDenyMobile|unique:UcenterMember',
        'nickname' => 'length:2,16|unique:Member',
        'verify' => 'require|captcha',
        //'verify'            => 'require|captcha|token', 有表单令牌
    ];

    //提示信息
    protected $message = [
        'username.require' => 'Please enter user name',
        'Username.length' => 'User name length between 4-16 characters',
        'Username.checkDenyMember' => 'username is forbidden use'
        'Username.unique' => 'username has been occupied',
        'Username.checkUpdateUname' => 'username has been occupied',
        'Password.require' => 'Please enter a password',
        'Password.length' => 'password length between 6-30 characters',
        'Repassword.require' => 'Please enter the confirmation password',
        'Repassword.length' => 'Confirm password length between 5 and 30 characters'
        'Repassword.confirm' => 'Confirm that the password is not consistent',
        'Oldpassword.require' => 'Please enter password verification',
        'Oldpassword.length' => 'password length between 6-30 characters',
        'Email.require' => 'Please enter email address'
        'Email.email' => 'mailbox format error',
        'Email.length' => 'mailbox length between 6 and 40 characters'
        'Email.unique' => 'mailbox has been occupied',
        'Email.checkDenyEmail' => 'mailbox is forbidden to register'
        'Email.ceckUpdateEmail' => 'mailbox has been occupied',
        'Mobile.require' => 'Please enter the phone number',
        'Mobile.isMobile' => 'phone number malformed',
        'Mobile.unique' => 'Mobile phone number is occupied',
        'Mobile.checkDenyMobile' => 'phone number disabled'
        'Mobile.ceckUpdateMobile' => 'mobile phone number is occupied',
        'Nickname.require' => 'Please enter nickname',
        'Nickname.length' => 'Nickname length between 2-16 characters',
        'Nickname.unique' => 'Nickname already occupied'
        'Verify.require' => 'Please enter a verification code',
        'Verify.captcha' => 'Verification code input error',
        'Verify.token' => 'illegal request',
        'Username.token' => 'illegal request',
        'Email.token' => 'illegal request',
        'Mobile.token' => 'illegal request',
        'Vcode.require' => 'Please enter the verification code',
        'Vcode.number' => 'Verification code can only be numeric'
        'Vcode.length' => 'Verification code length can only be 6 digits',
    ];

    /*定义验证场景*/
    protected $scene = [
        'register' => [
            'username' => 'require|length:4,30|unique:UcenterMember',
            'nickname',
            'password',
            'repassword',
            'email',
            'mobile',
        ],
        'create' => [
            'username',
            'password',
            'email',
            'mobile'
        ],
        'admin_edit' => [
            'username' => 'require|length:4,30|checkDenyMember|checkUpdateUname',
            'password' => 'length:6,30',
            'email'    => 'email|length:6,40|checkDenyEmail|checkUpdateEmail',
            'mobile' => 'isMobile|checkDenyMobile|checkUpdateMobile'
        ],
        'edit' => [
            'username' => 'require|length:4,30|checkDenyMember|checkUpdateUname|token',
            'password' => 'length:6,30',
            'email'    => 'email|length:6,40|checkDenyEmail|checkUpdateEmail',
            'mobile' => 'isMobile|checkDenyMobile|checkUpdateMobile'
        ],
        'edit_need_pwd' => [
            'username' => 'length:4,30|checkDenyMember|checkUpdateUname',
            'oldpassword' => 'require|length:4,30',
            'password' => 'length:6,30',
        ],
        'admin_login' => [
            'username' => 'require|length:4,30',
            'password'=> 'require|length:4,30',
            'verify'
        ],
        'login' => [
            'username' => 'require|length:4,30',
            'password'=> 'require|length:4,32',
            //'verify'
        ],
        'activate' => [
            'email'    => 'require|email|length:6,40|token',
            'verify',
        ],
        'rest_pwd' => [
            'email'    => 'email|length:6,40|token',
            'vcode'     => 'require|number|length:6',
            'password' => 'require|length:5,32',
            'repassword'=>'require|length:5,32|confirm:password'
        ],
        'login_for_email' => [
            'email' => 'require|email|length:6,40|token',
            'password'=> 'require|length:6,30',
            //'verify'
        ],
        'login_for_mobile' => [
            'mobile' => 'require|isMobile|token',
            'password'=> 'require|length:6,30',
            //'verify'
        ],
        'validate_username' => [
            'username' => 'require|length:4,16|unique:UcenterMember',
        ],
        'validate_email' => [
            'email' => 'require|email|length:4,30|unique:UcenterMember',
        ],
        'validate_mobile' => [
            'mobile' => 'require|isMobile|length:4,30|unique:UcenterMember'
        ],
        'validate_nickname' => [
            'nickname' => 'require|isMobile|length:2,16|unique:Member'
        ],
    ];

    /**
     * 检测用户名是不是被禁止注册
     */
    protected function checkDenyMember($val)
    {
        $char = config('site_ban_char');
        $char = explode(',', $char);
        return !in_array($val, $char);
    }

    /**
     * 检测邮箱是不是被禁止注册
     */
    protected function checkDenyEmail($value)
    {
        //暂不限制，下一个版本完善
        return true;
    }

    /**
     * 检测手机是不是被禁止注册
     */
    protected function checkDenyMobile($value)
    {
        //暂不限制，下一个版本完善
        return true;
    }

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

    /**
     * 验证用户修改时用户名是否存在
     */
    protected function checkUpdateUname($value, $rule, $data)
    {
        return $this->isOnlyUser(['username' => $value], $data['id']);
    }

    /**
     * 验证用户修改时邮箱是否存在
     */
    protected function checkUpdateEmail($value, $rule, $data)
    {
        return $this->isOnlyUser(['email' => $value], $data['id']);

    }

    /**
     * 验证用户修改时手机号是否存在
     */
    protected function checkUpdateMobile($value, $rule, $data)
    {
        return $this->isOnlyUser(['mobile' => $value], $data['id']);
    }

    /**
     * 判断用户唯一
     * @param  array  $map
     * @param  array  $data
     * @return boolean
     */
    protected function isOnlyUser($map, $id)
    {
        if (!$this->uid) {
            $this->uid = db('UcenterMember')->where($map)->value('id');
        }

        if ($this->uid && ($this->uid != $id)) {
            return false;
        }
        return true;
    }
}
