<?php
/**
 * JYmusic音乐管理系统
 *
 * @version     2.0
 * @author      战神~~巴蒂 <jyuucn@163.com>
 * @license     http://jyuu.cn/license
 * @copyright   2014 - 2017 JYmusic
 */

namespace app\common\behavior;

use think\Db;
use think\Hook;

// 初始化钩子信息
class InitHook
{
    /**
     * 行为扩展的执行入口必须是run
     * @return void
     */
    public function run(){
        
        if (is_file(ROOT_PATH. 'config' . DS . 'database.php')) {
            $data = cache('hooks');
            if(!$data){
                $hooks = Db::name('Hooks')->column('name,addons');
                foreach ($hooks as $key => $value) {
                    if($value){
                        $map['status']  = 1;
                        $names          = explode(',',$value);
                        $map['name']    = array('IN',$names);
                        $data           = Db::name('Addons')->where($map)->column('id,name');
                        if($data){
                            $addons = array_intersect($names, $data);
                            Hook::add($key, array_map('get_addon_class', $addons));
                        }
                    }
                }
                cache('hooks', Hook::get());
            }else{

                Hook::import($data, false);
            }
        }
    }
}