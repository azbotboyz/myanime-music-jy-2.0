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

class Ranks extends Model
{
    /**
     * @var bool 自动写入时间戳
     */
    protected $autoWriteTimestamp = true;

    /**
     * @var bool 自动写入状态吗
     */
    protected $insert = ['status' => 1];

    //自动过滤标题
    protected function setNameAttr($value)
    {
        return text_filter($value);
    }

    //自动获取别名
    protected function setAliasAttr($value, $data)
    {
        return get_pinyin($data['name'], $value);
    }

    //自动获取别名
    protected function setSortAttr($value, $data)
    {
        if (!intval($value)) {
            $value = $this->where(['status' => 1])->count() + 1;
        }
        return intval($value);
    }
    
    //自动获取规则
    public function getRuleAttr($value) {
        return json_decode($value, true);
    }

    /**
     * 设置歌曲参数
     */
    public function setSongsMap($id)
    {
        $rank = $this->where('id', $id)
            ->cache(true)
            ->value('rule');
        return json_decode($rank, true);
    }

    /**
     * 获取列表
     * @param  array $param
     * @param  boolean $isAddSys
     * @return mixed
     */
    public function getList($param = [], $isAddSys = true)
    {
        $map['status'] = isset($param['status']) ? intval($param['status']) : 1;
        if (!$isAddSys) {
            $map['is_sys'] = 0;
        }
        $param['cache'] = true;
        if (isset($param['alias'])) {
            $map['alias'] = $param['alias'];
        }
        return parseTagsList($this, $param, $map);
    }
    
    public function parseData($data = [])
    {
        $data = $data? $data : $this->toArray();
        $alias = !empty($data['alias'])? $data['alias'] : $data['id'];
        $data['url'] = url('home/Ranks/read',['name' => $alias]);
        return  $data;
    }
}
