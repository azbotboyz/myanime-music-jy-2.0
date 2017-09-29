<?php
/**
 * JYmusic音乐管理系统 PHP version 5.4+
 *
 * @version     2.0
 * @author      战神~~巴蒂 <jyuucn@163.com>
 * @license     http://jyuu.cn/license [未经授权严禁私自出售，否者承担法律责任]
 * @copyright   2014 - 2017 JYmusic
 */
namespace app\services;

use think\Db;
use think\Cache;

/**
 * 检测网站操作行为
 * @package app\services\Actions
 */
class Actions
{
    /**
     * 公共操作延迟更新，避免频繁写入数据库
     * @param  int  $id
     * @return mixed
     */
    public static function update($table, $field, $id, $isInc = true, $pk = 'id')
    {
        $key      = $field . '_num';
        $cacheKey = $table . '_' . $field;
        $cache    = self::cache($cacheKey);

        $nowTime = time();
        //检测第一次缓存
        if (empty($cache)) {
            //初始化过期时间，时间如果过期自动更新数据 单位：秒
            $expires              = 60 * 15;
            $cache['expire_time'] = $nowTime + $expires;
        }

        $model = Db::name($table);
        //如果没有当前数据的缓存
        if (!isset($cache[$key][$id])) {
            //获取当前数据在数据库中的值
            $num = $model->where($pk, $id)->value($field);
            if (!is_numeric($num)) {
                return false;
            }
        } else {
            $num = intval($cache[$key][$id]);
        }

        //判断是否过期 过期后写入数据库
        if ($cache['expire_time'] < $nowTime) {
            //已经过期，更新数据
            foreach ($cache[$key] as $id => $val) {
                $model->where($pk, $id)->setField($field, $val);
            }
            //清空缓存
            $cache = null;
        } else {
            if (!$isInc) {
                $num = $num > 0 ? --$num : $num;
            } else {
                ++$num;
            }
            $cache[$key][$id] = $num;
        }
        //写入或清空缓存
        self::cache($cacheKey, $cache);
        return [$pk => $id, $field => $num];
    }
    
    /**
     * 获取缓存的操作次数
     * @param string $table
     * @param string $field
     * @param int $id
     * @return mixed
     */
    public static function getNum($table, $field, $id = 0)
    {
        $cacheKey = $table . '_' . $field;
        $cache    = self::cache($cacheKey);
        $key      = $field . '_num';
        
        if ($cache[$key] && $id) {
            return isset($cache[$key][$id])? intval($cache[$key][$id]) : 0;
        }
        
        return $cache ? $cache[$key] : '';
    }

    /**
     *  文件缓存，用于持久化缓存数据
     * @param $name
     * @param string $value
     * @return bool
     */
    public static function cache($name, $value = '')
    {
        $options = [
            'type'   => 'File',
            'expire' => 0,
            'path'   => RUNTIME_PATH . 'data/',
        ];
        $cache = Cache::connect($options);

        if (is_null($name)) {
            return false;
        }
        if ('' === $value) {
            return $cache->get($name);
        }
        if (is_null($value)) {
            // 删除缓存
            return $cache->rm($name);
        }
        return $cache->set($name, $value);
    }

    /**
     * 记录用户操作防刷新
     * @param  string|array $option 操作的字符串或者数组参数
     * @return bool
     */
    public static function isBan($option)
    {
        if (is_array($option)) {
            $action    = 'user_action_' . $option['action'];
            $limitTime = $option['limit_time'];
            $limitNum  = $option['limit_num'];
            $lockTime  = $option['lock_time'];
        } else {
            $action    = 'user_action_' . $option;
            $limitTime = 60;
            $limitNum  = 40;
            $lockTime  = 15 * 60;
        }

        //记录操作并缓存
        $spite   = session($action);
        $nowTime = time();

        //halt($spite);

        if (empty($spite)) {
            //初始化缓存
            $spite['num']        = 1;
            $spite['check_time'] = $nowTime + $limitTime;
            $spite['lock_time']  = '';
            session($action, $spite);
            return false;
        }

        if ($spite['check_time'] > $nowTime) {
            //锁15分钟
            if ($spite['num'] >= $limitNum) {
                $spite['lock_time'] = $nowTime + $lockTime;
                session($action, $spite);
                return true;
            }
            ++$spite['num'];
        } else {
            //未解锁
            if ($spite['lock_time'] > $nowTime) {
                return true;
            }
            //过期重置
            $spite['check_time'] = $nowTime + $limitTime;
            $spite['lock_time']  = '';
            $spite['num']        = 1;
        }
        session($action, $spite);
        return false;
    }

    /**
     * 操作间隔
     * @param string $action
     * @param int $time
     * @return bool
     */
    public static function isGap($action = '', $time = 60)
    {
        $spite   = session($action);
        $nowTime = time();

        if ($spite && ($spite['lock_time'] > $nowTime)) {
            return true;
        }

        $spite['lock_time'] = time() + $time;
        session($action, $spite);
        return false;
    }
    
    /**
     * 检测今天是否已经操作过
     * @param int|string $uid
     * @param string $action
     * @param int $id
     * @return bool
     */
    public static function isDayAllow($uid, $action = '', $id)
    {
        $cache = Cache::get($action);
        if (isset($cache[$id]) && in_array($uid, $cache[$id])) {
            return false;
        }
        
        $cache[$id][] = $uid;
        $end = strtotime(date('Y-m-d', strtotime('+1 day'))) - time();
        Cache::set($action, $cache, $end);
        return true;
    }
}
