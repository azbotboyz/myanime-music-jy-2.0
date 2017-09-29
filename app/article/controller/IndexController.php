<?php
/**
 * JYmusic音乐管理系统 PHP version 5.4+ [未经授权严禁私自出售，否者承担法律责任]
 *
 * @version     2.0
 * @author      战神~~巴蒂 [jyuucn@163.com]
 * @license     LICENSE: http://jyuu.cn/license
 * @copyright   2014 - 2017 by JYmusic
 */

namespace app\article\controller;

use app\common\model\Article;
use app\common\model\ArticleCategory;

/**
 * 前台统一控制器
 */
class IndexController extends ArticleController
{
    /**
     * 资讯首页
     * @return mixed
     */
    public function index()
    {
        $this->seoMeta();
        return $this->fetch();
    }
    
    public function read($id)
    {
        $info = [];
        $map['status'] = 1;
        if ((int)$id) {
            $map['id'] = $id;
            $info = Article::get($map);
        }
        if (empty($info)) {
            abort(404,'你查看的资讯不存在！');
        }
        $content =  $info->content->getData();
        
        $info = $info->parseData();
        
        $cate = ArticleCategory::get($info['category_id']);
        if (empty($cate)) {
            $this->error('你查看的资讯所属分类不存在或被禁用！');
        }
        
        $info['cate'] =$cate->parseData();
        $info = array_merge($info, $content);
        unset($content);
        $this->seoMeta($info);
        
        return $this->fetch('article/detail', ['data' =>$info]);
    }
}
