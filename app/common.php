<?php
/**
 * JYmusic音乐管理系统
 *
 * @version     2.0
 * @author      战神~~巴蒂 <jyuucn@163.com>
 * @license     http://jyuu.cn [未经授权严禁私自出售，否者承担法律责任]
 * @copyright   2014 - 2017 JYmusic
 */

// 注册类的根命名空间
\think\Loader::addNamespace('addons', JYMUSIC_ADDON_PATH);

use app\services\Actions;

/**
 * 检测用户是否登录
 * @return integer 0-未登录，大于0-当前登录用户ID
 */
function is_login()
{
    $user = session('user_auth');
    if (empty($user)) {
        return 0;
    }
    return session('user_auth_sign') == data_auth_sign($user) ? $user['uid'] : 0;
}

/**
 * 记录行为日志，并执行该行为的规则
 *
 * @param string $action    行为标识
 * @param string $model     触发行为的模型名
 * @param int    $recordId 触发行为的记录id
 * @param int    $userId   执行行为的用户id
 *
 * @return boolean
 */
function action_log($action = null, $model = null, $recordId = null, $userId = null)
{
    //参数检查
    if (empty($action) || empty($model) || empty($recordId)) {
        return 'Parameter can not be empty';
    }
    if (empty($userId)) {
        $userId = is_login();
    }
    //查询行为,判断是否执行
    $actionInfo = db('Action')->column($action);
    if ($actionInfo['status'] != 1) {
        return 'The behavior is disabled or deleted';
    }

    //插入行为日志
    $data['action_id']   = $actionInfo['id'];
    $data['user_id']     = $userId;
    $data['action_ip']   = ip2long(get_client_ip());
    $data['model']       = $model;
    $data['record_id']   = $recordId;
    $data['create_time'] = time();

    //解析日志规则,生成日志备注
    if (!empty($actionInfo['log'])) {
        if (preg_match_all('/\[(\S+?)\]/', $actionInfo['log'], $match)) {
            $log['user']   = $userId;
            $log['record'] = $recordId;
            $log['model']  = $model;
            $log['time']   = time();
            $log['data']   = [
                'user'   => $userId,
                'model'  => $model,
                'record' => $recordId,
                'time'   => time(),
            ];
            $replace = [];
            foreach ($match[1] as $value) {
                $param = explode('|', $value);
                $replace[] = isset($param[1])? call_user_func($param[1], $log[$param[0]]) : $log[$param[0]];
            }
            $data['remark'] = str_replace($match[0], $replace, $actionInfo['log']);
        } else {
            $data['remark'] = $actionInfo['log'];
        }
    } else {
        //未定义日志规则，记录操作url
        $data['remark'] = 'Operation url:' . $_SERVER['REQUEST_URI'];
    }
    $res = false;
    db::name('ActionLog')->inster($data);
    if (!empty($actionInfo['rule'])) {
        //解析行为
        $rules = parse_action($action, $userId);
        //执行行为
        $res = execute_action($rules, $actionInfo['id'], $userId);
    }
    return $res;
}

/**
 * 系统非常规用户MD5加密方法
 * @param string $str 要加密的字符串
 * @param string $key 加密密钥
 * @return string
 */
function jy_ucenter_md5($str, $key = 'JYucenter')
{
    return '' === $str ? '' : md5(sha1($str) . $key);
}

/**
 * 系统加密方法
 * @param string $data 要加密的字符串
 * @param string $key  加密密钥
 * @param int $expire  过期时间 单位 秒
 * @return string
 */
