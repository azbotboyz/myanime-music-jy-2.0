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

class Tags extends Model
{
    /**
     * @var bool 自动写入时间戳
     */
    protected $autoWriteTimestamp = true;

    /**
     * @var bool 自动写入状态吗
     */
    protected $insert = ['status' => 1];
    
    protected $auto = ['alias'];

    /**
     * 自动过滤标题
     * @param string $value 要过滤的标题
     * @return string
     */
    protected function setNameAttr($value)
    {
        return text_filter($value);
    }

    /**
     * 自动获取别名
     * @param string $value
     * @return string
     */
    protected function setAliasAttr($value, $data)
    {
        return get_pinyin($data['name'], $value);
    }
    
    /**
     * 获取当前标签的id 不存在侧自动新增
     * @param string $tags
     * @return array
     */
    protected function getIds($tags)
    {
        if (strpos($tags, ',')) {
            $tags = explode(',', $tags);
        }
        
        $ids = [];
        $tags = (array)$tags;
        foreach ($tags as $val) {
            $map = ['name' => $val];
            $id = $this->where('name', $val)->value('id');
            if ($id) {
                $ids[] = $id;
            } elseif ($this->save($map)) {
                $ids[] = $this->id;
            }
        }
        return $ids;
    }

    /**
     * 设置歌曲标签
     */
    public function saveSongs($tags, $songId = null)
    {
        $ids = $this->getIds($tags);
        $res = true;
        if ($songId && !empty($ids)) {
            $db = db('songs_tags');
            $map = ['songs_id' => $songId];
            
            //查看是否存在原有标签
            $old = $db->where($map)->select();
            //如果存在
            if ($old) {
                foreach ($old as $val) {
                    if (!in_array($val['tags_id'], $ids)) {
                        $db->delete($val['id']);
                    }
                }
            }
            
            $data = [];
            foreach ($ids as $id) {
                $map['tags_id'] = $id;
                if (!$db->where($map)->value('id')) {
                    $data[] = $map;
                }
            }
            
            if (!empty($data)) {
                $res = $db ->insertAll($data);
            }
        }
        return $res;
    }
    

    /**
     * 获取列表
     * @param  array $param
     * @return  mixed
     */
    public function getList($param = [])
    {
        $map['status'] = isset($param['status']) ? intval($param['status']) : 1;
        if (isset($param['id']) && strpos($param['id'], ',')) {
            $map['id'] = ['in', $param['id']];
        }
        if (isset($param['group'])) {
            $map['group'] = $param['group'];
        }
        if (!isset($param['tree'])) {
            return parseTagsList($this, $param, $map);
        }
        
        return $this->getTree();
    }

    /**
     * 获取标签组
     * @return array
     */
    public function getGroupList()
    {
        $group = array_merge(['0' => '其它'], config("tag_group"));
        krsort($group);
        return $group;
    }

    /**
     * 获取标签树
     * @return array
     */
    public function getTree()
    {
        $list = cache('tags_tree_list');
        if (!empty($list)) {
            return $list;
        }
        $tags = $this->where('status', 1)
            ->field('id,name,alias,group')
            ->order('sort asc,id asc')
            ->select();

        if ($tags) {
            $group     = $this->getGroupList();
            $groupTags = [];
            foreach ($tags as $k => $v) {
                $data = $v->getData();
                $groupTags[$data['group']][] = $data;
            }
            unset($tags);
            foreach ($group as $key => $value) {
                $list[$key]['id']   = $key;
                $list[$key]['name'] = $value;
                if (isset($groupTags[$key])) {
                    $list[$key]['tags'] = $groupTags[$key];
                }
            }
            unset($groupTags);
        }
        cache('tags_tree_list', $list);
        return $list;
    }
    
    /**
     * @param $ids
     * @param array $param
     * @return array
     */
    public function getSongIds($ids, $param = [])
    {
        $limit = isset($param['limit'])? intval($param['limit']) : 20;
        if (isset($param['page'])) {
            $page = request()->param('page', 1);
            $limit = $page . ',' . $limit;
        }
  
        $sids = db('songs_tags')
            ->where('tags_id', 'in', $ids)
            ->order('id desc')
            ->page($limit)
            //->cache('songs_tags',60,'limit_cache')
            ->column('songs_id');
        return $sids;
    }
    
    public function parseData($data = [])
    {
        if (is_object($data)) {
            $data = $data->toArray();
        }
        $data = $data? $data : $this->toArray();
        
        $alias = !empty($data['alias']) && !is_numeric($data['alias']) ? $data['alias'] : $data['id'];
        $data['url'] = url('home/Tag/read', ['name' => $alias]);
        
        return  $data;
    }
}
