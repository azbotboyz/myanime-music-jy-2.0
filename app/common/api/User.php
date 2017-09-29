<?php
/**
 * JYmusic音乐管理系统 PHP version 5.4+
 *
 * @version     2.0
 * @author      战神~~巴蒂 <jyuucn@163.com>
 * @license     http://jyuu.cn/license [未经授权严禁私自出售，否者承担法律责任]
 * @copyright   2014 - 2017 JYmusic
 */
namespace app\common\api;

use think\Cache;
use think\Loader;
use app\common\model\Member;
use app\common\model\UcenterMember;

class User
{
    protected $model;
    
    protected $error;

    /**
     * 初始化方法，实例化操作模型
     */
    public function __construct()
    {
        $this->model = new UcenterMember;
    }

    /**
     * 注册一个新用户
     * @param array $data
     * @return integer  注册成功-用户信息，注册失败-错误编号
     */
    public function register($data, $isLogin = false)
    {
        $data['mobile'] = isset($data['mobile']) ? $data['mobile'] : '';
        $uid   = $this->model->register($data);
        if ($uid && $isLogin) {
            return $this->autoLogin($uid);
        }
        return $uid;
    }

    /**
     * 用户登录认证
     * @param  string  $username 用户名
     * @param  string  $password 用户密码
     * @param  integer $type     用户名类型 （1-用户名，2-邮箱，3-手机，4-UID）
     * @return integer           登录成功-用户ID，登录失败-错误编号
     */
    public function login($username, $password, $type = 1)
    {
        //UC 登录
        $uid = $this->model->login($username, $password, $type);
        //UC登录成功
        if (0 < $uid) {
            /* 登录用户 */
            return $this->autoLogin($uid);
        }
        $this->parseError($uid);
        return false;
    }

    /**
     * 用户自动登录认
     * @param  integer $uid  用户名类型 （1-用户名，2-邮箱，3-手机，4-UID）
     * @return integer       登录成功-用户ID，登录失败-错误编号
     */
    public function autoLogin($uid)
    {
        /* 登录用户 */
        $memberModel = new Member;
        if ($memberModel->login($uid)) {
            return $uid;
        }
        $this->error = $memberModel->getError();
        return false;
    }
    
    /**
     * 设置用户激活
     * @param int $uid
     * @return bool
     */
    public function setActive($uid)
    {
        $status = $this->model->where('id', $uid )->value('status');
        
        if (!is_numeric($status)) {
            $this->error = '用户不存在！';
            return false;
        }
        
        if ($status == 1) {
            $this->error = '账号已激活！';
            return false;
        }
        
        if ($this->model->where('id', $uid )->setField('status', 1)) {
            return true;
        }
        $this->error = '账号已激活失败！';
        return false;
    }
    
    /**
     * 用户记住登录
     * @return void
     */
    public function remember($uid = 0)
    {
        $expire = 30 * 24 * 3600;
        $cookie = jy_encrypt($uid, '', $expire);
        cookie('JYUUID', $cookie, $expire);
    }

    /**
     * 用户退出登录
     * @return mixed
     */
    public function logout()
    {
        $member = new Member;
        return $member->logout();
    }

    /**
     * 获取用户信息
     * @param  string  $uid         用户ID或用户名
     * @param  boolean $isUsername 是否使用用户名查询
     * @return array                用户信息
     */
    public function info($uid = null, $isUsername = false)
    {
        $list = Cache::get('sys_user_info_list');
        $uid  = !($uid && is_numeric($uid)) ? is_login() : $uid;
        /* 查找用户信息 */
        $key = "u{$uid}";
        if (isset($list[$key])) {
            //已缓存，直接使用
            return $list[$key];
        }
        $info = [];
        $map = $isUsername? ['username' => $uid] : ['id' => $uid];
        $user = $this->model->hidden(['password'])
            ->where($map)
            ->with('profile')
            ->find();
        
        if ($user) {
            $info = $user->toArray();
            $info['profile'] =$user->profile->parseData($info['profile']);
            $info = array_merge($info, $info['profile']);
            unset($info['profile']);
            unset($user);
            
            $list[$key] = $info;
            /* 缓存用户 */
            $count = count($list);
            $max   = config('data_max_cache_limit');
            while ($count-- > $max) {
                array_shift($list);
            }
            Cache::tag('limit_cache')->set('sys_user_info_list', $list);
        }
        return $info;
    }
    
