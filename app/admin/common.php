<?php
/**
 * JYmusic音乐管理系统
 *
 * @version     2.0
 * @author      战神~~巴蒂 <jyuucn@163.com>
 * @license     http://jyuu.cn [未经授权严禁私自出售，否者承担法律责任]
 * @copyright   2014 - 2017 JYmusic
 */
/*
 * 后台通用函数
 */

/**
 * 分析枚举类型配置值 格式 a:名称1,b:名称2
 * @param string
 * @return array
 */
function parse_config_attr($string)
{
    $array = preg_split('/[,;\r\n]+/', trim($string, ",;\r\n"));
    if (strpos($string, ':')) {
        $value = array();
        foreach ($array as $val) {
            list($k, $v) = explode(':', $val);
            $value[$k]   = $v;
        }
    } else {
        $value = $array;
    }
    return $value;
}

/**
 * 分析枚举类型字段值 格式 a:名称1,b:名称2
 * 暂时和 parse_config_attr功能相同 但请不要互相使用，后期会调整
 * @param string
 * @return array
 */
function parse_field_attr($string)
{
    if (0 === strpos($string, ':')) {
        // 采用函数定义
        return eval('return ' . substr($string, 1) . ';');
    } elseif (0 === strpos($string, '[')) {
        // 支持读取配置参数（必须是数组类型）
        return config(substr($string, 1, -1));
    }

    $array = preg_split('/[,;\r\n]+/', trim($string, ",;\r\n"));
    if (strpos($string, ':')) {
        $value = array();
        foreach ($array as $val) {
            list($k, $v) = explode(':', $val);
            $value[$k]   = $v;
        }
    } else {
        $value = $array;
    }
    return $value;
}

/**
 * 获取配置的类型
 * @param int $type 配置类型
 * @return string
 */
function get_config_type($type = 0)
{
    $list = config('config_type_list');
    return $list[$type];
}

/**
 * 获取配置的分组
 * @param int $group 配置分组
 * @return string
 */
function get_config_group($group = 0)
{
    $list = config('config_group_list');
    return isset($list[$group]) ? $list[$group] : '';
}

/* 解析插件数据列表定义规则*/
function get_addonlist_field($data, $grid, $addon)
{
    // 获取当前字段数据
    foreach($grid['field'] as $field){
        $array  =   explode('|',$field);
        $temp  =    $data[$array[0]];
        // 函数支持
        if(isset($array[1])){
            $temp = call_user_func($array[1], $temp);
        }
        $data2[$array[0]]   = $temp;
    }

    if(!empty($grid['format'])){
        $value  =   preg_replace_callback('/\[([a-z_]+)\]/', function($match) use($data2){return $data2[$match[1]];}, $grid['format']);
    }else{
        $value = implode(' ',$data2);
    }

    // 链接支持
    if(!empty($grid['href'])){
        $links  =   explode(',',$grid['href']);
        foreach($links as $link){
            $array  =   explode('|',$link);
            $href   =   $array[0];
            if(preg_match('/^\[([a-z_]+)\]$/',$href,$matches)){
                $val[]  =   $data2[$matches[1]];
            }else{
                $show   =   isset($array[1])?$array[1]:$value;
                // 替换系统特殊字符串
            
                if ('[EDIT]' === $href) {
                    $href =  'operatedata?name=[ADDON]&id=[id]';
                    $show = '<i class="fa fa-pencil text-info"></i>';
                } elseif ('[DELETE]' === $href) {
                    $href =  'erasedata?name=[ADDON]&id=[id]';
                    $show = '<i class="fa fa-times text-danger"></i>';
                } 
                
                $href  =  str_replace('[ADDON]',$addon,$href);
                // 替换数据变量
                $href   =  preg_replace_callback('/\[([a-z_]+)\]/', function($match) use($data){return $data[$match[1]];}, $href);
 
                $val[]  = '<a class="table-action-btn" href="'.url($href).'">'.$show.'</a>';
            }
        }

        $value  = implode(' ',$val);
    }
    return $value;
}

/**
 * 动态扩展左侧菜单,base.html里用到
 */
function extra_menu($extra_menu, &$base_menu)
{
    foreach ($extra_menu as $key => $group) {
        if (isset($base_menu['child'][$key])) {
            $base_menu['child'][$key] = array_merge($base_menu['child'][$key], $group);
        } else {
            $base_menu['child'][$key] = $group;
        }
    }
}

function arr_to_newline($arr)
{
    $line = '';
    foreach ($arr as $key => $val) {
        $line .= $key .":". $val ."\r\n";
    }
    return $line;
}

function check_update()
{
    $isCheck = cache('is_check_update');
    
    if ($isCheck) {
        return ['status' => 0];
    }
    
    $url = 'http://update.jyuu.cn/index.php?m=home&c=CheckVersion';
    $model	= db('Config');
    $upTime		= $model->where(array('name' =>'jymusic_update_time'))->value('value');
    $version	= $model->where(array('name' =>'jymusic_veision'))->value('value');
    $params = array(
        'version'		=> $version,
        'updateTime' 	=> $upTime,
        'ip'			=> $_SERVER['SERVER_ADDR'],
        'domain'  		=> $_SERVER['HTTP_HOST'],
        'auth'    		=> UC_AUTH_KEY,
        'userip'		=> get_client_ip(1)
    );
    $vars = http_build_query($params);
    
    try {
        $opts = array(
            CURLOPT_TIMEOUT => 30,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $vars,
            CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
        );
    
        /* 初始化并执行curl请求 */
        $ch = curl_init();
        curl_setopt_array($ch, $opts);
        $data = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        $data = json_decode($data, 1);
        cache('is_check_update', 111111, 86400);
        return $data;
    } catch (\Exception $e) {
        cache('is_check_update', 111111, 86400);
        return ['status' => 0];
    }
}
