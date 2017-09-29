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
use think\Paginator;

class Paginate extends Paginator
{

    /**
     * 上一页按钮
     * @param string $text
     * @return string
     */
    protected function getPreviousButton($text = "&laquo;")
    {

        if ($this->currentPage() <= 1) {
            return $this->getDisabledTextWrapper($text);
        }

        $url = $this->url(
            $this->currentPage() - 1
        );

        return $this->getPageLinkWrapper($url, $text);
    }

    /**
     * 下一页按钮
     * @param string $text
     * @return string
     */
    protected function getNextButton($text = '&raquo;')
    {
        if (!$this->hasMore) {
            return $this->getDisabledTextWrapper($text);
        }

        $url = $this->url($this->currentPage() + 1);
        return $this->getPageLinkWrapper($url, $text);
    }

    /**
     * 页码按钮
     * @return string
     */
    protected function getLinks()
    {
        if ($this->simple) {
            return '';
        }

        $block = [
            'first'  => null,
            'slider' => null,
            'last'   => null
        ];

        $side   = 3;
        $window = 5;

        if ($this->lastPage < $window) {
            $block['first'] = $this->getUrlRange(1, $this->lastPage);
        } elseif ($this->currentPage <= ($window -2)) {
            $start = ($window - $this->currentPage) + 3;
            $block['first'] = $this->getUrlRange(1, $window);
            $block['last']  = $this->getUrlRange($this->lastPage, $this->lastPage);
        } elseif ($this->currentPage > ($this->lastPage - $window)+1) {
            $block['first'] = $this->getUrlRange(1, 1);
            $block['last']  = $this->getUrlRange(($this->lastPage - $window)+1, $this->lastPage);
        } else {
            $block['first']  = $this->getUrlRange($this->currentPage-2, $this->currentPage+2);
            //$block['slider'] = $this->getUrlRange($this->currentPage-1, $this->currentPage+1);
            $block['last']   = $this->getUrlRange($this->lastPage, $this->lastPage);
        }

        $html = '';

        if (is_array($block['first'])) {
            $html .= $this->getUrlLinks($block['first']);
        }

        if (is_array($block['slider'])) {
            //$html .= $this->getDots();
            $html .= $this->getUrlLinks($block['slider']);
        }

        if (is_array($block['last'])) {
            $html .= $this->getDots();
            $html .= $this->getUrlLinks($block['last']);
        }

        return $html;
    }

    /**
     * 渲染分页html
     * @return mixed
     */
    public function render()
    {
        if ($this->hasPages()) {
            if ($this->simple) {
                return sprintf(
                    '<ul class="pager">%s %s</ul>',
                    $this->getPreviousButton(),
                    $this->getNextButton()
                );
            } else {
                return sprintf(
                    '<ul class="pagination">%s %s %s</ul>',
                    $this->getPreviousButton(),
                    $this->getLinks(),
                    $this->getNextButton()
                );
            }
        }
    }

    /**
     * 生成一个可点击的按钮
     *
     * @param  string $url
     * @param  int    $page
     * @return string
     */
    protected function getAvailablePageWrapper($url, $page)
    {
        $request = Request::instance();
        $curUrl = $request->url();
        $curPage = $request->route($this->options['var_page']);
        //halt($request);
        $pageNum = preg_replace('/\D/s', '', $url);
        if (!empty($curPage)) {
            $url = str_replace(
                $this->options['var_page'].'/'.$curPage,
                $this->options['var_page'].'/'.$pageNum,
                $curUrl
            );
        } else {
            $path = $request->path();
            $url = str_replace(
                $path,
                $path .'/'. $this->options['var_page'].'/'.$pageNum,
                $curUrl
            );
        }
        return '<li><a href="' . htmlentities($url) . '">' . $page . '</a></li>';
    }

    /**
     * 自动获取当前页码
     * @param string $varPage
     * @param int    $default
     * @return int
     */
    public static function getCurrentPage($varPage = 'page', $default = 1)
    {
        $page = Request::instance()->route($varPage);
        //halt(Request::instance());
      
        if (filter_var($page, FILTER_VALIDATE_INT) !== false && (int) $page >= 1) {
            return $page;
        }
        return $default;
    }

    /**
     * 生成一个禁用的按钮
     *
     * @param  string $text
     * @return string
     */
    protected function getDisabledTextWrapper($text)
    {
        return '<li class="disabled"><span>' . $text . '</span></li>';
    }

    /**
     * 生成一个激活的按钮
     *
     * @param  string $text
     * @return string
     */
    protected function getActivePageWrapper($text)
    {
        return '<li class="active"><span>' . $text . '</span></li>';
    }

    /**
     * 生成省略号按钮
     *
     * @return string
     */
    protected function getDots()
    {
        return $this->getDisabledTextWrapper('...');
    }

    /**
     * 批量生成页码按钮.
     *
     * @param  array $urls
     * @return string
     */
    protected function getUrlLinks(array $urls)
    {
        $html = '';

        foreach ($urls as $page => $url) {
            $html .= $this->getPageLinkWrapper($url, $page);
        }

        return $html;
    }

    /**
     * 生成普通页码按钮
     *
     * @param  string $url
     * @param  int    $page
     * @return string
     */
    protected function getPageLinkWrapper($url, $page)
    {
        if ($page == $this->currentPage()) {
            return $this->getActivePageWrapper($page);
        }

        return $this->getAvailablePageWrapper($url, $page);
    }
}
