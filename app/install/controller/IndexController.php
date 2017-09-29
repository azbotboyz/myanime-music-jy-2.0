<?php
/**
 * JYmusic音乐管理系统 PHP version 5.4+ [未经授权严禁私自出售，否者承担法律责任]
 *
 * @version     2.0
 * @author      战神~~巴蒂 [jyuucn@163.com]
 * @license     LICENSE: http://jyuu.cn/license
 * @copyright   2014 - 2017 by JYmusic
 */

namespace app\install\controller;

use think\Db;
use think\Controller;

/**
 * 前台统一控制器
 */
class IndexController extends Controller
{
    
    protected $db;
    
    protected function _initialize(){
        if(is_file(ROOT_PATH . 'storage' . DS . 'install.lock')){
            abort(404,'已经成功安装了JYmusic，请不要重复安装!');
        }
    }
    
    /**
     * 首页展示
     * @return \think\Response
     */
    public function index()
    {
        if(is_file(ROOT_PATH .'config' . DS . 'database.php')){
            // 已经安装过了 执行更新程序
            session('update',true);
            $msg = '请删除install.lock文件后再运行升级!';
        } else {
            $msg = '已经成功安装了JYmusic，请不要重复安装!';
        }
        
        if(is_file(ROOT_PATH . 'storage' . DS . 'install.lock')){
            $this->error($msg);
        }
        
        return $this->fetch();
    }
    
    /**
     * 第一步
     * @return \think\Response
     */
    public function step1()
    {
        session('error', false);
        //环境检测
        $env = check_env();
    
        //目录文件读写检测
        $dirfile = check_dirfile();
        $this->assign('dirfile', $dirfile);
    
        //函数检测
        $func = check_func();
    
        session('step', 1);
        $this->assign('env', $env);
        $this->assign('func', $func);
        return $this->fetch();
    }
    
    /**
     * 第二步
     * @return \think\Response
     */
    public function step2()
    {
        /*if(session('update')){
            session('step', 2);
            session('update',null);
            $this->display('update');
        }else{*/
            session('error') && $this->error('环境检测没有通过，请调整环境后重试！');
            $step = session('step');
            if($step != 1 && $step != 2 && $step != 3){
                $this->redirect('step1');
            }
            session('step', 2);
        return $this->fetch();
        //}
    }
    
    /**
     * 第三步
     * @return void
     */
    public function step3()
    {
        if(session('step') != 2){
            $this->redirect('step2');
        }
        echo $this->fetch();
    
        //连接数据库
        $dbConfig = session('db_config');
        $dbConfig['type'] = 'mysql';
        $this->db = Db::connect($dbConfig);
        
        //创建数据表
        create_tables($this->db, $dbConfig['prefix']);
    
        //注册创始人帐号
        $auth  = build_auth_key();
        $this->regAdmin($auth);
        
        //写入配置文件
        write_config($dbConfig, $auth);
        session('step', 3);

        echo '<script type="text/javascript">window.location.href="'.url('complete').'"</script>';

    }

    //安装完成
    public function complete(){
        $step = session('step');

        if(!$step){
            $this->redirect('index');
        } elseif($step != 3) {
            $this->redirect("step{$step}");
        }

        // 写入安装锁定文件
        file_put_contents(ROOT_PATH . 'storage' . DS . 'install.lock', 'lock');

        if(!session('update')){
            //创建配置文件
            $this->assign('info',session('config_file'));
        }
        session('step', null);
        session('error', null);
        session('update',null);

        return $this->fetch();
    }
    
