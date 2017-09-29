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

class MemberGroupLink extends Model
{
    /**
     * @var bool 自动写入时间戳
     */
    protected $autoWriteTimestamp = false;
    
    /**
     * 用户添加到用户组
     * @param int $uid
     * @param int $gid
     * @param $lenTime
     * @return mixed
     */
    public function addUserTo($uid = 0, $gid =0, $lenTime)
    {
        if ($gid == 1) {
            return $this->where('uid', $uid)->delete();
        }
        //判断是否存在 或者 存在过期的
        $link = $this->where('uid', $uid)->find();

        if($link) {
  
            if (($link->end_time > time()) && ($link->group_id == $gid)) {
                //追加时长
                $link->end_time =  $link->end_time + $lenTime;
            } else {
                $link->end_time = time() + $lenTime;
            }
            $link->group_id = $gid;
            $res =  $link->save();
            
        } else {
            $res = $this->save([
                'uid' => $uid,
                'group_id' => $gid,
                'end_time' => time() + $lenTime
            ]);
        }

        if ($res) {
            cache('sys_user_in_groups', null);
        }
        return $res;
    }

    /**
     * 获取组信息
     * @param  integer $uid
     * @return mixed
     */
    public function getUserlink($uid = 0)
    {
        if (!$uid ) {return '';}
        $detault = ['group_id' =>1, 'end_time' => 0];
        $list = cache('sys_user_in_groups');
        if (isset($list[$uid])) {
            //已缓存，直接使用
            return $list[$uid];
        }
        $info = $this->where('uid', $uid)
            ->where('end_time', '>', time())
            ->field('group_id,end_time')
            ->find();

        $list[$uid] = $info? $info : $detault;
        $count = count($list);
        $max   = config('data_max_cache_limit');
        while ($count-- > $max) {
            array_shift($list);
        }
        cache('sys_user_in_groups', $list);
        return  $list[$uid];
    }
}
