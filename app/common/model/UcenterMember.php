<?php
/**
 * JYmusic音乐管理系统
 *
 * @version     2.0
 * @author      战神~~巴蒂 [jyuucn@163.com]
 * @license     http://jyuu.cn [未经授权严禁私自出售，否者承担法律责任]
 * @copyright   2014 - 2017 JYmusic
 */

namespace app\common\model;

use think\Model;

class UcenterMember extends Model
{
    /**
     * 写入用户手机号码
     */
    protected function setMobileAttr($mobile = null)
    {
        return !empty($mobile) ? $mobile : 0;
    }

    // 自动写入更新时间
    protected function setUpdateTimeAttr()
    {
        return time();
    }

    // 自动加密用户密码
    protected function setPasswordAttr($pwd = null)
    {
        return jy_ucenter_md5($pwd, UC_AUTH_KEY);
    }

    //写入状态
    protected function setStatusAttr($status)
    {
        return !empty($status) ? $status : 1;
    }

    //设置时间戳
    public function getRegTimeAttr($value)
    {
        return date('Y-m-d H:i', $value);
    }
    
    //设置时间戳
    public function getLastLoginTimeAttr($value)
    {
        return date('Y-m-d H:i', $value);
    }

    //设置Ip
    public function getLastLoginIpAttr($value)
    {
        return long2ip($value);
    }

    //设置Ip
    public function getRegIpAttr($value)
    {
        return long2ip($value);
    }

    /**
     * 用户资料
     * @return object
     */
    public function profile()
    {
        return $this->hasOne('Member', 'uid');
    }

    /**
     * 定义关联Member
     * @return object
     */
    public function member()
    {
        return $this->hasOne('Member', 'uid');
    }

    /**
     * 注册一个新用户
     * @param array $data
     * @return integer  注册成功-用户信息，注册失败-错误编号
     */
    public function register($data = [])
    {
        
        $data = [
            'username'  => $data['username'],
            'password'  => $data['password'],
            'email'     => $data['email'],
            'mobile'    => $data['mobile'],
            'reg_time'  => time(),
            'reg_ip'    => get_client_ip(1),
            'status'    => isset($data['status']) ? intval($data['status']) : 1
        ];

        /* 添加用户 */
        $user = self::create($data);
        return $user ? $user->id : 0; //0-未知错误，大于0-注册成功
    }

    /**
     * 用户登录认证
     * @param string  $username 用户名
     * @param string  $password 用户密码
     * @param integer $type     用户名类型 （1-用户名，2-邮箱，3-手机，4-UID）
     * @return integer          登录成功-用户ID，登录失败-错误编号
     */
    public function login($username, $password, $type = 1)
    {
        $map = array();
        switch ($type) {
        case 1:
            $map['username'] = $username;
            break;
        case 2:
            $map['email'] = $username;
            break;
        case 3:
            $map['mobile'] = $username;
            break;
        case 4:
            $map['id'] = $username;
            break;
        default:
            return 0; //参数错误
        }

        /* 获取用户数据 */
        $user = $this->where($map)->find();
        
        if ($user && $user->status > 0) {
            if ($user->status == 2) {
                return -3;
            }
            /* 验证用户密码 */
            if (jy_ucenter_md5($password, UC_AUTH_KEY) === $user['password']) {
                $this->updateLogin($user['id']); //更新用户登录信息
                return $user['id']; //登录成功，返回用户ID
            }
            return -2; //密码错误
        }
        return -1; //用户不存在或被禁用
    }

    /**
     * 注销当前用户
     * @return boolean
     */
    public function logout()
    {
        session('user_auth', null);
        session('user_auth_sign', null);
        return true;
    }

    /**
     * 获取用户信息
     * @param string  $uid         用户ID或用户名
     * @param boolean $is_username 是否使用用户名查询
     * @return array|int                用户信息
     */
    public function info($uid, $isUsername = false)
    {
        $map = $isUsername? ['username' => $uid] : ['id' => $uid];
        $user = self::with('profile')->where($map)->find();

        if (!$user || $user->status !== 1) {
            $this->error = 'The user does not exist or is disabled';
            return false;
        }
        $info = $user->toArray();

        $info['profile'] =$user->profile->parseData($info['profile']);
        $info = array_merge($info, $info['profile']);
        unset($info['profile']);
        return  $info;
    }

    /**
     * 检测用户信息
     * @param string  $field 用户名
     * @param integer $type  用户名类型 1-用户名，2-用户邮箱，3-用户电话
     * @return integer 错误编号
     */
    public function checkField($field, $type = 1)
    {
        $data = array();
        switch ($type) {
            case 1:
                $data['username'] = $field;
                break;
            case 2:
                $data['email'] = $field;
                break;
            case 3:
                $data['mobile'] = $field;
                break;
            default:
                return 0; //参数错误
        }
        return $this->create($data) ? 1 : $this->getError();
    }

    /**
     * 更新用户登录信息
     * @param integer $uid 用户ID
     * @return void
     */
    protected function updateLogin($uid)
    {
        $data = [
            'last_login_time' => time(),
            'last_login_ip' => get_client_ip(1)
        ];

        $this->save($data, ['id' => $uid]);
    }
    
    /**
     * 验证用户密码
     * @param int    $uid         用户id
     * @param string $password_in 密码
     * @return true 验证成功，false 验证失败
     */
    public function verifyPassword($uid, $passwordIn)
    {
        $password = $this->where('id', $uid)->value('password');
        if (jy_ucenter_md5($passwordIn, UC_AUTH_KEY) === $password) {
            return true;
        }
        return false;
    }

    /**
     * 重置密码
     * @param int    $uid      用户id
     * @param string $password 密码
     * @return false|int true 更新成功，false 更新失败
     */
    public function updateUserPassword($uid, $password)
    {
        if (empty($uid) || empty($password)) {
            $this->error = 'Parameter error!';
            return false;
        }
        //更新用户信息
        $data['password'] = jy_ucenter_md5($password, UC_AUTH_KEY);

        return $this->save($data, ['id' => $uid]);
    }
}
