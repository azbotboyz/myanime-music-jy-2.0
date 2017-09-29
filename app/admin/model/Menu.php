<?php
/**
 * JYmusic音乐管理系统- 听客网 [未经授权严禁私自出售，否者承担法律责任]
 *
 * @author    战神~~巴蒂 <jyuucn@163.com>
 * @copyright 2014-2017 JYmusic
 * @license   http://jyuu.cn/license license
 * @version   GIT: 1.1
 */
namespace app\admin\model;

use app\common\model\Tree;
use think\Cache;
use think\Model;

/**
 * 菜单模型
 * @package  Menu
 */
class Menu extends Model
{
    /**
     * 自动过滤标题
     *
     * @param string $value 要过滤的标题
     * @param array  $data  提交的数据数组
     *
     * @return string
     */
    public function setTitleAttr($value, $data)
    {
        return htmlspecialchars($value);
    }

    /**
     * 自动完成状态码
     *
     * @param string $value 要过滤的标题
     * @param array  $data  提交的数据数组
     *
     * @return mixed
     */
    public function setStatusAttr($value, $data)
    {
        return !empty($value)? $value : 1;
    }

    /**
     * 自动修改状态
     *
     * @param type $value
     *
     * @return type
     */
    public function getStatusAttr($value)
    {
        $statusTexts = config('status_text');
        return $statusTexts[$value];
    }

    /**
     * 自动修改状态
     *
     * @param type $value
     *
     * @return type
     */
    public function getHideAttr($value)
    {
        $whetherTexts = config('whether_text');
        return $whetherTexts[$value];
    }

    /**
     * 自动修改状态
     *
     * @param string $value 是否显示
     *
     * @return string
     */
    public function getIsDevAttr($value)
    {
        $whetherTexts = config('whether_text');
        return $whetherTexts[$value];
    }

    /**
     * 获取全部菜单，仅获取id,title
     *
     * @return array|mixed
     */
    public function getLine()
    {
        if (Cache::has('menu_line')) {
            return Cache::get('menu_line');
        }
        $line = $this->column('id,pid,title');
        Cache::set('menu_line', $line);
        return $line;
    }

    /**
     * 获取全部菜单树，仅获取id,title
     *
     * @return array|mixed
     */
    public function getTree()
    {
        $treeModel  = new Tree;
        $menus  = $treeModel->toFormatTree($this->getLine());
        $menus  = array_merge([0=>['id'=>0,'title_show'=>'顶级菜单']], $menus);
        return $menus;
    }
}