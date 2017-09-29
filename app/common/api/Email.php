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

class Email
{
    /**
     * @var
     */
    protected $mail;
    
    /**
     * @var string
     */
    protected $error;
    
    protected $uid;
    
    /**
     * @param array $config
     * 初始化方法，实例化操作模型
     */
    public function __construct($config = [])
    {
        $this->mail = new \PHPMailer;
        //解决中文乱码
        $this->mail->CharSet = 'UTF-8';
        //设置中文语言
        $this->mail->setLanguage('zh_cn');
        $this->setConfig($config);
        $uid = is_login();
        $this->uid = $uid ? $uid : session_id();
    }
    
    /**
     * 设置邮件标题
     * @param  string $title
     * @return $this
     */
    public function title($title = '')
    {
        $title = !empty($title) ? $title : 'JYmusic音乐程序';
        $this->mail->Subject = $title;
        return $this;
    }
    
    /**
     * 设置文本信息
     * @param  string  $info
     * @return $this
     */
    public function message($info = '')
    {
        $this->mail->Body = $info;
        return $this;
    }
    
    /**
     * 设置html信息
     * @param string $content
     * @return mixed
     */
    public function html($content = '')
    {
        $this->mail->isHTML(true);
        $this->mail->Body = $content;
        return $this;
    }
    
    /**
     * 设置发送地址
     * @param string|array $emails
     * @return $this
     */
    public function address($emails = '')
    {
        if (is_array($emails)) {
            foreach ($emails as $mail) {
                $this->mail->addAddress($mail);
            }
        } else {
            $this->mail->addAddress($emails);
        }
        return $this;
    }
    
    /**
     * 发送邮件
     * @return bool
     */
    public function send()
    {
        if(!$this->mail->send()) {
            $this->error = $this->mail->ErrorInfo;
            return false;
        } else {
            return true;
        }
    }
    
    /**
     * 发送邮箱验证码
     * @param string $address
     * @return mixed
     */
    public function sendVcode($address = '')
    {
        $code = $this->createVcode();
        $msg  = '<p>你当前的验证码为：'.$code.',此验证码将在15分后过期</p>';
        $msg  .= '<p>如果不是本人操作请忽略此email!</p>';
        $msg  .= '<p>这是一封自动产生的email，请勿回复。</p>';
        return $this->address($address)->title('邮箱验证')->html($msg)->send();
    }
    
    /**
     * 效验验证码
     * @param int $vcode
     * @return bool
     */
    public function checkVcode($vcode = 0)
    {
        $key    = 'email_vcode_' . $this->uid;
        $cache  = Cache::get($key);
        if (!$cache) {
            $this->error='验证码已过期，请重新获取验证码';
            return false;
        }
        
        $cacheCode = intval($cache['vcode']);
        $vcode     = intval($vcode);
        
        //判断是否在有效期
        if ($cacheCode && $vcode && $cacheCode === $vcode) {
            return true;
        }
        $this->error='验证码输入不正确';
        return false;
    }
    
    /**
     * 设置发送测试邮件
     * @param string $address
     * @return mixed
     */
    public function sendTest($address)
    {
        $body = '这是一封JYmusic程序的一封测试邮件 <a href="http://jyuu.cn">点击前往官网</a>';
        return $this->address($address)->html($body)->send();
    }
    
    /**
     * 设置调试模式
     * @param int $type
     * @return $this
     */
    public function debug($type = 3)
    {
        $this->mail->SMTPDebug = $type;
        return $this;
    }
    
    /**
     * 设置邮件配置信息
     * @param array $config
     */
    protected function setConfig($config = [])
    {
        if (empty($config)) {
            $config = Cache::get('mail_config');
            if (empty($config)) {
                $config = db('config')->where('name', 'mail_conf')->value('value');
                $config = json_decode($config, true);
                Cache::set('mail_config', $config);
            }
        }
        
        if (empty($config)) {
            $this->error = '配置信息不完整';
        }

        if ($config['send_type'] == 'smtp') {
            $this->mail->isSMTP();      // 设置是否使用 SMTP
            $this->mail->Host = $config['host'];     // 邮箱服务器地址
            $this->mail->SMTPAuth = true; //使SMTP认证
            $this->mail->Username = $config['account']; // SMTP 用户名
            $this->mail->Password = $config['password'];          // SMTP 密码
            $this->mail->SMTPSecure = intval($config['ssl'])? 'ssl' : 'TLS';
        }
        $this->mail->setFrom($config['sender_email'], $config['sender_name']);
        $this->mail->Port = intval($config['port']);
    }
    
    /**
     * @param int $length 验证码长度
     * @param int $expiresTime 到期时间
     * @return mixed
     */
    protected function createVcode($length = 6, $expiresTime = 900)
    {
        //创建验证码
        $vcode = rand(pow(10, ($length - 1)), pow(10, $length) - 1);
        //缓存验证码
        $cache = [
            'uid' => $this->uid,
            'vcode' => $vcode
        ];
        $key = 'email_vcode_' . $this->uid;
        Cache::set($key, $cache, $expiresTime);
        return $vcode;
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