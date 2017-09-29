<?php
/**
 * JYmusic音乐管理系统
 *
 * @version     2.0
 * @author      战神~~巴蒂 [jyuucn@163.com]
 * @license     http://jyuu.cn [未经授权严禁私自出售，否者承担法律责任]
 * @copyright   2014 - 2017 JYmusic
 */
namespace app\common\taglib;

use think\template\TagLib;

/**
 * JYmusic 标签
 * @package
 */

class JY extends TagLib
{
    /**
     * 标签列表
     * @var array
     */
    protected $tags = [
        'songs'   => [
            'attr'  => 'result,res,page,id,no_id,artist,album,down,cate,genre,rank,tag,uid,cache,pos,status,field,limit,total,order',
            'level' => 3,
        ],
        'album'   => [
            'attr'  => 'result,res,id,page,artist,type,genre,uid,tags,cache,pos,status,field,limit,total,order',
            'level' => 3,
        ],
        'artist'  => [
            'attr'  => 'result,res,id,page,type,region,cache,tags,pos,sort,cache,status,field,limit,total,order',
            'level' => 3,
        ],
        'data'    => [
            'attr'  => 'result,res,name,field,limit,order,where,join,group,having,table',
            'level' => 2,
        ],
        'info'    => [
            'attr'  => 'result,res,id,name,page,cate,cache,pid,pos,type,limit,total,where,order,field',
            'level' => 3,
        ],
        'news'    => [
            'attr'  => 'result,res,id,name,page,cate,cache,pid,pos,type,limit,total,where,order,field',
            'level' => 3,
        ],
        'nav'     => ['attr' => 'result,res,cache,field,name,limit,total', 'close' => 1],
        'altype'  => ['attr' => 'result,res,id,page,cache,limit,field,total,order', 'level' => 3],
        'artype'  => ['attr' => 'result,res,id,page,cache,limit,field,total,order', 'level' => 3],
        'tags'    => ['attr' => 'result,res,id,page,group,alias,tree,field,cache,limit,total,order', 'level' => 3],
        'ranks'   => ['attr' => 'result,res,id,alias,page,cache,limit,field,total,order', 'level' => 3],
        'genre'   => ['attr' => 'result,res,id,alias,page,pid,cache,limit,field,total,order,tree', 'level' => 3],
        'member'  => ['attr' => 'result,res,uid,sex,is_musician,page,group,cache,status,limit,field,total,order', 'level' => 3],
        'site'    => ['attr' => 'result,res,id,page,cache,type,limit,order', 'level' => 3],
        'cate'    => ['attr' => 'result,res,id,alias,page,pid,cache,limit,field,total,order,tree', 'level' => 3],
        'down'    => ['attr' => 'result,uid,page,field,limit,cache,order', 'level' => 3],
        'audit'   => ['attr' => 'result,uid,page,field,limit,cache,order', 'level' => 3],
        'back'    => ['attr' => 'result,uid,page,field,limit,cache,order', 'level' => 3],
        'fav'     => ['attr' => 'result,name,uid,page,field,limit,cache,order', 'level' => 3],
        'fans'    => ['attr' => 'result,uid,page,limit,field,cache,order', 'level' => 3],
        'follow'  => ['attr' => 'result,uid,page,limit,field,cache,order', 'level' => 3],
        'visitor' => ['attr' => 'result,uid,page,limit,field,cache,order', 'level' => 3],
        'count'   => ['attr' => 'name,artist,genre,album,uid', 'close' => 0],
        'css'     => ['attr' => 'root,file', 'close' => 0],
        'js'      => ['attr' => 'root,file', 'close' => 0],
        'prev'    => ['attr' => 'result,res,name,data', 'close' => 1],
        'next'    => ['attr' => 'result,res,name,data', 'close' => 1],
        'loop'    => ['attr' => 'res,name,key,offset,length,mod'],
        'url'     => ['attr' => 'name,link,vars,suffix,domain', 'close' => 0, 'expression' => true],
    ];

