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

use think\Validate;
use app\common\model\Notice as NcModel;

class Notice
{
    /**
     * @var
     */
    protected $model;
    
    /**
     * @var mixed
     */
    protected $error = true;
    
    /**
     * 初始化方法，实例化操作模型
     */
    public function __construct($data = [])
    {
        $this->model= new NcModel;
        $this->data = $data;
    }
    
    /**
     * 发送一个新通知
     * @return mixed
     */
    public function send()
    {
        if ($this->checkData()){
            $this->model->data($this->data);
            return $this->model->save()? true : 'Message failed, please try again later';
        } else {
            return $this->error;
        }
    }
    
    /**
     * 发送的手机短息
     * @param  string $phone 发送的手机号
     * @return integer         错误编号
     */
    public function sendSms($phone)
    {
        return true;
    }
    
    /**
     * 发送邮件
     * @param  string $email 要发送的邮箱
     * @return integer         错误编号
     */
    public function sendEmail($email)
    {
        //获取当前用户的邮箱地址
        return true;
    }
    
    /**
     * 接收者
     * @param int $uid
     * @return $this
     */
    public function to($uid = 0)
    {
        $this->data['uid'] = $uid;
        return $this;
    }
    
    /**
     * 信息标题
     * @param string $title
     * @return $this
     */
    public function title($title = 'system notification')
    {
        $this->data['title'] = $title;
        return $this;
    }
    
    /**
     * 消息内容
     * @param string $content
     * @return $this
     */
    public function content($content)
    {
        $this->data['content'] = $content;
        return $this;
    }
    
    /**
     * 用于快速获取未读消息
     * @param int $uid   用户id
     * @param int $page  分页数
     * @param int $limit 获取数量
     * @return mixed
     */
    public function getUnread($uid=0, $page=1, $limit=10)
    {
        $map['uid']     = $uid;
        $map['is_read'] = 0;
        $map['status']  = 1;
        
        $result = $this->model->where($map)
            ->field('id,title,content,create_time')
            ->page($page . ',' . $limit)
            ->select();
        if ($result) {
            foreach ($result as &$v) {
                $v = $v->getData();
            }
            return $result;
        }
        $this->error = 'Message fetch failed';
        return false;
    }
    
    /**
     * 设置消息已读
     * @param $ids mixed
     * @return mixed
     */
    public function setRead($ids)
    {
        $result = $this->model->where('id', 'in', $ids)->setField('is_read', 1);
        if ($result) {
            return true;
        }
        $this->error = 'Message set has failed to read, please try again later!';
        return $this->error;
    }
    
    /**
     * 设置消息已读
     * @return mixed
     */
    public function delete($ids)
    {
        if (empty($ids)) {
            $this->error = 'The deleted message id is not specified';
        }
    
        if ($this->model->destroy($ids)) {
            return true;
        }
    
        $this->error = 'Message deletion failed, please try again later!';
        return $this->error;
    }
    
    /**
     * 设置消息体
     * @return bool
     */
    protected function checkData()
    {
        if (empty($this->data)) {
            $this->error = 'The transmission failed and the data does not exist';
            return false;
        }
        
        if (!isset($this->data['uid'])) {
            $this->error = 'Failed to send,The user ID sent can not be empty';
            return false;
        }
        
        $rule = [
            'title' => 'length:4,200',
            'content' => 'require|max:500'
        ];
        
        $msg = [
            'title.length' => 'Title Length 4-200 characters',
            'content.require' => 'The message content can not be empty',
			'content.max' => 'Message content up to 500 words'
        ];
        
        $validate = new Validate($rule, $msg);
        $result = $validate->check($this->data);
        if (!$result) {
            // 验证失败 输出错误信息
            $this->error = $validate->getError();
            return false;
        }
        return true;
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