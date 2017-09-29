<?php
/**
 * JYmusic音乐管理系统 PHP version 5.4+
 *
 * @author    战神~~巴蒂 <jyuucn@163.com>
 * @version   2.0.0
 * @license   http://jyuu.cn [未经授权严禁私自出售，否者承担法律责任]
 * @copyright 2014 - 2017 JYmusic
 */

namespace app\common\model;

use think\Model;

class Article extends Model
{

    /**
     * @var bool 自动写入时间戳
     */
    protected $autoWriteTimestamp = true;

    // 定义自动完成的属性
    protected $insert = ['uid'];

    /** 自动过滤标题 */
    protected function setTitleAttr($value)
    {
        return text_filter($value);
    }

    /** 自动过滤 */
    protected function setUidAttr($value)
    {
        return intval($value) ? $value : 1;
    }

    /** 自动获取描述 */
    protected function setDescriptionAttr($value)
    {
        if (!empty($value)) {
            return text_filter($value);
        }
        $content = input('content/a');
        if (!empty($content['content'])) {
            $content = text_filter(msubstr($content['content'], 0, 300, 'utf-8', false));
            $value   = msubstr($content, 0, 130, 'utf-8', false);
        }
        return $value;
    }

    /** 自动设置封面 */
    protected function setCoverUrlAttr($value)
    {
        if (empty($value)) {
            $content = input('content/a');
            preg_match_all('/<img.*?src="(.*?)".*?>/is', $content['content'], $array);
            $value = !empty($array[1][0])? $array[1][0] : '';
        }
        return $value;
    }

    /** 设置推荐位*/
    protected function setPositionAttr($value, $data)
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

    /** 自动过滤*/
    protected function setDeadlineAttr($value)
    {
        return !empty($value) ? strtotime($value) : '';
    }

    /** 自动设置*/
    protected function getDeadlineAttr($value)
    {
        return !empty($value) ? date('Y-m-d h:i:s', $value) : '';
    }

    /**
     * 获取内容扩展
     */
    public function content()
    {
        return $this->hasOne('ArticleContent');
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

        if (isset($param['uid'])) {
            $map['add_uid'] = strpos($param['uid'], ',')? ['in', text_filter($param['uid'])] : intval($param['uid']);
        }
        
        if (isset($param['pos'])) {
            $pos = intval($param['pos']);
            if ($pos) {
                $map[] = ['exp', "position & {$pos} = {$pos}"];
            } elseif ($param['pos'] == "*" || $param['pos'] == "all") {
                $map['position'] = ["neq", 0];
            }
        }
        
        if (isset($param['cate'])) {
            $map['category_id'] = strpos($param['cate'], ',')? ['in', text_filter($param['cate'])] : ['in', (new ArticleCategory())->getChildIds($param['cate'])];
        }
        
        return parseTagsList($this, $param, $map);
    }
    
    public function parseData($data = [])
    {
        $data = $data? $data : $this->toArray();
        $data['url'] = url('article/Index/read', ['id' => $data['id']]);
        $data['cate_url'] = (new ArticleCategory())->getUrl($data['category_id']);
        $data['cover_url'] = !empty($data['cover_url']) ? $data['cover_url'] : '/public/static/images/df_cover.png';
        return  $data;
    }
    
}
