<?php
/**
 * JYmusic音乐管理系统
 *
 * @version     2.0
 * @author      战神~~巴蒂 <jyuucn@163.com>
 * @license     http://jyuu.cn [未经授权严禁私自出售，否者承担法律责任]
 * @copyright   2014 - 2017 JYmusic
 */
namespace app\user\controller;

use app\common\api\User;
use app\common\model\Member;
use app\common\model\SyncLogin;
use Overtrue\Socialite\AccessToken;
use Overtrue\Socialite\SocialiteManager;
use app\common\controller\BaseController;

//第三方登录共控制器
class OauthController extends BaseController
{
    protected $driver;
    
    /**
     * 登录首页
     * @param string $driver
     */
    public function index($driver = '')
    {
        $this->driver = $driver;
        $config = $this->getConfig();
        $socialite = new SocialiteManager($config);
        $response = $socialite->driver($driver)->redirect();
        $response->send();
        exit();
    }
    
    
    public function callback($driver = '', $code = '')
    {
        $this->driver = $driver;
        
        $config = $this->getConfig();
        try {
            $socialite = new SocialiteManager($config);
            $userInfo = $socialite->driver($driver)->user();
        } catch (\Exception $e) {
            $this->error($e->getMessage(), '', '', 1000);
        }
            
        $tokens = $userInfo['token'];
        $tokens['openid'] = $userInfo['id'];
        
        //判断用户是否存在
        $model = new SyncLogin();
        $oauth = $model->where('openid', $tokens['openid'])
            ->where('type', $driver)
            ->find();

        //没有用户注册一个用户
        if (!$oauth) {
            //获取当前用户信息
            return  $this->register($userInfo, $tokens);
        }
        //判断用户是否被删除或禁用
        $status = Member::where('uid', $oauth['uid'])->value('status');
        
        if ($status < 1) {
            $this->error('用户不存在或被禁用');
        }
        
        if ($oauth['expires_time'] < time()) {
            //更新数据库中的数据
            $oauth->access_token = $tokens['access_token'];
            $oauth->refresh_token = isset($tokens['refresh_token'])? $tokens['refresh_token'] : '';
            $oauth->expires_time = time() + $tokens['expires_in'];
            $oauth->save();
        }
        
        //登录用户
        $api = new User();
        if ($api->autoLogin($oauth['uid'])) {
            $this->success('登录成功', 'user/Account/index');
        } else {
            $this->error('账号登录失败');
        }
    }
    
    /**
     * 注册一个新用户
     * @param array $user
     * @param array $tokens
     */
    protected function register($user, $tokens)
    {
        //注册一个新用户
        $api = new User();
        $maxId = $api->getMaxId()+1;
        
        $data['username'] = 'jy_user' . $maxId;
        $data['password'] = uniqid();
        $data['email'] = isset($user['email']) ? $user['email'] : '';

        if (!$uid = $api->register($data)) {
            $this->error('第三方登录失败，账号注册失败');
        }
        //写入同步数据库
        $sync = [
            'uid' => $uid,
            'type' => $this->driver,
            'openid' => $tokens['openid'],
            'unionid' => isset($tokens['unionid']) && $tokens['unionid']? $tokens['unionid'] : '',
            'access_token' => $tokens['access_token'],
            'refresh_token' => isset($tokens['refresh_token'])? $tokens['refresh_token'] : '',
            'expires_time' => time() + $tokens['expires_in']
        ];
        $model = new SyncLogin($sync);
        if (!$model->save()) {
            //删除注册的账号
            $api->delete($uid);
            $this->error('第三方登录失败，账号注册失败');
        }
        
        //保存用户信息
        $member = new Member();
        $member->data([
            'uid' => $uid,
            'nickname'  =>  text_filter($user['nickname']),
            'avatar' =>  $user['avatar'],
            'status' => 1,
            'reg_ip' => $this->request->ip(true),
            'reg_time' => time(),
            'last_login_ip' => $this->request->ip(true),
            'last_login_time' => time()
        ]);
        $member->save();
        
        //登录用户
        if ($api->autoLogin($uid)) {
            $this->success('登录成功', 'user/Setting/index');
        } else {
            $this->error('第三方登录失败，账号登录失败');
        }
        
    }
    
    /**
     * @param string $driver
     * @return mixed
     */
    protected function getConfig()
    {
        $config = json_decode(config('oauth_conf'), true);
        if (!isset($config[$this->driver])) {
            abort('404', '页面不存在');
        }
        
        if (empty($config[$this->driver]['client_id']) || empty($config[$this->driver]['client_secret'])) {
            $this->error('第三方登录配置不正确');
        }
        
        $redirect = url('user/Oauth/callback', '', true, true);
        if (config('url_model') == 4) {
            $redirect .= '&driver=' . $this->driver;
        } else {
            $redirect .= '?driver=' . $this->driver;
        }
        $config[$this->driver]['redirect'] = $redirect;
        return $config;
    }
}