function jy_encrypt($data, $key = '', $expire = 0)
{
    $key  = md5(empty($key) ? UC_AUTH_KEY : $key);
    $data = base64_encode($data);
    $def  = 0;
    $len  = strlen($data);
    $keyLen    = strlen($key);
    $char = '';
    for ($i = 0; $i < $len; $i++) {
        if ($def == $keyLen) {
            $def = 0;
        }
        $char .= substr($key, $def, 1);
        $def++;
    }

    $str = sprintf('%010d', $expire ? $expire + time() : 0);
    for ($i = 0; $i < $len; $i++) {
        $str .= chr(ord(substr($data, $i, 1)) + (ord(substr($char, $i, 1))) % 256);
    }
    return str_replace(array('+', '/', '='), array('-', '_', ''), base64_encode($str));
}

/**
 * 系统解密方法
 * @param  string $data 要解密的字符串 （必须是jy_encrypt方法加密的字符串）
 * @param  string $key  加密密钥
 * @return string
 */
function jy_decrypt($data, $key = '')
{
    $key  = md5(empty($key) ? UC_AUTH_KEY : $key);
    $data = str_replace(array('-', '_'), array('+', '/'), $data);
    $mod4 = strlen($data) % 4;
    if ($mod4) {
        $data .= substr('====', $mod4);
    }
    $data   = base64_decode($data);
    $expire = substr($data, 0, 10);
    $data   = substr($data, 10);

    if ($expire > 0 && $expire < time()) {
        return '';
    }
    $def    = 0;
    $len  = strlen($data);
    $keyLen    = strlen($key);
    $char = $str = '';

    for ($i = 0; $i < $len; $i++) {
        if ($def == $keyLen) {
            $def = 0;
        }

        $char .= substr($key, $def, 1);
        $def++;
    }

    for ($i = 0; $i < $len; $i++) {
        if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1))) {
            $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
        } else {
            $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
        }
    }
    return base64_decode($str);
}

/**
 * 检测当前用户是否为超级管理员
 * @param null|int $uid 要查询的用户id
 * @return boolean true-管理员，false-非管理员
 */
function is_administrator($uid = null)
{
    $uid = is_null($uid) ? is_login() : $uid;
    return $uid && (intval($uid) === config('user_administrator'));
}

/**
 * 数据签名认证
 *
 * @param array $data 被认证的数据
 * @return string       签名
 */
function data_auth_sign($data = [])
{
    //数据类型检测
    if (!is_array($data)) {
        $data = (array) $data;
    }
    ksort($data); //排序
    $code = http_build_query($data); //url编码并生成query字符串
    $sign = sha1($code); //生成签名
    return $sign;
}

/**
 * 对查询结果集进行排序
 * @param array  $list   查询结果
 * @param string $field  排序的字段名
 * @param string $sortby 排序类型 asc正向排序 desc逆向排序 nat自然排序
 * @return array|boolean
 */
function list_sort_by($list, $field, $sortby = 'asc')
{
    if (is_array($list)) {
        $refer = $resultSet = array();
        foreach ($list as $i => $data) {
            $refer[$i] = &$data[$field];
        }
        switch ($sortby) {
            case 'asc': // 正向排序
                asort($refer);
                break;
            case 'desc': // 逆向排序
                arsort($refer);
                break;
            case 'nat': // 自然排序
                natcasesort($refer);
                break;
        }
        foreach ($refer as $key => $val) {
            $resultSet[] = &$list[$key];
        }
        return $resultSet;
    }

    return false;
}

/**
 * 把返回的数据集转换成Tree
 *
 * @param object $list  要转换的数据集
 * @param string $pk    主键字段
 * @param string $pid   parent标记字段
 * @param string $child 输出child字段
 * @param int    $root  root标记字段
 *
 * @return array
 */
function list_to_tree($list, $pk = 'id', $pid = 'pid', $child = '_child', $root = 0)
{
    // 创建Tree
    $tree = [];
    if (is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            //$data   = $val->data();
            if (is_object($data)) {
                $data       = $data->toArray();
                $list[$key] = $data;
            }

            $refer[$data[$pk]] = &$list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId = $data[$pid];
            if ($root == $parentId) {
                $tree[] = &$list[$key];
            } else {
                if (isset($refer[$parentId])) {
                    $parent           = &$refer[$parentId];
                    $parent[$child][] = &$list[$key];
                }
            }
        }
    }
    return $tree;
}