    /* 导航列表 */
    public function tagNav($tag, $content)
    {
        $field = !isset($tag['field']) ? 'true' : $tag['field'];
        $cache = !isset($tag['cache']) ? 86400 : intval($tag['cache']);
        $limit = !isset($tag['limit']) ? 20 : intval($tag['limit']);
        $tree  = !isset($tag['tree']) ? false : true;
        $name  = isset($tag['result']) ? $tag['result'] : 'nav';
        $parse = '<?php ';
        $parse .= '$navModel = new \app\common\model\Channel();$__NAV__ = $navModel->getList([], ' . $limit . ',' . $field . ',' . $cache . ');';
        if ($tree) {
            $parse .= '$__NAV__ = list_to_tree($__NAV__, "id", "pid", "_child");';
        }
        $parse .= '?>{foreach name="__NAV__" item="' . $name . '"}';
        $parse .= '<?php $' . $name . '= $navModel->parseData($' . $name . '); ?>';
        $parse .= $content;
        $parse .= '{/foreach}';
        return $parse;
    }

    /**
     * 歌曲标签
     * @param  array $tag
     * @param  string $content
     * @return string
     */
    public function tagSongs($tag, $content)
    {
        $tag['tag_name'] = 'songs';
        return $this->tagMusicList($tag, $content);
    }

    /**
     * 专辑标签
     * @param  array $tag
     * @param  string $content
     * @return string
     */
    public function tagAlbum($tag, $content)
    {
        $tag['tag_name'] = 'album';
        return $this->tagMusicList($tag, $content);
    }

    /**
     * 会员标签
     * @param  array $tag
     * @param  string $content
     * @return string
     */
    public function tagMember($tag, $content)
    {
        $tag['tag_name'] = 'member';
        return $this->tagMusicList($tag, $content);
    }

    /**
     * 歌手标签
     * @param  array $tag
     * @param  string $content
     * @return string
     */
    public function tagArtist($tag, $content)
    {
        $tag['tag_name'] = 'artist';
        return $this->tagMusicList($tag, $content);
    }

    /**
     * 曲风标签
     * @param  array $tag
     * @param  string $content
     * @return string
     */
    public function tagGenre($tag, $content)
    {
        $tag['tag_name'] = 'genre';
        return $this->tagMusicList($tag, $content);
    }

    /**
     * 榜单标签
     * @param  array $tag
     * @param  string $content
     * @return string
     */
    public function tagRanks($tag, $content)
    {
        $tag['tag_name'] = 'ranks';
        return $this->tagMusicList($tag, $content);
    }

    /**
     * 风格标签
     * @param  array $tag
     * @param  string $content
     * @return string
     */
    public function tagTags($tag, $content)
    {
        $tag['tag_name'] = 'tags';
        return $this->tagMusicList($tag, $content);
    }

    /**
     * 专辑分类标签
     * @param  array $tag
     * @param  string $content
     * @return string
     */
    public function tagAltype($tag, $content)
    {
        $tag['tag_name'] = 'altype';
        $tag['model']    = 'albumType';
        return $this->tagMusicList($tag, $content);
    }

    /**
     * 专辑分类标签
     * @param  array $tag
     * @param  string $content
     * @return string
     */
    public function tagArtype($tag, $content)
    {
        $tag['tag_name'] = 'artype';
        $tag['model']    = 'artistType';
        return $this->tagMusicList($tag, $content);
    }

    /**
     * 资讯标签
     * @param  array $tag
     * @param  string $content
     * @return string
     */
    public function tagNews($tag, $content)
    {
        $tag['model'] = 'Article';
        return $this->tagMusicList($tag, $content);
    }

    /**
     * 资讯标签
     * @param  array $tag
     * @param  string $content
     * @return string
     */
    public function tagInfo($tag, $content)
    {
        $tag['tag_name'] = 'news';
        return $this->tagNews($tag, $content);
    }

    /**
     * 资讯分类标签
     * @param  array $tag
     * @param  string $content
     * @return string
     */
    public function tagCate($tag, $content)
    {
        $tag['tag_name'] = 'cate';
        $tag['model']    = 'ArticleCategory';
        return $this->tagMusicList($tag, $content);
    }

    /**
     * 网站帮助标签
     * @param  array $tag
     * @param  string $content
     * @return string
     */
    public function tagSite($tag, $content)
    {
        $tag['tag_name'] = 'site';
        return $this->tagMusicList($tag, $content);
    }

    /**
     * 获取歌曲下载
     * @param  array $tag
     * @param  string $content
     * @return string
     */
    public function tagDown($tag, $content)
    {
        $tag['tag_name'] = 'songs';
        $tag['call']     = 'getDownList';
        return $this->tagMusicList($tag, $content);
    }
    
