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

use app\common\model\Config;

class SettingController extends AdminController
{
    /**
     * 显示网站所有设置
     * @param mixed $group
     * @return \think\Response
     */
    public function index($group = 1)
    {
        if (intval($group)) {
            $map = ['status' => 1, 'group' => intval($group)];
            $list = $this->lists("Config", $map, 'sort asc', 'id,name,title,extra,value,remark,type', '', 30);
            $this->assign('list', $list);
            $this->assign('group_id', $group);
            return $this->fetch();
        } else {

            $this->assign('group_id', $group);
            return $this->$group();
        }
    }

    /**
     * 保存更新的配置
     * @param  array  $config
     * @return \think\Response
     */
    public function update($config)
    {
        if ($config && is_array($config)) {
            $Config = new Config;
            foreach ($config as $name => $value) {
                $Config->save(['value'=> $value], ['name' => $name]);
            }
        }
        //清空配置缓存
        cache('db_config_list', null);
        $this->success('配置保存成功');
    }
    
    /**
     * 第三方登录认证
     * @return mixed
     */
    public function oauth()
    {
        $model = new Config();
        
        if ($this->request->isPost()) {
            $post = $this->request->post();
            $meta = $post['oauth_meta'];
            unset($post['oauth_meta']);
            $config = json_encode($post);
            $res = $model->where('name', 'oauth_meta')
                ->setField('value', $meta);
            $res2 = $model->where('name', 'oauth_conf')
                ->setField('value', $config);
            if ($res || $res2) {
                cache('db_config_list', null);
                $this->success('保存成功');
            } else {
                $this->error('保存失败');
            }
        } else {
            $meta = $model->where('name', 'oauth_meta')->value('value');
            $config = $model->where('name', 'oauth_conf')->value('value');
    
            $config = json_decode($config, true);

            $this->assign('meta', $meta);
            $this->assign('info', $config);
            return $this->fetch('oauth');
        }
    }
    

    /**
     * 保存新增推荐位的配置
     * @return \think\Response
     */
    public function createpos()
    {
        $name = $this->request->param('name');
        if (empty($name)) {
            $this->error('参数错误');
        }
        $model = new Config();
        $config = $model->where('name', $name)->field('name,title,value')->find();

        if ($this->request->isPost()) {
            $post = $this->request->post();
            $position  = parse_field_attr($config['value']);
            $fontIcons = config($name . '_font_icon');
            $id = max(array_keys($position))*2;
            $position[$id] = $post['title'];
            $fontIcons[$id] = $post['font_icon'];
            return $this->savePos($model, $name, $position, $fontIcons);
        } else {
            $info = [
                'name'  => $config['name'],
                'pos_title' => $config['title']
            ];
            return $this->fetch('position', ['info'=> $info]);
        }
    }

    /**
     * 保存更新推荐位的配置
     * @return \think\Response
     */
    public function editpos()
    {
        $name = $this->request->param('name');
        $id   = $this->request->param('id');

        if (empty($name) || !is_numeric($id)) {
            $this->error('参数错误');
        }

        $model = new Config;
        $config = $model->where('name', $name)->field('name,title,value')->find();
        $position  = parse_field_attr($config['value']);
        $fontIcons = config($name . '_font_icon');

        if ($this->request->isPost()) {
            $post = $this->request->post();
            $position[$id] = $post['title'];
            $fontIcons[$id] = $post['font_icon'];
            return $this->savePos($model, $name, $position, $fontIcons);
        } else {
            $info = [
                'id'    => $id,
                'name'  => $config['name'],
                'title' => $position[$id],
                'pos_title' => $config['title'],
                'font_icon' => $fontIcons[$id],
            ];
            return $this->fetch('position', ['info'=> $info]);
        }
    }

    /**
     * 保存更新推荐位的配置
     * @param  string           $name
     * @return \think\Response
     */
    public function delpos($name = '')
    {
        $name = $this->request->param('name');
        if (empty($name)) {
            $this->error('参数错误');
        }
        $model = new Config;
        $config = $model->where('name', $name)->field('name,title,value')->find();
        $position  = parse_field_attr($config['value']);
        $fontIcons = config($name . '_font_icon');
        $id = max(array_keys($position));
        unset($position[$id]);
        unset($fontIcons[$id]);
        return $this->savePos($model, $name, $position, $fontIcons);
    }

    protected function savePos($model, $name, $position, $fontIcons)
    {
        $model->save(['value'=> arr_to_newline($position)], ['name' => $name]);
        $model->save(['value'=> arr_to_newline($fontIcons)], ['name' => $name. '_font_icon']);
        cache('db_config_list', null);
        $this->success('推荐位配置更新成功');
    }
}