/**
 * 将list_to_tree的树还原成列表
 *
 * @param array  $tree  原来的树
 * @param string $child 孩子节点的键
 * @param string $order 排序显示的键，一般是主键 升序排列
 * @param array  $list  过渡用的中间数组，
 *
 * @return array 返回排过序的列表数组
 */
function tree_to_list($tree, $child = '_child', $order = 'id', &$list = array())
{
    if (is_array($tree)) {
        foreach ($tree as $key => $value) {
            $reffer = $value;
            if (isset($reffer[$child])) {
                unset($reffer[$child]);
                tree_to_list($value[$child], $child, $order, $list);
            }
            $list[] = $reffer;
        }
        $list = list_sort_by($list, $order, $sortby = 'asc');
    }
    return $list;
}

/**
 * 不区分大小写的in_array
 *
 * @param string $value 要查找的字符串
 * @param array  $array 查找的数组
 *
 * @return bool
 */
function in_array_case($value, $array)
{
    return in_array(strtolower($value), array_map('strtolower', $array));
}

/**
 * 处理插件钩子
 * @param string $hook   钩子名称
 * @param mixed $params 传入参数
 * @return void
 */
function hook($hook, $params = [])
{
    \Think\Hook::listen($hook, $params);
}

/**
 * 获取插件类的类名
 * @param strng $name 插件名
 * @return string
 */
function get_addon_class($name)
{
    return "\\addons\\{$name}\\{$name}";
}

/**
 * 获取插件模型类
 * @param string $addons 插件名
 * @param string $name 插件名
 * @return string
 */
function get_addon_model($addons, $name = '')
{
    $addons = ucfirst($addons);
    $name = !empty($name)? ucfirst($name) : $addons;
    return "\\addons\\{$addons}\\model\\{$name}";
}

/**
 * 获取插件配置信息
 * @param string $name 插件名
 * @return array
 */
function get_addon_config($name)
{
    static $config = [];

    if (isset($config[$name])) {
        return $config[$name];
    }

    $config = cache('addons_' . $name . '_config');
    if (!$config) {
        $config = db('Addons')->where(['status' => 1, 'name' => $name])
            ->value('config');

        if ($config) {
            $config = json_decode($config, true);
            cache('addons_' . ucfirst($name) . '_config', $config);
        }
    }
    return $config;
}

/**
 * 插件显示内容里生成访问插件的url
 * @param string $url url
 * @param array $param 参数
 * @return  string
 */
function addons_url($url, $param = array(), $suffix = true, $domain = false)
{
    $url    = parse_url($url);
    $paths  = explode('/', trim($url['path'], '/'));
    
    $addon['addons'] = $paths[0];
    $addon['controller'] = isset($paths[1])? strtolower($paths[1]) : 'index';
    $addon['action'] = isset($paths[2])? strtolower($paths[2]) : 'index';
    
    if (!empty($param)) {
        if (is_array($param)) {
            $param =  http_build_query($param);
        } else {
            $param =  ltrim($param, '?=');
        }
    }
    
    if (isset($url['query'])) {
        $param =  $url['query'] . '&' . $param;
    }
    
    if (request()->module() == 'admin') {
        $url = url('/apply/' . $addon['addons'] . '/' . $addon['controller'] . '/' .$addon['action'], $param,  $suffix, $domain);
    } else {
        $url = url('home/apply/index', $addon, $suffix, $domain);
        if (config('url_model') == 4 && !empty($param)) {
            $suffix = '.' .config('url_html_suffix');
            $param = str_replace(['&', '=', '?='], '/', $param);
            $url = str_replace($suffix, '/', $url) . $param . $suffix;
        } elseif(!empty($param)) {
            $url = $url .'?'. $param;
        }
    }
    return $url;
}

