<?php
/**
 * JYmusic音乐管理系统
 *
 * @version     2.0
 * @author      战神~~巴蒂 [jyuucn@163.com]
 * @license     http://jyuu.cn [未经授权严禁私自出售，否者承担法律责任]
 * @copyright   2014 - 2017 JYmusic
 */

namespace app\admin\controller;

use think\Request;
use app\common\model\Config;
use app\common\api\Email as EmailApi;

class EmailController extends AdminController
{

    /**
     * 显示列表
     * @return \think\Response
     */
    public function index()
    {
        $config = Config::where('name', 'mail_conf')->value('value');
        $config = json_decode($config, true);
        cookie('forward_url', $this->request->url());
        return $this->fetch('', ['config' => $config]);
    }
    
    /**
     * 测试邮件发送
     * @param \think\Request $request
     * @return \think\Response
     */
    public function test(Request $request)
    {
        $post = $request->post();
        $api =  new EmailApi($post);
        $res = $api->title('这是一封测试邮件')->sendTest($post['test_email']);
        
        if (!$res) {
            $this->error($api->getError());
        } else {
            $this->success('邮件发送成功');
        }
    }

    /**
     * 保存更新的资源
     * @param \think\Request $request
     * @return \think\Response
     */
    public function update(Request $request)
    {
        
        $post = $request->post();
        $config = json_encode($request->post());
        
        if(Config::where('name', 'mail_conf')->setField('value', $config)){
            cache('mail_config', $post);
            $this->success('邮件配置修改成功');
        } else {
            $this->error('邮件配置修改失败');
        }
        
    }
    
}
