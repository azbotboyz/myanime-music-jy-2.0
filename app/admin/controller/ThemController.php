<?php
/**
 * JYmusic音乐管理系统 PHP version 5.4+
 *
 * @version     2.0
 * @author      战神~~巴蒂 <jyuucn@163.com>
 * @license     http://jyuu.cn/license [未经授权严禁私自出售，否者承担法律责任]
 * @copyright   2014 - 2017 JYmusic
 */

namespace app\admin\controller;

use think\Cache;
use think\Request;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;

/**
 * 后台主题管理页面
 * @author 战神~~巴蒂 <jyuucn@163.com>
 */
class ThemController extends AdminController {
    
    /**
     * 主题列表
     * @param  string  $type
     * @return \think\Response
     */
    public function index($type = 'web')
    {
        $adapter = new Local(ROOT_PATH );
        $filesystem  = new Filesystem($adapter);
        
        //获取当前主题配置信息
        $viewConf = require ROOT_PATH . 'config' . DS . 'view.php';
        $curConf = $viewConf[$type . '_them'];

        $path =  'resources/' . $type;
        $contents = $filesystem->listContents($path);
        
        foreach ($contents as &$them) {
            $them['cover'] =  '/' . $them['path'] . '/screenshot.png';
            $configFile = ROOT_PATH . $them['path'] . '/conf.php';
            if (is_file($configFile)) {
                $them['info'] =  require $configFile;
                $them['status'] = true;
            } else {
                $them['status'] = false;
                $them['error'] =  '模板主题配置文件不存在';
            }
            $them['type'] = $type;
            $them['active'] = $curConf['them_name'] == $them['basename'] ? true : false;
        }

        return $this->fetch('', [
            'type' => $type,
            '_list'=>$contents
        ]);
    }

    /**
     * 安装主题
     * @return void
     */
    public function install()
    {
  
    }
    
    /**
     * 卸载主题
     * @return void
     */
    public function uninstall()
    {
    
    }
    
    /**
     * 编辑主题
     * @return void
     */
    public function edit()
    {
    
    }
    
    /**
     * 启用主题
     * @return void
     */
    public function enable($type, $filename)
    {
        //获取当前主题配置信息
        $viewFile  = 'config' . DS . 'view.php';
        $viewConf = require ROOT_PATH . $viewFile;
        $configFile = ROOT_PATH . 'resources' . DS . $type . DS . $filename . DS . 'conf.php';
        
        if (!is_file($configFile)) {
            $this->error('启用失败,主题配置文件读取失败');
        }
        $themConf = require $configFile;
        $curKey = $type . '_them';
        $viewConf[$curKey]['them_name'] =  $filename;
        $viewConf[$curKey]['assets_path'] =  $themConf['assets_path'];
        $viewConf[$curKey]['home_view'] =  $themConf['home_path'];
        $viewConf[$curKey]['user_view'] =  $themConf['user_path'];
        $viewConf[$curKey]['article_view'] =  $themConf['article_path'];
        
        $content = <<<str
<?php
/**
 * JYmusic音乐管理系统
 *
 * @version     2.0
 * @author      战神~~巴蒂 <jyuucn@163.com>
 * @license     http://jyuu.cn [未经授权严禁私自出售，否者承担法律责任]
 * @copyright   2014 - 2017 JYmusic
 */
return [
    'web_them'   =>[
        'them_name'     => '{$viewConf['web_them']['them_name']}',
        'assets_path'   => '{$viewConf['web_them']['assets_path']}',
        'home_view'     => '{$viewConf['web_them']['home_view']}',
        'user_view'     => '{$viewConf['web_them']['user_view']}',
        'article_view'  => '{$viewConf['web_them']['article_view']}',
    ],
    'use_wap_view'=> {$viewConf['use_wap_view']},
    'wap_them'   =>[
        'them_name'     => '{$viewConf['wap_them']['them_name']}',
        'assets_path'   => '{$viewConf['wap_them']['assets_path']}',
        'home_view'     => '{$viewConf['wap_them']['home_view']}',
        'user_view'     => '{$viewConf['wap_them']['user_view']}',
        'article_view'  => '{$viewConf['wap_them']['article_view']}',
    ],
];
str;
        $adapter = new Local(ROOT_PATH );
        $filesystem  = new Filesystem($adapter);
        
        //try{
            $filesystem->put($viewFile, $content);
            $this->success('主题启用成功');
        /*} catch (\Exception $e) {
            $error = $e->getMessage();
            $error = $error? $error : '主题启用失败';
            $this->error($error);
        }*/
    }
    
    /**
     * 禁用主题
     * @return void
     */
    public function disable() {

    }

    /**
     * 主题配置页面
     */
    public function config() {
    
    }

    /**
     * 保存插件设置
     */
    public function saveConfig() {

    }
    
}
