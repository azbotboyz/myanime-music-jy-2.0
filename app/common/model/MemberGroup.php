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

class MemberGroup extends Model
{
    /**
     * @var bool 自动写入时间戳
     */
    protected $autoWriteTimestamp = true;

    /**
     * @var bool 自动写入状态吗
     */
    protected $insert = ['status' => 1];

    /**
     * 自动过滤标题
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
     * 自动过滤标题
     */
    protected function setIconFontAttr($value)
    {
        return text_filter($value);
    }

    /**
     * 自动解析规则
     */
    protected function setRuleAttr($value)
    {
        return !empty($value) ? json_encode($value) : '';
    }

    /**
     * 自动解析规则
     */
    protected function getRuleAttr($value)
    {
        return !empty($value) ? json_decode($value, true) : '';
    }

    /**
     * 获取组信息
     * @param  integer $gid
     * @return mixed
     */
    public function getInfo($gid = 0)
    {
        if (!$gid) {
            return '';
        }
        $list = Cache::get('user_group_lists');
        if (isset($list[$gid])) {
            return $list[$gid];
        }
        $info = self::where('id', $gid)
            ->where('status', 1)
            ->field('id,name,icon_url,icon_font,alias,rule')
            ->find();

        if ($info) {
            $info = $info->getData();
            $info['rule'] = json_decode($info['rule'], true);
        }
        return $info;
    }

    /**
     * 获取类型列表
     * @param  integer $limit
     * @param  string  $field
     * @return array
     */
    public function getList($limit = 99, $field = "id,name,icon_url,icon_font,alias,rule")
    {
        if (!$list = Cache::get('user_group_lists')) {
            $groups = self::where('status', 1)
                ->field($field)
                ->limit($limit)
                ->select();

            if ($groups) {
                foreach ($groups as $val) {
                    $val = $val->getData();
                    $val['rule'] = json_decode($val['rule'], true);
                    $list[$val['id']] = $val;
                }
            }
            Cache::tag('limit_cache')->set('user_group_lists', $list);
        }
        return $list;
    }


    /**
     * 获取用户组信息
     * @param  integer $uid
     * @return mixed
     */
    public function getUserIn($uid = 0)
    {
        $group = [];
        if ($uid) {
            $linkModel = new MemberGroupLink;
            $link = $linkModel->getUserlink($uid);
            $group = $this->getInfo($link['group_id']);
            $group['end_time'] = $link['end_time'];
        }
        unset($group['rule']);
        return $group;
    }
    
    /**
     * 将用户移除用户组
     * @param  integer $uid
     * @return mixed
     */
    public function removeUserIn($uid = 0)
    {
        $group = $this->getInfo(1);
        $linkModel = new MemberGroupLink;
        $linkModel->where('uid', $uid)->delete();
        return $group;
    }
    
}
