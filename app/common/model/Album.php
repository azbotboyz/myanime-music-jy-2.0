<?php
/**
 * JYmusic音乐管理系统
 *
 * @version     2.0
 * @author      战神~~巴蒂 [jyuucn@163.com]
 * @license     http://jyuu.cn [未经授权严禁私自出售，否者承担法律责任]
 * @copyright   2014 - 2017 JYmusic
 */

namespace app\common\model;

use think\Db;
use think\Model;
use think\Cache;

/**
 * @package 专辑模型
 * @author 战神~~巴蒂 <jyuucn@163.com>
 */
class Album extends Model
{
    /**
     * @var bool 自动写入时间戳
     */
    protected $autoWriteTimestamp = true;

    /**
     * @var bool 自动写入状态吗
     */
    protected $insert = ['status' => 1];

    protected $auto = ['type_name'];

    /** 自动过滤标题 */
    protected function setNameAttr($value)
    {
        return text_filter($value);
    }

    /** 自动写入艺术家 */
    protected function setArtistNameAttr($value, $data)
    {
        if (intval($data['artist_id'])) {
            return Db::name('artist')->where('id', $data['artist_id'])->value('name');
        }
        return $value;
    }

    /** 自动写入类型 */
    protected function setTypeNameAttr($value, $data)
    {
        if (intval($data['type_id'])) {
            return Db::name('album_type')->where('id', $data['type_id'])->value('name');
        }
        return $value;
    }

    /** 设置推荐位 */
    protected function setPositionAttr($value)
    {
        if (!is_array($value)) {
            return 0;
        }
        $pos = 0;
        //将各个推荐位的值相加
        foreach ($value as $val) {
            $pos += $val;
        }
        return $pos;
    }

    /** 自动写入 */
    protected function setAddUnameAttr($value, $data)
    {
        return $data['add_uid'] ?: get_nickname($data['add_uid']);
    }
    
    /** 自动写入 */
    protected function setCoverIdAttr($value)
    {
        return $value ? intval($value) : '';
    }
    
    /** 自动写入 */
    protected function setCoverUrlAttr($value)
    {
        return $value ? text_filter($value) : '';
    }

    /** 自动写入 */
    protected function setPubTimeAttr($value)
    {
        return $value ? text_filter($value) : '未知';
    }

    /** 自动过滤 */
    protected function setCompanyAttr($value)
    {
        return  $value ?: text_filter($value);
    }
    
    /** 获取封面 */
    protected function getCoverUrlAttr($val)
    {
        if (!empty($val) && (0 === strpos($val, 'http'))) {
            return $val;
        }
        return get_cover_url($val, 'album');
    }
    
    /**
     * 获取专辑类型
     * @param int $limit
     * @param string $field
     * @return mixed
     */
    public function getTypes($limit = 99, $field = "id,name")
    {
        $list = Db::name('album_type')
            ->field($field)
            ->limit($limit)
            ->cache('album_type_lists')
            ->select();

        return $list;
    }
    
    /**
     * 获取专辑列表
     * @param  array   $param   条件
     * @return array
     */
    public function getList($param = [])
    {
        $map['status'] = isset($param['status']) ? intval($param['status']) : 1;
        if (isset($param['id']) && strpos($param['id'], ',')) {
            $map['id'] = ['in', $param['id']];
        }

        if (isset($param['artist'])) {
            $map['artist_id'] = strpos($param['artist'], ',')? ['in',  text_filter($param['artist'])] : intval($param['artist']);
        }

        if (isset($param['uid'])) {
            $map['add_uid'] = strpos($param['uid'], ',')? ['in', text_filter($param['uid'])] : intval($param['uid']);
        }
    
        if (!isset($param['field'])) {
            $param['field'] = ['company,status,update_time', true];
        }

        if (isset($param['pos'])) {
            $pos = intval($param['pos']);
            if ($pos) {
                $map[] = ['exp', "position & {$pos} = {$pos}"];
            } elseif ($param['pos'] == "*" || $param['pos'] == "all") {
                $map['position'] = ["neq", 0];
            }
        }

        if (isset($param['type'])) {
            $map['type_id'] = strpos($param['type'], ',')? ['in', text_filter($param['type'])] : intval($param['type']);
        }

        if (isset($param['tag'])) {
            $map['id'] = ['in', (new Tags)->getSongIds(text_filter($param['tag']))];
        }
        
        return parseTagsList($this, $param, $map);
    }
    
    public function getFavList($param =[])
    {
        $limit = isset($param['limit']) ? intval($param['limit']) : 20;
        if (isset($param['page'])) {
            $page = request()->param('page', 1);
            $limit = (($page-1)*$limit) . ',' . $limit;
        }
        
        $where = [];
        if (isset($param['uid'])) {
            $where ['uid']  = $param['uid'];
        }
        
        $sids = db('album_fav')->where($where)->limit($limit);
        
        if (isset($param['cache'])) {
            $sids = $sids->cache(intval($param['cache']))->column('album_id');
        } else {
            $sids = $sids->column('album_id');
        }
        if (!$sids) {
            return null;
        }
        $map['id'] = ['in',  $sids];
        return parseTagsList($this, $param, $map);
    }

    public function parseData($data = [])
    {
        $data = $data? $data : $this->getData();
        if (isset($data['introduce'])) {
            $data['description'] = text_filter(msubstr($data['introduce'], 0, 200));
        }
        $data['url'] = url('home/Album/read', ['id'=> $data['id']]);

        if (isset($data['add_uid'])) {
            $data['user_url'] = $data['add_uid'] > 0? url('user/Home/read', ['id'=> $data['add_uid']]) : '';  
        }
        if (isset($data['type_id'])) {
            $type = (new AlbumType())->simpleInfo($data['type_id']);
            $data['type_url'] = $type['url'];
        }
        if (isset($data['artist_id'])) {
            $data['artist_url'] = $data['artist_id'] > 0? url('home/Artist/read', ['id'=> $data['artist_id']]) : '';
        }
        return  $data;
    }
}
