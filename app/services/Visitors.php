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

use think\Request;

/**
 * visitors 获取访客信息
 * @package app\services
 */
class Visitors
{

    /**
     * @var string
     */
    protected $local;

    /**
     * @var string
     */
    protected $root = null;

    /**
     * File constructor.
     */
//    public function __construct()
    //    {
    //
    //    }

    /**
     * 获取访客信息
     */
    public function getInfo()
    {

    }

    /**
     * 获取访客位置信息
     */

    /**
     * @param null $ip
     * @return bool|mixed
     */
    public function local($ip = null)
    {
        if (empty($ip)) {
            $request = Request::instance();
            $ip      = $request->ip();
        }
        $res = @file_get_contents('http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json&ip=' . $ip);

        if (empty($res)) {
            return false;
        }

        $json = json_decode($res, true);

        if (!isset($json['ret']) || $json['ret'] !== 1) {
            return false;
        }
        $result = [
            'address'        => $json['province'] . $json['city'],
            'address_detail' => [
                'country'  => $json['country'],
                'city'     => $json['city'],
                'province' => $json['province'],
            ],
            'ip'             => $ip,
        ];
        unset($json);
        return $result;
    }

    //获取当前用户地理位置
    public function local2($ip = null)
    {
        // $pos = cookie('positioning');
        //        if (empty($pos)){
        if (empty($ip)) {
            $request = Request::instance();
            $ip      = $request->ip();
        }

        $res = file_get_contents("http://api.map.baidu.com/location/ip?ak=6O3twG1jp2M3nYTZfvgbX2B7&ip={$ip}&coor=bd09ll");
        if (empty($res)) {
            return false;
        }

        $res = json_decode($res, true);

        if ($res['status'] !== 0) {
            return false;
        }

        $pos       = $res['content'];
        $pos['ip'] = $ip;
        return $pos;
    }
}
