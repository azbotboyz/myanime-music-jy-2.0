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

use think\Model;

class SeoRule extends Model
{
    /**
     * @var bool 自动写入时间戳
     */
    protected $autoWriteTimestamp = false;

    /**
     * @var bool 自动写入状态吗
     */
    protected $insert = ['status' => 1];

    /** 自动过滤标题*/
    protected function setTitleAttr($value)
    {
        return text_filter($value);
    }

    /** 自动过滤标题*/
    protected function setTitleRuleAttr($value)
    {
        return text_filter($value);
    }

    /** 自动过滤*/
    protected function setKeywordsRuleAttr($value)
    {
        return text_filter($value);
    }

    /** 自动过滤*/
    protected function setDescriptionRuleAttr($value)
    {
        return text_filter($value);
    }

    /**
     * 获取列表
     * @return mixed
     */
    public function getList()
    {
        $rules = cache('seo_rules');
        if (empty($rules)) {
            $list = $this->where('status', 1)
                ->field('id,title', true)
                ->select();

            foreach ($list as $val) {
                $key         = $val['module'] . '_' . ucfirst($val['controller']) . '_' . $val['action'];
                $rules[$key] = $val;
            }
            unset($list);
            cache('seo_rules', $rules);
        }
        return $rules;
    }

    /**
     * 解析当前页面规则
     * @param $module
     * @param $controller
     * @param $action
     * @param $data
     * @return mixed
     */
    public function parseActive($module, $controller, $action, $data = [])
    {
        $rules = $this->getList();
        $key   = $module . '_' . $controller . '_' . $action;
        if (!isset($rules[$key])) {
            return false;
        }
        $rule = $rules[$key];
        unset($rules);

        $data['web_name']    = config('web_site_name');
        $data['web_title']   = config('web_site_title');
        $data['web_keyword'] = config('web_site_keyword');
        $data['web_desc']    = config('web_site_description');
        
      
        if (!isset($data['name'])) {
            if (isset($data['title'])) {
                $data['name'] = $data['title'];
            } elseif (isset($data['nickname'])) {
                $data['name'] = $data['nickname'];
            } else {
                $data['name'] = '';
            }
        }

        $data['title']       = isset($data['title']) ? $data['title'] : '';
        $data['artist_name'] = isset($data['artist_name']) ? $data['artist_name'] : '';
        $data['album_name']  = isset($data['album_name']) ? $data['album_name'] : '';
        $data['user_name']   = isset($data['user_name']) ? $data['user_name'] : '';
        $seo['title']       = $this->replace_meta($rule['title_rule'], $data);
        $seo['keyword']     = isset($data['keywords']) && !empty($data['keywords']) ? $data['keywords'] : $this->replace_meta($rule['keywords_rule'], $data);
        $seo['description'] = isset($data['description']) && !empty($data['description']) ? $data['description'] : $this->replace_meta($rule['description_rule'], $data);
        return $seo;
    }

    /** 规则替换字符 */
    protected function replace_meta($rule, $data)
    {
        return str_replace(
            ['{$web_site_name}', '{$web_site_title}', '{$web_site_keyword}', '{$web_site_description}', '{$name}', '{$album_name}', '{$artist_name}', '{$user_name}'],
            [$data['web_name'], $data['web_title'], $data['web_keyword'], $data['web_desc'], $data['name'], $data['album_name'], $data['artist_name'], $data['user_name']],
            $rule
        );
    }
}