    /**
     * 写入管理员数据
     * @return \think\Response
     */
    public function create()
    {
        $post = $this->request->post();
        $rule = [
            'hostname' => 'require',
            'database' => 'require',
            'username' => 'require',
            'hostport' => 'require|number',
            'prefix' => 'require',
        ];
    
        $msg = [
            'hostname.require' => '数据库地址不能为空',
            'database.require' => '数据库名不能为空',
            'username.require' => '数据库用户名不能为空',
            'hostport.require' => '数据库端口不能为空',
            'hostport.number'  => '数据库端口只能为数字',
            'prefix.require' => '数据表前缀不能为空',

        ];
        $result = $this->validate($post['db'], $rule, $msg);
        if (true !== $result) {
            // 验证失败 输出错误信息
            $this->error($result);
        }
        $post['db']['type'] = 'mysql';
        
        //检测数据库是否可以正常连接
        try {

            $dbh = Db::connect([
                // 数据库类型
                'type'        => 'mysql',
                // 服务器地址
                'hostname'    => $post['db']['hostname'],
                // 数据库用户名
                'username'    => $post['db']['username'],
                // 数据库密码
                'password'    => $post['db']['password'],
                // 数据库连接端口
                'hostport'    => $post['db']['hostport']
            ]);

            //$dbh = new \PDO('mysql:host='.$post['db']['hostname'].':'.$post['db']['hostport'].';', $post['db']['username'], $post['db']['password']);
            session('db_config', $post['db']);
            $dbh = null;
        } catch (\Exception $e) {
            $this->error('数据库连接失败<br>' . $e->getMessage());
        }
        
        $rule = [
            'username' => 'require|length:4,16',
            'password' => 'require|length:5,32',
            'repassword'=>'require|confirm:password',
            'email'     => 'require|email',
        ];
    
        $msg = [
            'username.require' => '管理员用户名不能为空',
            'username.length' => '管理员用户名长度4-16个字符',
            'password.require' => '管理员密码不能为空',
            'password.length' => '管理员密码长度5-32个字符',
            'repassword.require' => '确认密码不能为空',
            'repassword.confirm'  => '确认密码与密码不一致',
            'email.require' => '管理员邮箱不能为空',
            'email.email' => '管理员邮箱格式不正确',
        ];
        $result = $this->validate($post['admin'], $rule, $msg);
        if (true !== $result) {
            // 验证失败 输出错误信息
            $this->error($result);
        }
        unset($post['admin']['repassword']);
        //缓存管理员信息
        session('admin_info', $post['admin']);
    
        $DB = Db::connect([
            // 数据库类型
            'type'        => 'mysql',
            // 服务器地址
            'hostname'    => $post['db']['hostname'],
            // 数据库用户名
            'username'    => $post['db']['username'],
            // 数据库密码
            'password'    => $post['db']['password'],
            // 数据库连接端口
            'hostport'    => $post['db']['hostport']
        ]);
        $sql = "CREATE DATABASE IF NOT EXISTS `{$post['db']['database']}` DEFAULT CHARACTER SET utf8";

        if ($DB->execute($sql)) {
            //跳转到数据库安装页面
            $this->success("", 'step3');
        } else {
            $this->error('数据库创建失败，请查看是否有创建权限或数据库名称是否正确');
        }
    }
    
    /**
     * 注册管理员账号
     * @param string $auth
     */
    protected function regAdmin($auth)
    {
        show_msg('开始注册创始人帐号...');
        $admin = session('admin_info');
        $admin['password'] = user_md5($admin['password'], $auth);
        $admin['reg_ip'] = $admin['last_login_ip']  = get_client_ip(1);
        $admin['reg_time'] = $admin['last_login_time']  = time();
        $admin['status'] = 1;

        $dbConfig = session('db_config');
        $prefix = $dbConfig['prefix'];
        $db = $this->db->name('ucenter_member');

        $admin['id'] = 1;
        if ($db->insert($admin)) {
            $member = $this->db->name('member');
            $user['nickname'] = $admin['username'];
            $user['reg_ip'] = $admin['reg_ip'];
            $user['reg_time'] = $admin['reg_time'];
            $user['status'] = 1;
    
            if ($member->insert($user)) {
                show_msg('创始人帐号注册完成！');
            } else {
                show_msg('创始人帐号创建失败...');
            }
        
        } else {
            show_msg('创始人帐号创建失败...');
        }
    }
}
