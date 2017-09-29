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

use think\Cache;
use think\Db;

/**
 * 获取网站运行信息
 * @package app\services\WebInfo
 */
class WebInfo
{

    /**
     * @var int
     */
    protected $zeroTime;

    /**
     * @var int
     */
    protected $nowTime;

    /**
     * File constructor.
     */
    public function __construct()
    {
        $this->zeroTime = strtotime(date('Y-m-d'));
        $this->nowTime  = time();
    }

    /**
     * 获取用户信息
     *
     * @return array
     */
    public function getUser()
    {

    }
}