    /**
     * 获取待审歌曲
     * @param  array $tag
     * @param  string $content
     * @return string
     */
    public function tagAudit($tag, $content)
    {
        $tag['tag_name'] = 'songs';
        $tag['status']     = 2;
        return $this->tagMusicList($tag, $content);
    }
    
    /**
     * 获取审核不通过歌曲
     * @param  array $tag
     * @param  string $content
     * @return string
     */
    public function tagBack($tag, $content)
    {
        $tag['tag_name'] = 'songs';
        $tag['status']   = 0;
        return $this->tagMusicList($tag, $content);
    }

    /**
     * 获取歌曲下载
     * @param  array $tag
     * @param  string $content
     * @return string
     */
    public function tagFav($tag, $content)
    {
        if ($tag['name'] == 'artist') {
            $tag['tag_name'] = 'artist';
        } elseif ($tag['name'] == 'album') {
            $tag['tag_name'] = 'album';
        } else {
            $tag['tag_name'] = 'songs';
        }
        $tag['call'] = 'getFavList';
        return $this->tagMusicList($tag, $content);
    }

    /**
     * 用户粉丝标签
     * @param  array $tag
     * @param  string $content
     * @return string
     */
    public function tagFollow($tag, $content)
    {
        $tag['tag_name'] = 'member';
        $tag['call']     = 'getFollow';
        $tag['uid']      = isset($tag['uid']) ? $tag['uid'] : '$user["uid"]';
        return $this->tagMusicList($tag, $content);
    }

    /**
     * 用户粉丝标签
     * @param  array $tag
     * @param  string $content
     * @return string
     */
    public function tagFans($tag, $content)
    {
        $tag['tag_name'] = 'member';
        $tag['call']     = 'getFans';
        $tag['uid']      = isset($tag['uid']) ? $tag['uid'] : '$user["uid"]';
        return $this->tagMusicList($tag, $content);
    }

    /**
     * 用户访客标签
     * @param  array $tag
     * @param  string $content
     * @return string
     */
    public function tagVisitor($tag, $content)
    {
        $tag['tag_name'] = 'member';
        $tag['call']     = 'getVisitors';
        $tag['uid']      = isset($tag['uid']) ? $tag['uid'] : '$user["uid"]';
        return $this->tagMusicList($tag, $content);
    }
    
    /**
     * 用户访客标签
     * @param  array $tag
     * @param  string $content
     * @return string
     */
    public function tagCount($tag, $content)
    {
        $result = isset($tag['result']) ? $tag['result'] : 'count';
        $name = isset($tag['name']) ? $tag['name'] : 'songs';
        $field = null;
        $val = null;
        if (isset($tag['uid'])) {
            $field = $name == 'songs'? 'up_uid' : 'add_uid';
            $val   = $tag['uid'];
        }
        if (isset($tag['artist'])) {
            $field = 'artist_id';
            $val  = $tag['artist'];
        }
        
        if (isset($tag['album'])) {
            $field = 'album_id';
            $val   = $tag['album'];
        }
        if (isset($tag['genre'])) {
            $field = 'genre_id';
            $val   = $tag['genre'];
        }

        $parse = '<?php ';
        if (0 === strpos($val, '$')) {
            $parse .= '$'.$result.' = getDataCount("'.$name.'","'. $field .'",'.$val.');';
        } else {
            $parse .= '$' . $result . ' = getDataCount("' . $name . '","' . $field . '","' . $val . '");';
        }
        $parse .= 'echo $'.$result.';';
        $parse .= ' ?>';
        
        return $parse;
    }
    
    /**
     * 上一条标签
     * @param  array $tag
     * @param  string $content
     * @return string
     */
    public function tagPrev($tag, $content)
    {
        $result = isset($tag['result']) ? $tag['result'] : 'prev';
        $name   = isset($tag['name']) ? ucfirst($tag['name']) : 'Songs';

        if ($name == "Info" || $name == 'Article') {
            $url   = $name   = 'Article';
            $field = "id,title";
        } else {
            $url   = $name != 'Songs' ? $name : 'Music';
            $field = "id,name";
        }

        $info     = !empty($tag['data']) ? $tag['data'] : 'data';
        $modelStr = '$' . $name . 'Model';
        $parse    = '<?php ' . $modelStr . ' = isset(' . $modelStr . ')?' . $modelStr . ':new \app\common\model\\' . $name . '();';
        $parse .= '$' . $result . '= ' . $modelStr . '->field("' . $field . '")->order("id DESC")->where(["status"=>1,"id"=>["lt", $' . $info . '["id"]]])->find();';
        $parse .= 'if(empty($' . $result . ')): $' . $result . '= ' . $modelStr . '->field("' . $field . '")->order("id DESC")->find();?><?php endif; ?>';
        $parse .= '<?php $' . $result . '["url"]=url(\'/' . strtolower($url) . '/\'.$' . $result . '[\'id\']); ?>  ';
        $parse .= $content;
        return $parse;
    }

