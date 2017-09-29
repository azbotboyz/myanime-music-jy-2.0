<?php
/**
 * JYmusic音乐管理系统 PHP version 5.6+
 *
 * @author    战神~~巴蒂 <jyuucn@163.com>
 * @version   2.0.0
 * @license   http://jyuu.cn [未经授权严禁私自出售，否者承担法律责任]
 * @copyright 2014 - 2017 JYmusic
 */

namespace app\common\model;

use think\Model;
use think\Cache;

class ArticleCategory extends Model
{
    protected $parentCate;
    
    /**
     * @var bool 自动写入时间戳
     */
    protected $autoWriteTimestamp = true;

    /**
     * @var bool 自动写入状态吗
     */
    protected $insert = ['status' => 1];

    /**
     * 自动过滤标题
     */
    protected function setTitleAttr($value)
    {
        return text_filter($value);
    }

    /**
     * 自动设置别名
     */
    protected function setAliasAttr($value, $data)
    {
        return get_pinyin($data['title'], $value);
    }

    /**
     * 自动设置排序
     */
    protected function setSortAttr($value, $data)
    {
        if (!intval($value)) {
            $map['pid'] = empty($data['pid'])? 0 : $data['pid'];
            $value = $this->where($map)->count();
        }
        return $value;
    }

    /**
     * 自动过滤标题
     */
    protected function setMetaTitleAttr($value)
    {
        return !empty($value)? text_filter($value) : '';
    }

    /**
     * 自动过滤标题
     */
    protected function setKeywordsAttr($value)
    {
        return !empty($value)? text_filter($value) : '';
    }

    /**
     * 自动过滤标题
     */
    protected function setDescriptionAttr($value)
    {
        return !empty($value)? text_filter($value) : '';
    }


    public function getInfo($id = 0)
    {
        if (!intval($id)) {
            return '';
        }

        $list = cache('article_cate_list');
        if (isset($list[$id])) {
            //已缓存，直接使用
            return $list[$id];
        }

        $info = $this->where('id', $id)->field('id,title,alias,pid,sort,status')->find();
        if ($info) {
            $info = $info->getData();
        }
        $list[$id] = $info;
        cache('article_cate_list', $list, 30*86400, 'article_cate');
        return $info;
    }

    /**
     * 获取导航树，指定分类则返回指定分类极其子导航，不指定则返回所有导航树
     * @param integer $id    导航ID
     * @param boolean $field 查询字段
     * @return array          分类树
     */
    public function getTree($id = 0, $field = [], $format = false)
    {
        /* 获取当前分类信息 */
        if ($id) {
            $info = self::get($id)->getData();
            $map = ['id'];
        }

        /* 获取所有分类*/
        $map = ['status' => ['gt', -1]];
        $field = !empty($field)? $field : 'id,title,alias,pid,sort,status';
        $list = $this->where($map)
            ->order('sort')
            ->field($field)
            ->cache('article_cate_list_'.$id, 30*86400, 'article_cate')
            ->select();

        if ($format) {
            $tree = new Tree();
            return  $tree->toFormatTree($list, 'title');
        }

        $list = list_to_tree($list, 'id', 'pid', '_child', $id);
        /* 获取返回数据 */
        if (isset($info)) { //指定分类则返回当前分类极其子分类
            $info['_child'] = $list;
        } else { //否则返回所有分类
            $info = $list;
        }
        return $info;
    }
    
    /**
     * 获取子孙ID
     * @return [type] [description]
     */
    public function getChildIds($pid = 0, $isAddPid = true)
    {
        $ids = [];
        if (intval($pid)) {
            $ids = $this->where('status', 1)->where('pid', $pid)->column('id');
            if ($isAddPid) {
                $ids[] = $pid;
            }
        }
        return $ids;
    }
    
    /**
     * 列表
     * @return  mixed
     */
    public function getList($param = [])
    {
        $map['status'] = isset($param['status']) ? intval($param['status']) : 1;
        if (isset($param['id']) && strpos($param['id'], ',')) {
            $map['id'] = ['in', $param['id']];
        }
        if (isset($param['pid'])) {
            $map['pid'] = $param['pid'];
        }
        if (isset($param['alias'])) {
            $map['alias'] = $param['alias'];
        }
        return parseTagsList($this, $param, $map);
    }
    
    public function parseData($data = [])
    {
        $data = $data? $data : $this->toArray();
        $alias = !empty($data['alias'])? $data['alias'] : $data['id'];
        $data['url'] = url('article/Category/read', ['name' => $alias]);

        return  $data;
    }
    
    public function getUrl($id = 0)
    {
        if (!(int)$id || !$data = $this->cache(true)->field('id,alias')->find($id)) {
            return null;
        }
        $alias = !empty($data['alias'])? $data['alias'] : $data['id'];
        return  url('article/Category/read', ['name' => $alias]);
    }
    
    public function getParentCate($pid = 0)
    {
        if ((int)($pid)) {
            return null;
        }
        
        $cate = self::where('pid', $pid)->find();
        
        if (!$cate) {
            return null;
        }
    
        $this->parentCate = $cate;
        return $cate;
    }
    
    /**
     * 获取当前分类页模板
     */
    protected function getTpl($data = [])
    {
        if ((int)($data)) {
            $data = self::get($data)->toArray();
        }
        if (!$data) {
            return $data;
        }
        
        $parentCate = $this->getParentCate($data['pid']);
        
        if (empty($data['template_index'])) {
        
        }
    }
    
}
