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

use app\admin\model\Addons;
use app\admin\model\Hooks;
use think\Db;
use think\Request;

/**
 * 后台扩展管理页面
 *
 * @author 战神~~巴蒂 <jyuucn@163.com>
 */
class AddonsController extends AdminController
{

    public function _initialize()
    {
        $addonsModel = new Addons;

        $this->assign('_extra_menu', array(
            'Application background management' => $addonsModel->getAdminList(),
        ));
        parent::_initialize();
    }

    /**
     * 插件列表
     */
    public function index(Request $request)
    {
        $this->assign('meta_title', 'Plugin list');
        $addonsModel = new Addons;

        $list   = $addonsModel->getList();
        $page   = $request->param('page', 1);
        $number = 60; // 每页显示
        $voList = $addonsModel->paginate($number, false, ['page' => $page]); // 分页查询

        $_page  = $voList->render(); // 获取分页显示
        $voList = array_slice($list, bcmul($number, $page) - $number, $number);

        // 模板变量赋值
        $this->assign('_page', $_page);
        $this->assign('_list', $voList);
        // 记录当前列表页的cookie
        cookie('__forward__', $request->url());
        return $this->fetch();
    }

    /**
     * 插件后台显示页面
     *
     * @param string $name 插件名
     * @return \think\Response
     */
    public function adminList($addon_name)
    {
        $requset = Request::instance();

        // 记录当前列表页的cookie
        Cookie('__forward__', $requset->url());
        $this->assign('addon_name', $addon_name);
        $class = get_addon_class($addon_name);
        if (!class_exists($class)) {
            $this->error('The plugin does not exist');
        }

        $addon = new $class();
        $this->assign('addon', $addon);
        $param = $addon->admin_list;
        if (!$param && !isset($addon->custom_adminlist)) {
            $this->error('The plugin list information is incorrect');
        }

        $this->assign('meta_title', $addon->info['title']);
        $this->assign('title', $addon->info['title']);

        if (!$param && isset($addon->custom_adminlist)) {
            $viewPath = $addon->addons_path . $addon->custom_adminlist;
            $this->assign('custom_adminlist', $this->fetch($viewPath));
            return $this->fetch('adminlist');
        }

        //将数组转换为变量
        extract($param);
        $this->assign($param);

        $fields = !isset($fields) || empty($fields) ? '*' : $fields;
        $key    = !isset($search_key) ? 'title' : $search_key;

        $map = [];

        $searchWord = $this->request->param($key, '');

        if (!empty($searchWord)) {
            $map[$key] = ['like', '%' . $searchWord . '%'];
        }

        $type = $this->request->param('type', '');

        if (!empty($type)) {
            $map['type'] = $type;
        }
        $this->assign('type', $type);

        if (isset($model)) {
            //$model_name = $model;
            $class = get_addon_model($addon_name, $model);
            $model = class_exists($class) ? new $class() : Db::name($model);

            if ($fields == '*') {
                $fields = $model->getTableFields();
            }

            // 条件搜索
            foreach ($_REQUEST as $name => $val) {
                if (in_array($name, $fields)) {
                    $map[$name] = $val;
                }
            }

            $order = !isset($order) ?: '';
            $list = $this->lists($model, $map, $order, $fields);

            if (isset($list_grid)) {
                $fields = [];
                foreach ($list_grid as &$value) {
                    // 字段:标题:链接
                    $val = explode(':', $value);
                    // 支持多个字段显示
                    $field = explode(',', $val[0]);
                    $value = ['field' => $field, 'title' => $val[1]];

                    if (isset($val[2])) {
                        // 链接信息
                        $value['href'] = $val[2];
                        // 搜索链接信息中的字段信息
                        preg_replace_callback('/\[([a-z_]+)\]/', function ($match) use (&$fields) {
                            $fields[] = $match[1];
                        }, $value['href']);
                    }
                    
                    if (strpos($val[1], '|')) {
                        // 显示格式定义
                        list($value['title'], $value['format']) = explode('|', $val[1]);
                    }

                    foreach ($field as $val) {
                        $array    = explode('|', $val);
                        $fields[] = $array[0];
                    }
                }
                $this->assign('model', $model);
                $this->assign('list_grid', $list_grid);
            }
        }

        $this->assign('statusStyle', config('status_style'));
        $this->assign('_list', $list);
        if ($addon->custom_adminlist) {
            $this->assign('custom_adminlist', $this->fetch($addon->addons_path . $addon->custom_adminlist));
        }
        return $this->fetch('adminlist');
    }