/**
 * text_filter函数用于过滤标签，输出没有html的干净的文本
 * @param string $text 文本内容
 * @return string 处理后内容
 */
function text_filter($text)
{
    $text = nl2br($text);
    $text = real_strip_tags($text);
    $text = addslashes($text);
    $text = trim($text);
    return $text;
}

function real_strip_tags($str, $allowable_tags = "")
{
    $str = html_entity_decode($str, ENT_QUOTES, 'UTF-8');
    return strip_tags($str, $allowable_tags);
}

/**
 * 过滤Emoji表情
 * @param $str
 * @return mixed
 */
function filterEmoji($str)
{
    $str = preg_replace_callback('/./u', function (array $match) {
        return strlen($match[0]) >= 4 ? '' : $match[0];
    }, $str);
    return $str;
}

/**
 * 字符串截取，支持中文和其他编码
 * @static
 * @access public
 *
 * @param string $str     需要转换的字符串
 * @param string $start   开始位置
 * @param string $length  截取长度
 * @param string $charset 编码格式
 * @param string $suffix  截断显示字符
 *
 * @return string
 */
function msubstr($str, $start = 0, $length, $charset = "utf-8", $suffix = true)
{
    $strLen = mb_strlen($str,'utf-8');
    $slice = mb_substr($str, $start, $length, $charset);
    return $suffix && ($strLen > $length) ? $slice . '...' : $slice;
}

/**
 * 获取客户端IP地址
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @param boolean $adv  是否进行高级模式获取（有可能被伪装）
 * @return mixed
 */
function get_client_ip($type = 0, $adv = false)
{
    return request()->ip($type, $adv);
}

/**
 * 格式化字节大小
 * @param  number $size      字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 */
function format_bytes($size, $delimiter = '')
{
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    for ($i = 0; $size >= 1024 && $i < 5; $i++) {
        $size /= 1024;
    }
    return round($size, 2) . $delimiter . $units[$i];
}

/**
 * 时间戳格式化
 * @param int $time
 * @return string 完整的时间显示
 */
function time_format($time = null, $format = 'Y-m-d H:i:s')
{
    if (strpos($time, '-')) {
        return date($format, strtotime($time));
    }
    
    $time = $time === null ? time() : intval($time);
    return date($format, $time);
}

/**
 * 字符串转换为数组，主要用于把分隔符调整到第二个参数
 * @param  string $str  要分割的字符串
 * @param  string $glue 分割符
 * @return array
 */
function str2arr($str, $glue = ',')
{
    return explode($glue, $str);
}

/**
 * 数组转换为字符串，主要用于把分隔符调整到第二个参数
 * @param  array  $arr  要连接的数组
 * @param  string $glue 分割符
 * @return string
 */
function arr2str($arr, $glue = ',')
{
    return implode($glue, $arr);
}

/**
 * 防超时的file_get_contents改造函数
 * @param $url
 * @return string
 */
function jy_file_get_contents($url)
{
    $context = stream_context_create([
        'http' => [
            'timeout' => 30,
        ],
    ]); // 超时时间，单位为秒

    return file_get_contents($url, 0, $context);
}

/**
 * 字符串转拼音
 * @param  string  $value
 * @param  string  $name
 * @param  integer $limit
 * @return string
 */
function get_pinyin($name = "", $value = '', $limit = 6)
{
    if (ctype_alnum($name)) {
        return strtolower($name);
    }
    if (!empty($value)) {
        return $value;
    }
    if (mb_strlen($name) > $limit) {
        $name = mb_substr($name, 0, $limit, 'utf-8');
    }

    $pinyin = new \app\services\PinYin();
    return $pinyin->convert($name);
}

/**
 * 根据用户ID获取用户基本信息
 * @param  integer $uid 用户ID
 * @return array 用户昵称
 */
function get_user_info($uid = 0)
{
    $api =  new \app\common\api\User();
    return $api->info($uid);
}

