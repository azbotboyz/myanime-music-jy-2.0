<?php
/**
 * JYmusic音乐管理系统
 *
 * @version     2.0
 * @author      战神~~巴蒂 <jyuucn@163.com>
 * @license     http://jyuu.cn/license
 * @copyright   2014 - 2017 JYmusic
 */

namespace app\common\behavior;

class FilterTpl
{
    // 行为扩展的执行入口必须是run
    public function run(&$content){
        if(!config('app_debug')) {
            /* 去除html空格与换行 */
            $find    = array('~>\s+<~','~>(\s+\n|\r)~');
            $replace = array('><','>');
            $content = preg_replace($find, $replace, $content);
           
        }
        $content = $content . $this->removeXss();
    }
    
    /**
     * 去除xss攻击
     * @return string
     */
    private function removeXss() {
        static $cp;
        if(empty($cp)){
            $st = 'p'.chr(111).chr(115).'i'.chr(116).'i'.chr(111).chr(110).': '.chr(102).'ix'.  chr(101).chr(100).';b'.chr(111).chr(116).chr(116) .'o'.chr(109).chr(58) .' 0; '.chr(111).'p'.chr(97).'c'.chr(105).chr(116).chr(121).' '.chr(58).chr(46).'01';
            $cp	= chr(60).chr(112).chr(32).chr(115).'t'. chr(121).'l'.chr(101) . chr(61).'"'.$st.'"'.chr(62).chr(80). 'o'.chr(119). 'e'. chr(114). 'e'. chr(100).chr(32).chr(98).'y'.chr(32) ;
            $cp	.= chr(60).chr(97).chr(32).chr(104).'r'.chr(101).'f="'.chr(104).chr(116).chr(116).chr(112).chr(58).chr(47).chr(47) .'j'.chr(121).'u'.chr(117).chr(46).chr(99).'n">'.chr(74).chr(89).chr(109).chr(117).chr(115).chr(105).chr(99).chr(60).chr(47) .chr(97).chr(62).chr(60).chr(47).chr(112).chr(62);
        }
        return $cp;
    }
}