    /**
     * 下一条标签
     * @param  array $tag
     * @param  string $content
     * @return string
     */
    public function tagNext($tag, $content)
    {
        $result = isset($tag['result']) ? $tag['result'] : 'next';
        $name   = isset($tag['name']) ? ucfirst($tag['name']) : 'Songs';
        if ($name == "Info" || $name == 'Article') {
            $url   = $name   = 'Article';
            $field = "id,title";
        } else {
            $url   = $name != 'Songs' ? $name : 'Music';
            $field = "id,name";
        }

        $info     = !empty($tag['data']) ? $tag['data'] : 'data';
        $modelStr = '$' . $name . 'Model';
        $parse    = '<?php ' . $modelStr . ' = isset(' . $modelStr . ')? ' . $modelStr . ':new \app\common\model\\' . $name . '();';
        $parse .= '$' . $result . '=' . $modelStr . '->field("' . $field . '")->order("id asc")->where(["status"=>1,"id"=>["gt", $' . $info . '["id"]]])->find();';
        $parse .= 'if(empty($' . $result . ')): $' . $result . '= ' . $modelStr . '->field("' . $field . '")->order("id asc")->find();?><?php endif; ?>';
        $parse .= '<?php $' . $result . '["url"]=url(\'/' . strtolower($url) . '/\'.$' . $result . '[\'id\']); ?>  ';
        $parse .= $content;
        return $parse;
    }

