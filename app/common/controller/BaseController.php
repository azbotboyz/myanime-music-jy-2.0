<?php
/**
 * JYmusic音乐管理系统 PHP version 5.4+ [未经授权严禁私自出售，否者承担法律责任]
 *
 * @version     2.0
 * @author      战神~~巴蒂 [jyuucn@163.com]
 * @license     LICENSE: http://jyuu.cn/license
 * @copyright   2014 - 2017 by JYmusic
 */

namespace app\common\controller;

use think\Url;
use think\Cache;
use think\Config;
use think\Controller;
use app\common\model\SeoRule;
use app\common\api\Config as configApi;

/**
 * 前台统一控制器
 */
class BaseController extends Controller
{

    /**
     * 前台控制器初始化操作
     */
    public function _initialize()
    {
        /**
         * 加载配置信息
         */
        $config = Cache::get('db_config_list');
        if (!$config) {
            $config = configApi::lists('db_config_list');
        }
        Config::set($config);

        //判断站点关闭
        if ($config['web_site_close']) {
            abort(200, $config['web_off_msg']);
        }

        if ($config['url_model'] == 4) {
            Url::root('/index.php?' . Config::get('var_pathinfo') . '=');
        } elseif ($config['url_model'] == 3) {
            Url::root('/index.php');
        } else {
            Url::root('/');
        }

        $this->assign('meta_title', config('web_site_title'));
        $this->assign('meta_keywords', config('web_site_keyword'));
        $this->assign('meta_description', config('web_site_description'));
    }

    /**
     * 解析seo
     * @param array $data
     */
    protected function seoMeta($data = [], $action = null, $controller = null, $module = null)
    {
        $module     = !is_null($module) ? $module : $this->request->module();
        $controller = !is_null($controller) ? $controller : $this->request->controller();
        $action     = !is_null($action) ? $action : $this->request->action();
        $model      = new SeoRule();
        if ($seo = $model->parseActive($module, $controller, $action, $data)) {
            $this->assign('meta_title', $seo['title']);
            $this->assign('meta_keywords', $seo['keyword']);
            $this->assign('meta_description', $seo['description']);
        }
    }

    /**
     * 公共返回
     * @param $res
     * @return \think\response
     */
    protected function ret($res, $msg = 'ok', $type = 'json')
    {
        if ($res) {
            $result = [
                'code'   => 0,
                'msg'    => $msg,
                'result' => $res,
            ];

            if ($this->request->isAjax()) {
                return json($result, 200);
            } else {
                return jsonp($result, 200);
            }
        }
        return $this->retErr(40500);
    }

    /**
     * 公共成功返回
     * @param mixed $result
     * @param string $type
     * @return \think\response
     */
    protected function retSucc($result = 'ok', $type = 'json')
    {
        if(is_string($result)) {
            $result = ['code' => 0, 'msg' => $result];
        } else {
            $result['code'] = 0;
            $result['msg'] = isset($result['msg'])? $result['msg'] : 'ok';
        }

        if ($this->request->param('callback')) {
            return jsonp($result, 200);
        }
    
        $header = $this->request->header();
        if ((!$this->request->isAjax() && strpos($header['accept'], 'javascript')) ||  $type == 'jsonp') {
            return jsonp($result, 200);
        } else {
            return json($result, 200);
        }
    }

    /**
     * 公共错误返回
     * @param integer $code
     * @param string $msg
     * @return \think\response\Json
     */
    protected function retErr($code, $msg = '', $type = 'json')
    {
        $codeMsg = [
            //验证错误码
            40004 => '参数错误',

            //操作限制错误码
            40011 => '收藏数量已达上限',
            40012 => '关注数量已达上限',
            40013 => '本月头像上传次数已达上限',
            40014 => '本月资料修改次数已达上限',
            40015 => '本月密码修改次数已达上限',
            40016 => '自己无法关注自己',

            //文件错误码
            40020 => '没有上传的文件',
            40021 => '文件大小超过限制',
            40022 => '文件后缀不允许',
            40023 => '文件类型不符合',
            40024 => '非法图像文件',
            40025 => '非法音频文件',
            40026 => '非法视频文件',
            40027 => '文件验证没有通过',
            40028 => '文件已经存在',
            
            //验证错误
            40030 => '验证错误',
            40031 => '用户名错误',
            40032 => '邮箱格式错误',
            40033 => '密码错误',
            40034 => '手机号错误',
            40035 => '验证码错误',
            40036 => '验证码已过期',
            40037 => 'token无效',
            40038 => '重复登录',
            
            //账号相关错误
            40040 => '用户不存在',
            40041 => '账号已经激活',
            40044 => '账号已被禁用',
            40045 => '邮箱不存在，请确认输入是否正确',

            //服务状态错误码
            40401 => '抱歉你还没有登录，请登录',
            40403 => '没有权限访问',
            40404 => '操作的类型不存在',
            40405 => '操作过于频繁，请慢点',
            40406 => '短时间内无法操作，请慢点',
            40407 => '暂时没有数据',
            
            //服务内存程序出错
            40500 => '抱歉操作失败，请稍后重试',
            40501 => '用户注册失败，请稍后重试',
            40502 => '用户登录失败，请稍后重试',
            40503 => '激活邮件发送失败，请稍后重试',
            40504 => '验证码获取失败，请稍后重试',
            40505 => '退出登录失败，请稍后重试',
        ];

        $result = [
            'code'  => $code,
            'error' => !empty($msg) ? $msg : $codeMsg[$code],
        ];

        if ($this->request->param('callback')) {
            return jsonp($result, 200);
        }
    
        $header = $this->request->header();
        if ((!$this->request->isAjax() && strpos($header['accept'], 'javascript')) ||  $type == 'jsonp') {
            return jsonp($result, 200);
        } else {
            return json($result, 200);
        }
    }
}
