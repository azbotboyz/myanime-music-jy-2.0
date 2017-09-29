<?php
/**
 * JYmusic音乐管理系统
 *
 * @version     2.0
 * @author      战神~~巴蒂 <jyuucn@163.com>
 * @license     http://jyuu.cn/license [未经授权严禁私自出售，否者承担法律责任]
 * @copyright   2014 - 2017 JYmusic
 */

namespace app\common\controller;

use think\View;
use think\Request;
use think\Config;
use think\Loader;

/**
 * 插件基类控制器
 * Class Controller
 * @package think\addons
 */
class AddonsController extends BaseController
{
    // 当前插件操作
    protected $addon = null;
    protected $controller = null;
    protected $action = null;
    // 当前template
    protected $template;
    
    // 模板配置信息
    protected $config = [
        'type' => 'Think',
        'view_path' => '',
        'view_suffix' => 'html',
        'strip_space' => true,
        'view_depr' => '_',
        'tpl_begin' => '{',
        'tpl_end' => '}',
        'taglib_begin' => '{',
        'taglib_end' => '}',
    ];

    /**
     * 架构函数
     * @param Request $request Request对象
     * @access public
     */
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        // 生成request对象
        $this->request = is_null($request) ? Request::instance() : $request;
        
        // 处理路由参数
        $route = $this->request->route();
        
        $this->addon = ucfirst($route['addons']);
        $this->controller = !empty($route['controller']) ? ucfirst($route['controller']) : 'Index';
        
        // 格式化路由的插件位置
        $this->action = !empty($route['action']) ? $route['action'] : 'index';
        
        // 生成view_path
        $tplParse = [
            '__ADDONS__'    => '/static/addons',
            '__LIBS__'      => '/static/libs',
            '__ADDONROOT__' => '/addons/' . $this->addon,
            '__ASSETS__'    => '/addons/' . $this->addon . '/assets',
            '__JS__'        => '/addons/' . $this->addon . '/assets/js',
            '__CSS__'       => '/addons/' . $this->addon . '/assets/css',
            '__IMG__'       => '/addons/' . $this->addon . '/assets/images',
        ];
        
        $viewPath = ROOT_PATH .'addons'. DS . $this->addon . DS .'view' . DS;
        
        // 重置配置
        Config::set('template.view_path', $viewPath);

        // 初始化视图模型
        $this->config['view_path'] = $viewPath;
        $this->config = array_merge(Config::get('template'), $this->config);
       
        $this->view = new View($this->config, $tplParse);
        session([
            'prefix'     => 'jy',
            'type'       => '',
            'auto_start' => true,
        ]);
    }

    /**
     * 加载模板输出
     * @access protected
     * @param string $template 模板文件名
     * @param array $vars 模板输出变量
     * @param array $replace 模板替换
     * @param array $config 模板参数
     * @return mixed
     */
    protected function fetch($template = '', $vars = [], $replace = [], $config = [])
    {
        $controller = Loader::parseName($this->controller);
        
        if ('think' == strtolower($this->config['type'])
            && $controller
            && 0 !== strpos($template, '/'
        )) {
            
            $depr = $this->config['view_depr'];
            
            $template = str_replace(['/', ':'], $depr, $template);

            if ('' == $template) {
                // 如果模板文件名为空 按照默认规则定位
                $template = $this->controller . '/' . $this->action;

            } elseif (false === strpos($template, $depr)) {
                $template = $this->controller . '/' . $template;
            }
        }

        return parent::fetch( strtolower($template), $vars = [], $replace = [], $config = []);
    }
}
