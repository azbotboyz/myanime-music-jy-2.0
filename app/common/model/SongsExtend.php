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

use think\Model;

/**
 * @package Song 歌曲扩展模型
 * @author 战神~~巴蒂 <jyuucn@163.com>
 */
class SongsExtend extends Model
{
    protected $auto = ['down_rule'];

    //读取器
    protected function setDownRuleAttr($val)
    {
        return is_array($val)? json_encode($val) : $val;
    }
    
    /** 过滤试听地址 */
    protected function setListenUrlAttr($val)
    {
        return text_filter($val);
    }
    
    /** 过滤试听文件id */
    protected function setListenFileIdAttr($val)
    {
        return !empty($val)? intval($val) : 0;
    }
    
    /** 过滤试听地址 */
    protected function setDownUrlAttr($val)
    {
        return !empty($val)? text_filter($val) : '';
    }
    
    /** 过滤下载文件id */
    protected function setDownFileIdAttr($val)
    {
        return !empty($val)? intval($val) : 0;
    }

    //设置服务器试听地址
    protected function getListenUrlAttr($val, $data)
    {
        if (0 === strpos($val, 'http')) {
            return $val;
        }
        
    	if ($data['server_id'] > 0) {
            $domain = (new Server)->getInfo($data['server_id'], 'listen_url');
    	} else {
            $domain = 'http://' . $_SERVER['HTTP_HOST'];
        }
        return rtrim($domain, '/' ) . '/' . ltrim($val, '/');
    }

    //设置服务器下载
    protected function getDownUrlAttr($val, $data)
    {
        if (!empty($val) && 0 !== strpos($val, 'http')) {
            
            if ($data['server_id'] > 0) {
                $domain = (new Server)->getInfo($data['server_id'], 'down_url');
            } else {
                $domain = 'http://' . $_SERVER['HTTP_HOST'];
            }
            $val =  rtrim($domain, '/') . '/' . ltrim($val, '/');
        }
        return $val;
    }

    // user_birthday读取器
    protected function getDownRuleAttr($val)
    {
        return !empty($val)? json_decode($val, true) : $val;
    }
}
