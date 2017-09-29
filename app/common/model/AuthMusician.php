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

use think\Cache;
use think\Model;
class AuthMusician extends Model
{
    /**
     * @var bool 自动写入时间戳
     */
    protected $autoWriteTimestamp = true;

    /**
     * @var bool 自动写入状态吗
     */
    protected $insert = ['status' => 2];
    
    /** 自动过滤标题 */
    protected function setArtistNameAttr($val)
    {
        return text_filter($val);
    }
    
    /** 自动过滤标题 */
    protected function setRealnameAttr($val)
    {
        return text_filter($val);
    }

    /** 自动过滤 */
    protected function setReasonAttr($val)
    {
        return text_filter($val);
    }

    /** 自动过滤 */
    protected function setIdcardImg1Attr($val)
    {
        return text_filter($val);
    }
    
    /** 自动过滤 */
    protected function setIdcardImg2Attr($val)
    {
        return text_filter($val);
    }
    
    /** 设置附件id */
    protected function setIdAttachIdAttr($val)
    {
        return json_encode($val);
    }
    
    /** 获取附件 */
    protected function getIdAttachIdAttr($val)
    {
        return json_decode($val, true);
    }
    
    /**
     * 定义相对关联
     * @return [type]
     */
    public function member()
    {
        return $this->belongsTo('Member', 'uid', 'uid');
    }
}
