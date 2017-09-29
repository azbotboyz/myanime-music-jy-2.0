<?php

/**
 * JYmusic音乐管理系统
 *
 * @version     2.0
 * @author      战神~~巴蒂 <jyuucn@163.com>
 * @license     http://jyuu.cn/license [未经授权严禁私自出售，否者承担法律责任]
 * @copyright   2014 - 2017 JYmusic
 */

namespace app\admin\model;

use think\Model;

/**
 * 插件模型
 */
class Addons extends Model
{

    protected $autoWriteTimestamp = false;

    protected $auto = [
        'create_time'
    ];

    protected function setCreateTimeAttr($value)
    {
        return time();
    }

    protected function setStatusAttr($value)
    {
        return 1;
    }


    /**
     * 获取插件列表
     * @return mixed
     */
    public function getList()
    {
        $dir = JYMUSIC_ADDON_PATH;
        
        
        $dirs = array_map('basename', glob($dir . '*', GLOB_ONLYDIR));
        
        if ($dirs === FALSE || !file_exists($dir)) {
            $this->error = '插件目录不可读或者不存在';
            return false;
        }
        
        $addons = [];
        $where ['name'] = ['in', $dirs];
        $list = $this->where($where)->field(true)->select();

        foreach ($list as $key => $val) {
            $list[$key] = $val->toArray();
            $val['uninstall'] = 0;
            $addons[$val['name']] = $list[$key];
        }
        
        foreach ($dirs as $val) {
            if (!isset($addons[$val])) {
                $class = get_addon_class($val);
                if (!class_exists($class)) { // 实例化插件失败忽略执行
                    \think\Log::record('插件' . $val . '的入口文件不存在！');
                    continue;
                }
                
                $obj = new $class();
                $addons [$val] = $obj->info;
                if ($addons[$val]) {
                    $addons[$val]['uninstall'] = 1;
                    unset($addons[$val]['status']);
                }
                if (!isset($addons[$val]['has_adminlist'])) {
                    $addons[$val]['has_adminlist'] = 0;
                }
            }
        }
        return list_sort_by($addons, 'uninstall', 'desc');
    }

    /**
     * 获取插件的后台列表
     * @return array
     */
    public function getAdminList()
    {
        $admin  = [];
        $addons = $this->where("status=1 AND has_adminlist=1")->field('title,name')->select();
        if ($addons) {
            foreach ($addons as $value) {
                $value = $value->toArray();
                $admin[] = [
                    'title' => $value['title'],
                    'icon'  => 'puzzle-piece',
                    'url'   => "addons/adminList?addon_name={$value['name']}"
                ];
            }
        }
        return $admin;
    }
}
