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

class Picture extends Model
{
    /**
     * 检测文件是否存在.
     * @param  string $md5 文件MD5
     * @return  Boolean
     */
    public function isHave($md5)
    {
        $file = $this->where(['md5' => $md5])
            ->field('id,name,filepath,size')
            ->find();

        if (is_null($file)) {
            return false;
        } else {
            return $file->getData();
        }
    }
}