    /**
     * url函数的tag标签
     * 格式：{JY:url name="" /}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string
     */
    public function tagUrl($tag)
    {
        $url  = isset($tag['name']) ? $tag['name'] : '/';
        $key  = strtolower($url);
        $urls = [
            'album'             => '"home/Album/index"',
            'album_type'        => '"home/AlbumType/index"',
            'artist'            => '"home/Artist/index"',
            'artist_type'       => '"home/ArtistType/index"',
            'genre'             => '"home/Genre/index"',
            'cate'              => '"home/Genre/index"',
            'tags'               => '"home/Tag/index"',
            'tag'               => '"home/Tag/index"',
            'ranks'             => '"home/Ranks/index"',
            'ranks_fire'        => '"home/Ranks/index",["name"=>"fire"]',
            'ranks_latest'      => '"home/Ranks/index",["name"=>"latest"]',
            'ranks_hot'         => '"home/Ranks/index",["name"=>"hot"]',
            'ranks_down'        => '"home/Ranks/index",["name"=>"down"]',
            'ranks_fav'         => '"home/Ranks/index",["name"=>"fav"]',
            'search'            => '"home/Search/index"',
            
            'user_captcha'      => '"user/Auth/captcha"',
            'user_pact'         => '"user/Auth/pact"',
            'user_login'        => '"user/Auth/login"',
            'user_sign'         => '"user/Auth/loginUser"',
            'user_signup'       => '"user/Auth/signup"',
            'user_join'         => '"user/Auth/createUser"',
            'user_logout'       => '"user/Auth/logout"',
            'user_findpwd'      => '"user/Auth/reset"',
            'oauth_weibo'       => '"user/oauth/index", ["driver"=>"weibo"]',
            'oauth_qq'          => '"user/oauth/index", ["driver"=>"qq"]',
            'oauth_wechat'      => '"user/oauth/wechat", ["driver"=>"wechat"]',
            'oauth_wechat_open' => '"user/oauth/wechat", ["driver"=>"wechat_open"]',
    
            'user'              => '"user/Index/index"',
            'user_home'         => '"user/Index/read", ["uid" => $user["uid"]]',
            'user_home_music'   => '"user/Index/music", ["uid" => $user["uid"]]',
            'user_home_album'   => '"user/Index/album", ["uid" => $user["uid"]]',
            'user_home_fans'    => '"user/Index/fans", ["uid" => $user["uid"]]',
            'user_home_follow'  => '"user/Index/follow", ["uid" => $user["uid"]]',
            'user_home_visitor' => '"user/Index/visitor", ["uid" => $user["uid"]]',

            'musician_auth'     => '"user/Musician/auth"',
            'musician_save'     => '"user/Musician/save"',
            'musician_update'   => '"user/Musician/update"',

            'user_set'          => '"user/Setting/index"',
            'user_send_set'     => '"user/Setting/update"',
            'user_avatar'       => '"user/Setting/avatar"',
            'user_send_avatar'  => '"user/Setting/changeAvatar"',
            'user_pwd'          => '"user/Setting/password"',
            'user_send_pwd'     => '"user/Setting/changePwd"',

            'user_account'      => '"user/Account/index"',
            'user_charge'       => '"user/Account/charge"',
            'user_upgrade'       => '"user/Account/upgrade"',

            'user_album'        => '"user/Album/index"',
            'user_album_audit'  => '"user/Album/audit"',
            'album_create'      => '"user/Album/create"',
            'album_save'        => '"user/Album/save"',
            'album_edit'        => '"user/Album/edit"',
            'album_update'      => '"user/Album/update"',

            'music_share'       => '"user/Music/create"',
            'music_save'        => '"user/Music/save"',
            'music_edit'        => '"user/Music/edit"',
            'music_update'      => '"user/Music/update"',
            'user_music'        => '"user/Music/index"',
            'user_music_index'  => '"user/Music/index"',
            'user_music_audit'  => '"user/Music/audit"',
            'user_music_back'  => '"user/Music/back"',
            'user_music_down'   => '"user/Music/down"',

            'user_fav'          => '"user/Fav/index"',
            'user_fav_music'    => '"user/Fav/songs"',
            'user_fav_songs'    => '"user/Fav/songs"',
            'user_fav_album'    => '"user/Fav/album"',
            'user_fav_artist'   => '"user/Fav/artist"',

            'user_relation'     => '"user/Relation/index"',
            'user_follow'       => '"user/Relation/follow"',
            'user_fans'         => '"user/Relation/fans"',

            'user_msg'      => '"user/Message/index"',
            'user_msg_send'      => '"user/Message/create"',
            'user_msg_del'      => '"user/Message/delete"',
            'user_notice'      => '"user/Notice/index"',
            'user_notice_del'  => '"user/Notice/delete"',
        ];
        
        if (isset($urls[$key])) {
            return '<?php echo url(' . $urls[$key] . ');?>';
        }
        $key = '/' . ltrim($key, '/');
        
        $vars   = isset($tag['vars']) ? $tag['vars'] : '';
        $suffix = isset($tag['suffix']) ? $tag['suffix'] : 'true';
        $domain = isset($tag['domain']) ? $tag['domain'] : 'false';
        return '<?php echo url("' . $key . '","' . $vars . '",' . $suffix . ',' . $domain . ');?>';
    }
    
    /**
     * 用户粉丝标签
     * @param  array $tag
     * @param  string $content
     * @return string
     */
    public function tagCss($tag)
    {
        $root = isset($tag['root']) ? $tag['root'] : '';
        $tag['call']     = 'getFans';
        
        return '<?php echo (new \app\services\Assets())->loadCss("' . $root . '","' . $tag['file'] . '"); "\r\n" ?>';
    }

    /**
     * 通用列表解析
     * @param  array $tag
     * @param  string $content
     * @return string
     */
    protected function tagMusicList($tag, $content)
    {
        $tag['item']  = isset($tag['result']) ? $tag['result'] : 'jy';
        $tag['key']   = !empty($tag['key']) ? $tag['key'] : 'key';
        $tag['empty'] = isset($tag['empty']) ? $tag['empty'] : '';
        $call         = isset($tag['call']) ? $tag['call'] : 'getList';

        $parse = '<?php $param = []; $param["status"]=1;';
        $attrs = explode(",", $this->tags[$tag['tag_name']]['attr']);
        
        foreach ($attrs as $val) {
            if (isset($tag[$val])) {
                if (is_numeric($tag[$val]) || 0 === strpos($tag[$val], '$') || 0 === strpos($tag[$val], ':')) {
                    $parse .= '$param["' . $val . '"] = ' . $tag[$val] . ';';
                } else {
                    $parse .= '$param["' . $val . '"] = "' . $tag[$val] . '";';
                }
            }
        }

        $model    = isset($tag['model']) ? $tag['model'] : $tag['tag_name'];
        $modelStr = '$' . $model . 'Model';
        $parse .= $modelStr . ' = isset(' . $modelStr . ')?' . $modelStr . ':new \app\common\model\\' . ucfirst($model) . '();$__MUSIC__ = ' . $modelStr . '->' . $call . '($param);';
        if (isset($tag['page'])) {
            $parse .= 'if(!empty($__MUSIC__)) : ';
            $parse .= '$' . $tag['item'] . '_page = $__MUSIC__->render();' . '$' . $tag['item'] . '_total = $__MUSIC__->total();';
            $parse .= ' else: $' . $tag['item'] . '_page = null; endif;';
        }

        $tag['name'] = '$__MUSIC__';
        $parse       = $this->gteEachStr($tag, $parse);

        if (!isset($tag['tree'])) {
            $parse .= '<?php $' . $tag['item'] . ' = ' . $modelStr . '->parseData($' . $tag['item'] . '); ?>';
        }
        $parse .= $content;
        $parse .= '<?php endforeach; endif; else: echo "' . $tag['empty'] . '" ;endif; ?>';

        return $parse;
    }