    /**
     * 启用插件
     */
    public function enable($id = '')
    {
        $res = Addons::where('id', $id)->update(['status' => 1]);
        if ($res) {
            cache('hooks', null);
            $this->success('Enabled successfully');
        } else {
            $this->error('Failed to enable');
        }
    }

    /**
     * 禁用插件
     */
    public function disable($id = '')
    {

        $res = Addons::where('id', $id)->update(['status' => 1]);
        if ($res) {
            cache('hooks', null);
            $this->success('Disable success');
        } else {
            $this->error('Disable failed');
        }
    }

    /**
     * 设置插件页面
     */
    public function config($id)
    {

        $id    = (int) $id;
        $addon = Addons::get($id);

        if (!$addon) {
            $this->error('The plugin is not installed');
        }
        $addon = $addon->getData();

        $addonClass = get_addon_class($addon['name']);

        if (!class_exists($addonClass)) {
            trace("Plugin{$addon->name}Can not be instantiated,", 'ADDONS', 'ERR');
        }

        $data                   = new $addonClass();
        $addon['addon_path']    = $data->addon_path;
        $addon['custom_config'] = $data->custom_config;

        $this->assign('meta_title', 'Set up plugins-' . $data->info['title']);
        $dbConfig = $addon['config'];

        $addon['config'] = include $data->config_file;

        if ($dbConfig) {
            $dbConfig = json_decode($dbConfig, true);

            foreach ($addon['config'] as $key => $value) {
                if ($value['type'] != 'group') {
                    $addon['config'][$key]['value'] = $dbConfig[$key];
                } else {
                    foreach ($value['options'] as $gourp => $options) {
                        foreach ($options['options'] as $gkey => $value) {
                            $addon['config'][$key]['options'][$gourp]['options'][$gkey]['value'] = $dbConfig[$gkey];
                        }
                    }
                }
            }
        }

        $this->assign('data', $addon);

        if ($addon['custom_config']) {

            $this->assign('custom_config', $this->fetch($addon['addon_path'] . $addon['custom_config']));
        }
        return $this->fetch();
    }

    /**
     * 保存插件设置
     */
    public function saveConfig()
    {
        $id     = (int) input('id');
        $config = input('config/a');
        $addon  = Addons::get($id);

        if (!$addon) {
            $this->error('The plugin does not exist');
        }

        $flag = $addon->where("id={$id}")
            ->setField('config', json_encode($config));

        if ($flag !== false) {
            cache('addons_' . ucfirst($addon->name) . '_config', null);
            $this->success('Saved successfully', Cookie('__forward__'));
        } else {
            $this->error('Save failed');
        }
    }

    /**
     * 获取插件所需的钩子是否存在，没有则新增
     *
     * @param string $str    钩子名称
     * @param string $addons 插件名称
     * @param string $msg    插件简介
     */
    public function existHook($str, $addons, $msg = '')
    {
        $Hooks         = new Hooks;
        $where['name'] = $str;
        $gethook       = $Hooks->where($where)->find();
        if (!$gethook || empty($gethook)) {
            $Hooks->name        = $str;
            $Hooks->description = $msg;
            $Hooks->type        = 1;
            $Hooks->update_time = time();
            $Hooks->addons      = $addons;
            $Hooks->status      = 1;
            $Hooks->save();
        }
    }

    /**
     * 删除钩子
     * @param string $hook 钩子名称
     */
    public function deleteHook($hook)
    {
        $model     = \think\Db::name('hooks');
        $condition = array(
            'name' => $hook,
        );
        $model->where($condition)->delete();
        cache('hooks', null);
    }

    /**
     * 安装插件
     */
    public function install()
    {
        $addon_name = trim(input('addon_name'));
        $class      = get_addon_class($addon_name);
        if (!class_exists($class)) {
            $this->error('The plugin does not exist');
        }
        $addons = new $class();
        $info   = $addons->info;

        // 检测信息的正确性
        if (!$info) {
            $this->error('Plugin information is missing');
        }
        session('addons_install_error', null);
        $install_flag = $addons->install();

        if (!$install_flag) {
            $this->error('The pre-installation operation failed' . session('addons_install_error'));
        }

        $addonsModel = new Addons;
        if (is_array($addons->admin_list) && $addons->admin_list !== array()) {
            $info['has_adminlist'] = 1;
        } elseif (!empty($addons->custom_adminlist)) {
            $info['has_adminlist'] = 1;
        } else {
            $info['has_adminlist'] = 0;
        }

        $info['config'] = json_encode($addons->getConfig());

        if ($addonsModel->save($info)) {
            $hooks_update = model('Hooks')->updateHooks($addon_name);
            if ($hooks_update) {
                cache('hooks', null);
                $this->success('Successful installation');
            } else {
                $addonsModel->where("name='{$addon_name}'")->delete();
                $this->error('Failed to update the hook at the plug,Please try to reinstall after uninstalling');
            }
        } else {
            $this->error('写入插件数据失败');
        }
    }

