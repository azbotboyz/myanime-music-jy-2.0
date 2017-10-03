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
            40004 => 'Parameter error',

            //操作限制错误码
            40011 => 'The maximum number of collections has been reached',
            40012 => 'The number of concerns has reached the limit',
            40013 => 'This month has passed the maximum number of video uploads',
            40014 => 'The number of changes to this month has reached the maximum number of changes',
            40015 => 'Password changes have reached the maximum number of months',
            40016 => 'I can not focus on myself',

            //文件错误码
            40020 => 'No uploaded files',
            40021 => 'The file size exceeds the limit',
            40022 => 'File suffix is ​​not allowed',
            40023 => 'The file type does not match',
            40024 => 'Illegal image file',
            40025 => 'Illegal audio files',
            40026 => 'Illegal video files',
            40027 => 'File validation did not pass',
            40028 => 'The file already exists',
            
            //验证错误
            40030 => 'Validation error',
            40031 => 'username error',
            40032 => 'The mailbox is malformed',
            40033 => 'wrong password',
            40034 => 'The phone number is wrong',
            40035 => 'Verification code error',
            40036 => 'The verification code has expired',
            40037 => 'token is invalid',
            40038 => 'Duplicate login',
            
            //账号相关错误
            40040 => 'User does not exist',
            40041 => 'Account has been activated',
            40044 => 'Account has been disabled',
            40045 => 'The mailbox does not exist. Please make sure the input is correct',

            //服务状态错误码
            40401 => 'Sorry that you are not logged in, please log in',
            40403 => 'No permission to access',
            40404 => 'The type of operation does not exist',
            40405 => 'Operation is too frequent, please slow down',
            40406 => 'Short time can not operate, please slow down',
            40407 => 'There is no data for the time being',
            
            //服务内存程序出错
            40500 => 'Sorry for the operation failed, please try again later',
            40501 => 'User registration failed, please try again later',
            40502 => 'User login failed, please try again later',
            40503 => 'Activation mail failed, please try again later',
            40504 => 'Verification code failed, please try again later',
            40505 => 'Exit login failed, please try again later',
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
