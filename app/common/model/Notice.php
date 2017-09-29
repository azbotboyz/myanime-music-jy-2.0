<?php

namespace app\common\model;

use think\Model;

class Notice extends Model
{
    //开启自动写入时间戳
    protected $autoWriteTimestamp = true;
    
    // 关闭自动写入update_time字段
    protected $updateTime = false;
   
    //设置发布消息的引用名称
    public function setAppNameAttr($value)
    {
        return !empty($value)? text_filter($value) : 'public';
    }
    
    //设置发布消息状态
    public function setStatusAttr($value)
    {
        return !empty($value)? $value : 1;
    }
    
    //设置发布消息的读取
    public function setIsReadAttr($value)
    {
        return !empty($value)? $value : 0;
    }
    
    //设置标题过滤
    public function setTitleAttr($value)
    {
        return !empty($value)? text_filter($value) : '';
    }
    
//    //设置内容过滤
//    public function setContentAttr($value)
//    {
//        return text_filter($value);
//    }

}