/**
 * 重置用户缓存信息
 * @param  integer $uid 用户ID
 * @return void
 */
function clear_user_info($uid = 0)
{
    $list = cache('sys_user_info_list');
    /* 查找用户信息 */
    $key = "u{$uid}";
    if (isset($list[$key])) {
        //已缓存，清除缓存
        unset($list[$key]);
        cache('sys_user_info_list', $list);
    }
}

/**
 * 根据用户ID获取用户基本信息
 * @param  integer $uid 用户ID
 * @return mixed 用户昵称
 */
function get_nickname($uid = 0)
{
    if (!intval($uid)) {
        return '';
    }
    $user = get_user_info($uid);
    return isset($user['nickname'])? $user['nickname'] : '';
}

/**
 * 获取用户头像
 * @param  mixed
 * @return string
 */
function get_user_avatar($user)
{
    $avatar = '';
    if (is_numeric($user)) {
        $user = get_user_info($user);
        $avatar = isset($user['avatar'])? $user['avatar'] : $avatar;
    } elseif (is_array($user)) {
        $avatar = isset($user['avatar'])? $user['avatar'] : $avatar;
    }
    $avatar = !is_numeric($avatar) ? $avatar : '';
    return get_cover_url($avatar, 'avatar');
}

/**
 * 是否允许上传头像
 * @param int $uid
 * @return bool
 */
function allow_send_set($uid = 0, $action = 'avatar')
{
    $key = "u{$uid}";
    $limit = cache('user_send_' . $action );
    $max = intval(config('user_send_limit'));
    if (isset($limit[$key])) {
        $nowTime = time();
        $endTime = $limit[$key]['start_time'] + (30*24*3600);
        if ($endTime > $nowTime && ($limit[$key][$action . '_num'] >= $max)) {
            return false;
        }
        if ($endTime < $nowTime) {
            $limit[$key]['start_time'] = $nowTime;
            $limit[$key][ $action. '_num'] = 0;
            cache('user_send_' . $action, $limit);
        }
        //$max = $max - $limit[$key]['upload_num'];
    }
    return true;
}

/**
 * 记录用户操作
 * @param int $uid
 * @param string $action
 */
function set_user_send($uid = 0, $action = 'avatar') {
    
    $key = "u{$uid}";
    $limit = cache('user_send_'. $action);
    if (empty($limit) || !isset($limit[$key])) {
        $limit[$key]['start_time'] = time();
        $limit[$key][$action . '_num'] = 1;
    } else {
        ++$limit[$key][$action . '_num'];
    }
    cache('user_send_' . $action, $limit);
}

/**
 * 获取用户组信息
 * @return mixed
 */
function get_user_groups($sid = 0)
{
    $model = new \app\common\model\MemberGroup();
    if (intval($sid)) {
        return $model->getInfo($sid);
    }
    return $model->getList();
}

/**
 * 获取用户所在用户组
 * @param  mixed
 * @return string
 */
function get_user_in_group($uid)
{
    $model = new \app\common\model\MemberGroup();
    return $model->getUserIn($uid);
}

/**
 * 获取曲风分类树
 * @return mixed
 */
function get_genre_tree($cid = 0, $field = '', $format = false)
{
    $genre = new \app\common\model\Genre();
    return $genre->getTree($cid, $field, $format);
}

/**
 * 获取资讯分类
 * @return mixed
 */
function get_article_cate($cid)
{
    $cate = new \app\common\model\ArticleCategory();
    return $cate->getInfo($cid);
}

/**
 * 获取资讯分类树
 * @return mixed
 */
function get_article_cate_tree($cid = 0, $field = '', $format = false)
{
    $cate = new \app\common\model\ArticleCategory();
    return $cate->getTree($cid, $field, $format);
}

/**
 * 获取资讯分类
 * @return mixed
 */
function get_article_cate_title($cid)
{
    $cate = get_article_cate($cid);
    return $cate ? $cate['title'] : $cate;
}

