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

use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local as LocalAdapter;

/**
 * Class File 文件服务
 * @package app\services
 */
class File {
    
    /**
     * @var LocalAdapter
     */
    protected $local;
    
    /**
     * @var string
     */
    protected $root = null;
    
    /**
     * File constructor.
     */
    public function __construct()
    {
        $this->local = new Filesystem(new LocalAdapter(ROOT_PATH));
        $root        = getenv('DOCUMENT_ROOT');
        if ($root !== ROOT_PATH) {
            $this->root = pathinfo($root, PATHINFO_BASENAME);
        }

    }
    
    /**
     * 移动addons cover
     *
     * @param mixed $dir
     * @return boolean
     */
    function moveAssonFiles ($dir=null)
    {
        $saveDir = $this->root . '/static/addons/' . $dir;
        //判断文件是否存在 存在删除文件
        $exists = $this->local->has($saveDir);
        
        if ($exists) {
            $this->local->deleteDir($saveDir);
        }

        $this->local->createDir($saveDir);
    
        //移动资源文件
        $copyDir = 'addons/' . $dir. '/assets';
        
        $contents = $this->local->listContents($copyDir, true);
        
        foreach ($contents as $val) {
            if ($val['type'] == 'file') {
                $saveFile   = $this->root . '/static/' . str_replace('/assets', '', $val['path'] );
                $this->local->copy($val['path'], $saveFile);
            }
        }

        //移动封面文件
        $copyFile = 'addons' . DS . $dir . DS . 'cover.png';
        if ($this->local->has($copyFile)) {
            $this->local->copy($copyFile, $saveDir . DS . 'cover.png');
        }

        return true;
    }
    
    /**
     * @param string $copyDir
     * @param string $saveDir
     */
    protected function copyDir($copyDir='', $saveDir='')
    {
        //$ = $this->local->listContents($copyDir, true);
        
    }
    
}