    /**
     * loop标签解析 循环输出数据集
     * 格式：{JY:loop name="userList" id="user" key="key" index="i" mod="2" offset="3" length="5" empty=""}
     * {user.username}
     * {user.email}
     * {/JY:loop}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string
     */
    public function tagLoop($tag, $content)
    {
        $name         = $tag['name'];
        $tag['key']   = !empty($tag['key']) ? $tag['key'] : 'key';
        $tag['item']  = isset($tag['result']) ? $tag['result'] : $tag['res'];
        $tag['empty'] = isset($tag['empty']) ? $tag['empty'] : '';

        // 允许使用函数设定数据集 <volist name=":fun('arg')" id="vo">{$vo.name}</volist>
        $parseStr = '<?php ';

        if (':' == substr($name, 0, 1)) {
            $name = $this->autoBuildVar($name);
            $parseStr .= '$_result=' . $name . ';';
            $name = '$_result';
        } else {
            $name = $this->autoBuildVar($name);
        }
        $tag['name'] = $name;
        $parseStr    = $this->gteEachStr($tag, $parseStr);

        //转换数据
        if (isset($tag['model'])) {
            $modelStr = '$' . $tag['model'] . 'Model';
            $parseStr .= '<?php ' . $modelStr . '= isset(' . $modelStr . ')?' . $modelStr . ':new \app\common\model\\' . ucfirst($tag['model']) . '();';
            $parseStr .= '$' . $tag['item'] . ' = ' . $modelStr . '->parseData($' . $tag['item'] . '); ?>';
        }

        $parseStr .= $content;
        $parseStr .= '<?php endforeach; endif; else: echo "' . $tag['empty'] . '" ;endif; ?>';

        return $parseStr;
    }

    protected function gteEachStr($tag, $parseStr)
    {
        $name   = $tag['name'];
        $empty  = $tag['empty'];
        $key    = $tag['key'];
        $index  = isset($tag['index']) ? $tag['index'] : 'i';
        $item   = isset($tag['result']) ? $tag['result'] : $tag['res'];
        $offset = !empty($tag['offset']) && is_numeric($tag['offset']) ? intval($tag['offset']) : 0;
        $length = !empty($tag['length']) && is_numeric($tag['length']) ? intval($tag['length']) : 'null';
        $parseStr .= 'if(is_array(' . $name . ') || ' . $name . ' instanceof \think\Collection || ' . $name . ' instanceof \think\Paginator): ';

        // 设置了输出数组长度
        if (0 != $offset || 'null' != $length) {
            $parseStr .= '$__JYLIST__ = is_array(' . $name . ') ? array_slice(' . $name . ',' . $offset . ',' . $length . ', true) : ' . $name . '->slice(' . $offset . ',' . $length . ', true); ';
        } else {
            $parseStr .= '$__JYLIST__ = ' . $name . ';';
        }

        $parseStr .= 'if( count($__JYLIST__)==0 ) : echo "' . $empty . '" ;';
        $parseStr .= 'else: ';
        // 设置索引;
        $parseStr .= '$' . $index . '=0; ';
        $parseStr .= 'foreach($__JYLIST__ as $' . $key . '=>$' . $item . '): ';
        if (isset($tag['mod'])) {
            $mod = (int) $tag['mod'];
            $parseStr .= '$mod = ($' . $index . ' % ' . $mod . '); ';
        }

        $parseStr .= '++$' . $index . '; ?>';

        return $parseStr;
    }
}