/**
 * 获取专辑类型
 * @return mixed
 */
function get_album_types()
{
    $album = new \app\common\model\AlbumType();
    return $album->getList(['order'=>'id asc']);
}

/**
 * 获取专辑类型
 * @return mixed
 */
function get_artist_types()
{
    $album = new \app\common\model\ArtistType();
    return $album->getList(['order'=>'id asc']);
}

/**
 * 获取排行榜单
 * @param bool $isAddSys
 * @return mixed
 */
function get_ranks($isAddSys = false)
{
    $model = new \app\common\model\Ranks();
    return $model->getList(['order'=>'sort asc,id desc'], $isAddSys);
}

/**
 * 获取服务器组
 * @param int $sid
 * @return mixed
 */
function get_server($sid = 0)
{
    $sever = new \app\common\model\Server();
    if (intval($sid) > 0) {
        return $sever->getInfo($sid);
    }
    return $sever->getList();
}

/**
 * 返回单个数据的简单信息
 * @param $id
 * @param $name
 * @return mixed
 */
function get_simple_info($id, $name)
{
    $name =ucfirst($name);
    $model = "\\app\\common\\model\\" . $name;
    return (new $model())->simpleInfo($id);
}

/**
 * 发送系统通知
 * @param array $data
 * @return mixed
 */
function send_notice($data = [])
{
    $notice = new \app\common\api\Notice($data);
    return $notice->send();
}

/**
 * 获取封面地址
 * @param  array|string|intger $image
 * @param  string $type
 * @return string
 */
function get_cover_url($image = '', $type = 'songs')
{
    if (is_array($image)) {
        $image = $image['cover_url'];
    }
    if (empty($image)) {
        switch ($type) {
            case 'songs':
                $detfault  = 'df_scover.png';
                break;
            case 'artist':
                $detfault  = 'df_arcover.png';
                break;
            case 'album':
                $detfault  = 'df_alcover.png';
                break;
            case 'avatar':
                $detfault  = 'df_avatar.png';
                break;
            default:
                $detfault = 'cover.png';
                break;
        }
        $image = '/public/static/images/' . $detfault;
    }
    return get_image_url($image);
}

/**
 * 是否带域名的图片地址
 * @return mixed
 */
function get_image_url($image)
{
    if (false === filter_var($image, FILTER_VALIDATE_URL)) {
        $image = request()->domain() .'/'. ltrim($image, '/');
    }
    return $image;
}

/**
 * 解析标签部分公用
 * @param  object $model
 * @param  array $param
 * @param  array $map
 * @return mixed
 */
function parseTagsList($model, $param, $map)
{
    $cache = isset($param['cache'])? $param['cache'] : 7200;
    $field = isset($param['field'])? $param['field'] : '*';
    $limit = isset($param['limit'])? $param['limit'] : 20;
    $limit = $limit > 200 ? 200 : $limit;
    if (isset($param['id']) && strpos($param['id'], ',')) {
        $map['id'] = ['in', $param['id']];
    }

    $order = 'id desc';
    if (isset($param['order'])) {
        $order = $param['order'];
        if (!strpos($order, " ")) {
            if (strpos($order, ',')) {
                $order = strtr($order, [',' => ' desc,']). ' desc';
            } else{
                $order = $order . ' desc';
            }
        }
    }
    
    if (isset($param['page'])) {
        $pageParam = [];
        $total = '';
        if ((int)$param['page'] > 1) {
            $pageParam['page'] = $param['page'];
        }
        if (isset($param['total']) && intval($param['total'])) {
            $count = $model->where($map)->count();
            if ($count >= $param['total']) {
                $total = $param['total'];
            }
        }
        if (is_array($field)) {
            return  $model = $model->where($map)->field($field[0], $field[1])
                ->order($order)->paginate($limit, $total, $pageParam);
        }
        return $model = $model->where($map)->field($field)->order($order)
            ->paginate($limit, $total, $pageParam);
    }
    $cacheKey = md5($model->getTable() . serialize(array_merge($map, $param)));
    
    if (is_array($field)) {
       return  $model = $model->where($map)->field($field[0], $field[1])->order($order)
           ->limit($limit)->cache($cacheKey, $cache, 'limit_cache')->select();
    }
    return  $model->where($map)->field($field)->order($order)
        ->limit($limit)->cache($cacheKey,$cache, 'limit_cache')->select();
}

