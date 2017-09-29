<?php
/**
 * JYmusic音乐管理系统 PHP version 5.4+
 *
 * @version     2.0
 * @author      战神~~巴蒂 <jyuucn@163.com>
 * @license     http://jyuu.cn/license [未经授权严禁私自出售，否者承担法律责任]
 * @copyright   2014 - 2017 JYmusic
 */

namespace app\services;

use think\Db;
use think\View;
use think\Config;

/**
 * 插件类
 * @author JYmusic <jyuucn@163.com>
 */
abstract class Addons
{
    /**
     * 视图实例对象
     * @var view
     * @access protected
     */
    protected $view = null;

    public $info             = [];
    public $addon_path       = '';
    public $config_file      = '';
    public $custom_config    = '';
    public $admin_list       = [];
    public $custom_adminlist = '';
    public $access_url       = [];

    public function __construct()
    {
        // 获取当前插件目录

        $addonName         = $this->getName();
        $this->addons_path = JYMUSIC_ADDON_PATH . $addonName . DS;
        // 读取当前插件配置信息
        if (is_file($this->addons_path . 'config.php')) {
            $this->config_file = $this->addons_path . 'config.php';
        }
        $TMPL_PARSE_STRING['__ADDONROOT__'] = '/addons/' . $addonName;
        $TMPL_PARSE_STRING['__ASSETS__']    = '/addons/' . $addonName . '/assets';
        $TMPL_PARSE_STRING['__JS__']        = '/addons/' . $addonName . '/assets/js';
        $TMPL_PARSE_STRING['__CSS__']       = '/addons/' . $addonName . '/assets/css';
        $TMPL_PARSE_STRING['__IMG__']       = '/addons/' . $addonName . '/assets/images';

        Config::set('view_replace_str', $TMPL_PARSE_STRING);
        // 初始化视图模型
        $config     = ['view_path' => $this->addons_path];
        $config     = array_merge(Config::get('template'), $config);
        $this->view = new View($config, Config::get('view_replace_str'));

        // 控制器初始化
        if (method_exists($this, '_initialize')) {
            $this->_initialize();
        }
    }

    /**
     * 加载模板和页面输出 可以返回输出内容
     * @access public
     * @param string $template 模板文件名或者内容
     * @param array $vars 模板输出变量
     * @param array $replace 替换内容
     * @param array $config 模板参数
     * @return mixed
     * @throws \Exception
     */
    public function fetch($template = '', $vars = [], $replace = [], $config = [])
    {
        if (!is_file($template)) {
            $template = '/' . $template;
        }
        // 关闭模板布局
        $this->view->engine->layout(false);

        echo $this->view->fetch($template, $vars, $replace, $config);
    }

    /**
     * 渲染内容输出
     * @access public
     * @param string $content 内容
     * @param array $vars 模板输出变量
     * @param array $replace 替换内容
     * @param array $config 模板参数
     * @return mixed
     */
    public function display($content, $vars = [], $replace = [], $config = [])
    {
        // 关闭模板布局
        $this->view->engine->layout(false);

        echo $this->view->display($content, $vars, $replace, $config);
    }

    /**
     * 模板变量赋值
     * @access protected
     * @param mixed $name 要显示的模板变量
     * @param mixed $value 变量的值
     * @return void
     */
    public function assign($name, $value = '')
    {
        $this->view->assign($name, $value);
    }

    /**
     * 获取当前模块名
     * @return string
     */
    final public function getName()
    {
        $data = explode('\\', get_class($this));
        return array_pop($data);
    }

    final public function checkInfo()
    {
        $info_check_keys = array('name', 'title', 'description', 'status', 'author', 'version');
        foreach ($info_check_keys as $value) {
            if (!array_key_exists($value, $this->info)) {
                return false;
            }
        }
        return true;
    }

    /**
     * 获取插件的配置数组
     * @param string $name 可选模块名
     * @return array|mixed|null
     */
    final public function getConfig($name = '')
    {
        static $_config = [];

        $name = !empty($name)?  $name : $this->getName();
        if (isset($_config[$name])) {
            return $_config[$name];
        }

        $config = cache('addons_' . $name . '_config');
        if ($config) {
            return $config;
        }

        $config = Db::name('Addons')->where(['status' => 1, 'name' => $name])
            ->value('config');

        if ($config) {
            $config = json_decode($config, true);
            cache('addons_' . ucfirst($name) . '_config', $config);
            return $config;
        }

        if (is_file($this->config_file)) {
            $temp_arr = include $this->config_file;
            foreach ($temp_arr as $key => $value) {
                if ($value['type'] == 'group') {
                    foreach ($value['options'] as $gkey => $gvalue) {
                        foreach ($gvalue['options'] as $ikey => $ivalue) {
                            $config[$ikey] = $ivalue['value'];
                        }
                    }
                } else {
                    $config[$key] = $temp_arr[$key]['value'];
                }
            }
            unset($temp_arr);
        }

        $_config[$name] = $config;
        return $config;
    }

    /**
     * 获取插件所需的钩子是否存在，没有则新增
     * @param string $str    钩子名称
     * @param string $addons 插件名称
     * @param string $msg    插件简介
     */
    public function existHook($str, $addons = '', $msg = '')
    {
        $Hooks         = Db::name('hooks');
        $where['name'] = $str;
        $gethook       = $Hooks->where($where)->find();
		$addons 	 = !empty($addons)? $addons : $this->getName();

        if (!$gethook || empty($gethook)) {
            $data['name']        = $str;
            $data['description'] = $msg;
            $data['type']        = 1;
            $data['update_time'] = time();
            $data['addons']      = ucfirst($addons);
            $data['status']      = 1;
            $Hooks->insert($data);
        }
    }

    public function execSql($sql)
    {
    	$sql = str_replace("\r", "\n", $sql);
		$sql = explode(";\n", $sql);

		foreach ($sql as $value) {
			$value = trim($value);
			if(empty($value)) continue;
			$res = Db::execute($value);
		}
    	if (false !== $res) {
    		return true;
    	}
    	session('addons_install_error', 'sql 执行失败');
    	return false;
    }
    
    public function deleteHook($hook){
        $model = Db::name('hooks');
        $condition = [
            'name' => $hook,
        ];
        $model->where($condition)->delete();
    }
    
    //必须实现安装
    abstract public function install();

    //必须卸载插件方法
    abstract public function uninstall();
}
