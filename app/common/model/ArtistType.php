<?php
/**
 * JYmusic音乐管理系统
 *
 * @version     2.0
 * @author      战神~~巴蒂 <jyuucn@163.com>
 * @license     http://jyuu.cn [未经授权严禁私自出售，否者承担法律责任]
 * @copyright   2014 - 2017 JYmusic
 */

namespace app\common\model;

use think\Model;

class ArtistType extends Model
{

    /**
     * @var bool 自动写入时间戳
     */
    protected $autoWriteTimestamp = true;

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
        return get_pinyin($data['name'], $value, 3);
    }

    /**
     * 获取类型列表
     * @param  array $param
     * @return array
     */
    public function getList($param = [])
    {
        $map['status'] = isset($param['status']) ? intval($param['status']) : 1;
        if (isset($param['id']) && strpos($param['id'], ',')) {
            $map['id'] = ['in', $param['id']];
        }
        return parseTagsList($this, $param, $map);
    }

    public function parseData($data)
    {
        $alias = !empty($data['alias'])? $data['alias'] : $data['id'];
        $data['url'] = url('home/Artist/type', ['name' => $alias]);
        return  $data;
    }
    
    public function getUrl($id = 0)
    {
        if (!(int)$id || !$data = $this->cache(true)->field('id,alias')->find($id)) {
            return null;
        }
        $alias = !empty($data['alias']) && !is_numeric($data['alias']) ? $data['alias'] : $data['id'];
        return  url('home/Artist/type', ['name' => $alias]);
    }
}
