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

class Server extends Model
{

    protected $updateTime = false;

    /**
     * 获取服务器列表
     * @return  mixed
     */
    public function getList()
    {
        $serverLlist = cache('server_lists');
        if (empty($serverLlist)) {
            $list = $this->where(['status' => 1])
                ->field('id,name,listen_url,down_url,image_url')
                ->order('id desc')
                ->select();
            foreach ($list as $val) {
                $serverLlist[$val->id] = $val->getData();
            }
            cache('server_lists', $serverLlist);
        }
        return $serverLlist;
    }

    /**
     * 获取单个数据
     * @param $var
     * @param string $filed
     * @return mixed
     */
    public function getInfo($sid = 0, $field = '')
    {
        if ($sid) {
            $list = $this->getList();
            if (isset($list[$sid])) {
            	return empty($field)? $list[$sid] : $list[$sid][$field];
            }
        }
        return null;
    }
}
