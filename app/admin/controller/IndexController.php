<?php
/**
 * JYmusic音乐管理系统 PHP version 5.4+
 *
 * @version     2.0
 * @author      战神~~巴蒂 <jyuucn@163.com>
 * @license     http://jyuu.cn/license [未经授权严禁私自出售，否者承担法律责任]
 * @copyright   2014 - 2017 JYmusic
 */

namespace app\admin\controller;

use think\Cache;
use app\common\model\Songs;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;

class IndexController extends AdminController
{

    /**
     * @return \think\Response
     */
    public function index()
    {
        $songsAuditList = (new Songs())->getAuditList();
        return $this->fetch('', ['songsAuditList' => $songsAuditList]);
    }
    
    public function clear($type = 'page')
    {
        if ('page' == $type) {
            Cache::clear('limit_cache');
        } else {
            $adapter = new Local(RUNTIME_PATH);
            $filesystem = new Filesystem($adapter);
            try {
                if ('run' == $type) {
                    $filesystem->deleteDir('temp');
                } else {
                    $filesystem->deleteDir('cache');
                    $filesystem->deleteDir('log');
                    $filesystem->deleteDir('temp');
                }
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
        }
        $this->success("缓存清理完毕!");
    }
}
