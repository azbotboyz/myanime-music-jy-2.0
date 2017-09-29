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
        'artist_name.require'  => '请填写音乐人名称',
        'artist_name.max'      => '音乐人名称长度超出限制',
        'artist_name.checkArtistName'  => '音乐人名称已存在',
        'artist_name.token'   => '非法请求',
        'type.require'  => '请选择认证的音乐人类型',
        'type.number'  => '选择音乐人类型不正确',
        
        'realname.require'  => '请填写真实姓名',
        'realname.max'      => '真实姓名长度超出限制',
        'realname.checkUpdateName'   => '抱歉该音乐人已经认证过了',
        'phone.require'   => '请填写联系手机',
        'phone.isMobile'   => '请填写正确联系手机号码',
        'idcard.require'  => '请填写身份证号码',
        'idcard.length'  => '请输入15或18位身份证号码',
        'idcard_img1.require'  => '请上传身份证正面照片',
        'idcard_img1.max'  => '请上传身份证正面照片',
        'reason.require'  => '请填写认证理由',
        'reason.max'  => '认真理由长度10-200个字符'
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
            return '音乐人名称已存在';
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
        return $card->isValidate()? true : '身份证号码不正确！';
    }
}