/**
 * 获取数据数量
 * @param string $model
 * @param string $field
 * @param string $val
 * @return int
 */
function getDataCount($model, $field = '', $val = '')
{
    $map['status'] = 1;
    if (!empty($field) && !empty($map)) {
        $cacheKey = $model . '_count_' . $field;
        $map[$field] = $val;
        $key = $val;
    } else {
        $cacheKey = 'data_count';
        $key = $model;
    }

    $cache = cache($cacheKey);
    if (empty($cache) || !isset($cache[$key])) {
        $cache[$key] = db($model)->where($map)->count();
        \think\Cache::tag('limit_cache')->set($cacheKey, $cache, 86400);
    }
    return $cache[$key];
}

/**
 * curl get请求
 * @param string $url    请求的地址
 * @param array  $params 请求的参数
 * @return mixed
 */
function curl_get_arr($url, $params)
{
    $url = $url . '?' . http_build_query($params);
    
    $cur = curl_init();
    curl_setopt($cur, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($cur, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($cur, CURLOPT_URL, $url);
    $json = curl_exec($cur);
    curl_close($cur);

    //解决微信传过来的键值
    $result     = json_decode($json, 1);
    $pareResult = [];
    foreach ($result as $k => $v) {
        $k              = trim($k, "'");
        $pareResult[$k] = $v;
    }
    unset($result);
    return $pareResult;
}

function replace_sys_variate($str){
    $content = str_replace(
        ['{$webname}','{$webemail}','{$webqq}','{$webphone}'],
        [config('web_site_name'),config('web_email'),config('web_qq'),config('web_phone')],
        $str
    );
    return $content;
}

/**
 * 记录用户操作防刷新
 * @param  string|array $option 操作的字符串或者数组参数
 * @return bool
 */
function isOffSpite($option)
{
   return Actions::isBan($option);
}

/**
 *  文件缓存，用于持久化缓存数据
 * @param $name
 * @param string $value
 * @return bool
 */
function file_cache($name, $value = '')
{
    return Actions::cache($name, $value);
}

/**
 * 时间差
 * @param $time
 * @return float|int|string
 */
function beforeTime($time){
    $time = is_numeric($time)? $time : strtotime($time);
    $mtime = time() - $time;//获取秒
    
    $time1 = $mtime/60; //转化为分钟
    if( $time1 < 1 ){
        $time1 = '刚刚';
    } elseif ($time1 < 60){
        $time1 = floor($time1).'分钟前';
    } elseif ( $time1 >= 60 && $time1 < (60*24) ){
        $time1 = floor($time1/60).'小时前';
    } elseif ($time1 >= 60*24 && $time1 < (60*24*5)){
        $time1 = floor($time1/60/24).'天前';
    } else {
        $time1 = date('y年m月d日', $time);
    }
    return $time1;
}

//兼容处理
if(!function_exists('read_exif_data')) {
    function read_exif_data($filePath)
    {
        return [];
    }
}

//兼容处理
if(!class_exists('finfo')) {
    if(!defined('FILEINFO_MIME_TYPE')) define('FILEINFO_MIME_TYPE',16);
    function finfo_open ($opt)
    {
        return null;
    }
    
    function finfo_file ($fiinfo = '', $filename)
    {
        if (isset($_FILES['files']['type'])) {
            return $_FILES['files']['type'][0];
        }
        return null;
    }
}