    /**
     * 卸载插件
     */
    public function uninstall($id = 0)
    {
        if (!intval($id)) {
            $this->error('The plugin does not exist');
        }

        $addon = Addons::where('id', $id)->field('id,name')->find();
        $class = get_addon_class($addon->name);
        $this->assign('jumpUrl', url('index'));

        if (!$addon || !class_exists($class)) {
            $this->error('The plugin does not exist');
        }

        session('addons_uninstall_error', null);
        $addons = new $class();
        if (!$addons->uninstall()) {
            $this->error('The preload operation failed' . session('addons_uninstall_error'));
        }
        //清除插件配置缓存
        cache('addons_' . ucfirst($addon->name) . '_config', null);

        $update = (new Hooks())->removeHooks($addon->name);

        if (!$update) {
            $this->error('The hook data mounted on the uninstall plugin failed');
        }

        cache('hooks', null);
        if ($addon->delete()) {
            $this->success('Uninstalled successfully');
        } else {
            $this->error('Uninstall plugin failed');
        }
    }

    /**
     * 操作插件后台列表的数据
     * @param  Request $requset
     * @param  [type]  $name
     * @param  integer $id
     * @return [type]
     */
    public function operatedata(Request $requset, $name, $id = 0)
    {
        $this->assign('name', $name);
        $class = get_addon_class($name);
        if (!class_exists($class)) {
            $this->error('The plugin does not exist');
        }
        $addon = new $class();
        $this->assign('addon', $addon);
        $param = $addon->admin_list;
        if (!$param) {
            $this->error('The plugin list information is incorrect');
        }
        extract($param);
        $this->assign('title', $addon->info['title']);

        $model = isset($model) ? $model : '';
        $class = get_addon_model($name, $model);

        if (!class_exists($class)) {
            $this->error('Model can not be implemented');
        }

        $model = new $class();
        if ($id) {
            $data = $model->find($id);
            if (is_object($data)) {
                $data = $data->getData();
            }
            $data || $this->error('Data does not exist!');
            $this->assign('info', $data);
        }

        if ($requset->isPost()) {
            // 获取模型的字段信息
            $data = $requset->post();
            if ($data['id']) {
                if ($model->update($data)) {
                    $this->success("{$addon->info['title']}update completed!", Cookie('__forward__'));
                } else {
                    $this->error("{$addon->info['title']}Update failed!");
                }
            } else {
                if ($model->create($data)) {
                    $this->success("{$addon->info['title']}Create success!", Cookie('__forward__'));
                } else {
                    $this->error("{$addon->info['title']}Failed to create!");
                }
            }
        } else {
            $fields = $model->fields;
            $this->assign('fields', $fields);
            $this->assign('meta_title', $id ? 'edit' . $addon->info['title'] : 'Added' . $addon->info['title']);
            return $this->fetch();
        }
    }

    /**
     * 更改应用状态
     * @return \think\Response
     */
    public function setStatus($name)
    {
        return $this->changeStatus($name);
    }

    /**
     * 删除数据
     * @param  string $name
     * @param  string $id
     * @return mixed
     */
    public function erasedata($name, $id = '')
    {
        $ids = array_unique((array) input('id/a', 0));

        if (empty($ids)) {
            $this->error('Please select the data to be operated!');
        }

        $class = get_addon_class($name);
        if (!class_exists($class)) {
            $this->error('The plugin does not exist');
        }

        $param = (new $class())->admin_list;
        if (!$param) {
            $this->error('The plugin list information is incorrect');
        }
        extract($param);
        $model = isset($model) ? $model : '';
        $class = get_addon_model($name, $model);

        if (!class_exists($class)) {
            $this->error('Model can not be implemented');
        }

        $addonModel = new $class();
        $map        = ['id' => ['in', $ids]];

        if ($addonModel->where($map)->delete()) {
            $this->success('successfully deleted');
        } else {
            $this->error('failed to delete!');
        }
    }
}
