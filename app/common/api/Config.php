<?php
/**
 * JYmusic音乐管理系统 PHP version 5.4+
 *
 * @version     2.0
 * @author      战神~~巴蒂 <jyuucn@163.com>
 * @license     http://jyuu.cn/license [未经授权严禁私自出售，否者承担法律责任]
 * @copyright   2014 - 2017 JYmusic
 */

namespace app\common\api;

class Config
{
    /**
     * 获取数据库中的配置列表
     * @return array 配置数组
     */
    public static function lists()
    {
        $config = cache('db_config_list');
        if (!$config) {

            $map  = ['status' => 1];
            $data = db('Config')->where($map)->field('type,name,value')->select();

            $config = [];
            if ($data && is_array($data)) {
                foreach ($data as $value) {
                    $config[$value['name']] = self::parse($value['type'], $value['value']);
                }
            }
            cache('db_config_list', $config, 0, 'noclear');
        }
        return $config;
    }

    /**
     * 根据配置类型解析配置
     * @param  integer $type  配置类型
     * @param  string  $value 配置值
     * @return string $value
     */
    private static function parse($type, $value)
    {
        switch ($type) {
            case 3: //解析数组
            case 6: //解析数组
                $array = preg_split('/[,;\r\n]+/', trim($value, ",;\r\n"));
                if (strpos($value, ':')) {
                    $value = array();
                    foreach ($array as $val) {
                        list($k, $v) = explode(':', $val);
                        $value[$k]   = $v;
                    }
                } else {
                    $value = $array;
                }
                break;
        }
        return $value;
    }
}