    /**
     * 获取用户在数据库的信息
     * @param array $map
     * @param string $field
     * @return mixed
     */
    public function getUser($map = [], $field = '*')
    {
        return $this->model->where($map)->field($field)->find();
    }
    
    /**
     * 返回最大id
     * @return mixed
     */
    public function getMaxId()
    {
        return $this->model->max('id');
    }
    
    /**
     * @param int $id
     * @return int
     */
    public function delete($id)
    {
        return $this->model->where('id','=', $id)->delete();
    }
    
    /**
     * 检测用户名
     * @param  string  $username 用户名
     * @return integer         错误编号
     */
    public function checkUsername($username = null)
    {
        return $this->model->checkField($username, 1);
    }

    /**
     * 检测邮箱
     * @param  string  $email  邮箱
     * @return integer         错误编号
     */
    public function checkEmail($email)
    {
        return $this->model->checkField($email, 2);
    }

    /**
     * 检测手机
     * @param  string  $mobile  手机
     * @return integer         错误编号
     */
    public function checkMobile($mobile)
    {
        return $this->model->checkField($mobile, 3);
    }

    /**
     * 更新用户信息
     * @param array  $data     修改的字段数组
     * @param int    $uid      用户id
     * @param string $password 强制密码用来验证
     * @return true 修改成功，false 修改失败
     */
    public function updateInfo($data, $uid, $password = null, $isPwd = false)
    {
        $scene = 'edit';
        if ($isPwd) {
            $scene = 'edit_need_pwd';
            if (empty($password)) {
                $this->error = '请输入验证密码';
                return false;
            }
            if (!$this->model->verifyPassword($uid, $password)) {
                $this->error = '验证密码输入不正确';
                return false;
            }
        }

        if (isset($data['password']) && empty($data['password'])) {
            unset($data['password']);
        }

        $data['id'] = $uid;
        $validate   = Loader::validate('UcenterMember');
        if (!$validate->scene($scene)->check($data)) {
            // 验证失败 输出错误信息
            $this->error = $validate->getError();
            return false;
        }

        if ($this->model->allowField(true)->save($data, ['id' => $uid])) {
            return true;
        }

        $this->error = '信息修改失败';
        return false;
    }
    
    /**
     * 重置密码
     * @param int    $uid      用户id
     * @param string $password 密码
     * @return  boolean true 更新成功，false 更新失败
     */
    public function restPassword($uid, $password)
    {
        if ($this->model->updateUserPassword($uid, $password)) {
            return true;
        }
        $this->error = '密码重置失败';
        return false;
    }
    
    /**
     * 获取用户名登录的类型
     * @param $username
     * @return int
     */
    public function getLoginType($username)
    {
        if(filter_var($username, FILTER_VALIDATE_EMAIL)){
            return 2;
        } elseif (is_numeric($username) && preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#', $username)) {
            return 3;
        } elseif(is_numeric($username)) {
            return 4;
        } else {
            return 1;
        }
    }

    /**
     * 返回错误信息
     * @return mixed
     */
    public function parseError($uid)
    {
        switch ($uid) {
            case -1:
                $error = '用户不存在或被禁用！';
                break; //系统级别禁用
            case -2:
                $error = '密码错误！';
                break;
            case -3:
                $error = '账号未激活！';
                break;
            default:
                $error = '未知错误！';
                break; // 0-接口参数错误（调试阶段使用）
        }
        $this->error = $error;
    }

    /**
     * 返回错误信息
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }
}
