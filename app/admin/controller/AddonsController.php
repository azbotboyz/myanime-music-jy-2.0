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
            '应用后台管理' => $addonsModel->getAdminList(),
        ));
        parent::_initialize();
    }

    /**
     * 插件列表
     */
    public function index(Request $request)
    {
        $this->assign('meta_title', '插件列表');
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
            $this->error('插件不存在');
        }

        $addon = new $class();
        $this->assign('addon', $addon);
        $param = $addon->admin_list;
        if (!$param && !isset($addon->custom_adminlist)) {
            $this->error('插件列表信息不正确');
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
            $this->success('启用成功');
        } else {
            $this->error('启用失败');
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
            $this->success('禁用成功');
        } else {
            $this->error('禁用失败');
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
            $this->error('插件未安装');
        }
        $addon = $addon->getData();

        $addonClass = get_addon_class($addon['name']);

        if (!class_exists($addonClass)) {
            trace("插件{$addon->name}无法实例化,", 'ADDONS', 'ERR');
        }

        $data                   = new $addonClass();
        $addon['addon_path']    = $data->addon_path;
        $addon['custom_config'] = $data->custom_config;

        $this->assign('meta_title', '设置插件-' . $data->info['title']);
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
            $this->error('插件不存在');
        }

        $flag = $addon->where("id={$id}")
            ->setField('config', json_encode($config));

        if ($flag !== false) {
            cache('addons_' . ucfirst($addon->name) . '_config', null);
            $this->success('保存成功', Cookie('__forward__'));
        } else {
            $this->error('保存失败');
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
            $this->error('插件不存在');
        }
        $addons = new $class();
        $info   = $addons->info;

        // 检测信息的正确性
        if (!$info) {
            $this->error('插件信息缺失');
        }
        session('addons_install_error', null);
        $install_flag = $addons->install();

        if (!$install_flag) {
            $this->error('执行插件预安装操作失败' . session('addons_install_error'));
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
                $this->success('安装成功');
            } else {
                $addonsModel->where("name='{$addon_name}'")->delete();
                $this->error('更新钩子处插件失败,请卸载后尝试重新安装');
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
            $this->error('插件不存在');
        }

        $addon = Addons::where('id', $id)->field('id,name')->find();
        $class = get_addon_class($addon->name);
        $this->assign('jumpUrl', url('index'));

        if (!$addon || !class_exists($class)) {
            $this->error('插件不存在');
        }

        session('addons_uninstall_error', null);
        $addons = new $class();
        if (!$addons->uninstall()) {
            $this->error('执行插件预卸载操作失败' . session('addons_uninstall_error'));
        }
        //清除插件配置缓存
        cache('addons_' . ucfirst($addon->name) . '_config', null);

        $update = (new Hooks())->removeHooks($addon->name);

        if (!$update) {
            $this->error('卸载插件所挂载的钩子数据失败');
        }

        cache('hooks', null);
        if ($addon->delete()) {
            $this->success('卸载成功');
        } else {
            $this->error('卸载插件失败');
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
            $this->error('插件不存在');
        }
        $addon = new $class();
        $this->assign('addon', $addon);
        $param = $addon->admin_list;
        if (!$param) {
            $this->error('插件列表信息不正确');
        }
        extract($param);
        $this->assign('title', $addon->info['title']);

        $model = isset($model) ? $model : '';
        $class = get_addon_model($name, $model);

        if (!class_exists($class)) {
            $this->error('模型无法实列化');
        }

        $model = new $class();
        if ($id) {
            $data = $model->find($id);
            if (is_object($data)) {
                $data = $data->getData();
            }
            $data || $this->error('数据不存在！');
            $this->assign('info', $data);
        }

        if ($requset->isPost()) {
            // 获取模型的字段信息
            $data = $requset->post();
            if ($data['id']) {
                if ($model->update($data)) {
                    $this->success("{$addon->info['title']}更新成功！", Cookie('__forward__'));
                } else {
                    $this->error("{$addon->info['title']}更新失败！");
                }
            } else {
                if ($model->create($data)) {
                    $this->success("{$addon->info['title']}创建成功！", Cookie('__forward__'));
                } else {
                    $this->error("{$addon->info['title']}创建失败！");
                }
            }
        } else {
            $fields = $model->fields;
            $this->assign('fields', $fields);
            $this->assign('meta_title', $id ? '编辑' . $addon->info['title'] : '新增' . $addon->info['title']);
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
            $this->error('请选择要操作的数据!');
        }

        $class = get_addon_class($name);
        if (!class_exists($class)) {
            $this->error('插件不存在');
        }

        $param = (new $class())->admin_list;
        if (!$param) {
            $this->error('插件列表信息不正确');
        }
        extract($param);
        $model = isset($model) ? $model : '';
        $class = get_addon_model($name, $model);

        if (!class_exists($class)) {
            $this->error('模型无法实列化');
        }

        $addonModel = new $class();
        $map        = ['id' => ['in', $ids]];

        if ($addonModel->where($map)->delete()) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }
}
