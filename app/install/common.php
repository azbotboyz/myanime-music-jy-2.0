<?php
/**
 * JYmusic音乐管理系统 PHP version 5.4+ [未经授权严禁私自出售，否者承担法律责任]
 *
 * @version     2.0
 * @author      战神~~巴蒂 [jyuucn@163.com]
 * @license     LICENSE: http://jyuu.cn/license
 * @copyright   2014 - 2017 by JYmusic
 */

/**
 * 系统环境检测
 * @return array 系统环境数据
 */
function check_env()
{
    $items = [
        'os'     => ['操作系统', '不限制', '类Unix', PHP_OS, 'check'],
        'php'    => ['PHP版本', '5.4', '5.4+', PHP_VERSION, 'check'],
        'upload' => ['附件上传', '不限制', '2M+', '未知', 'check'],
        'gd'     => ['GD库', '2.0', '2.0+', '未知', 'check'],
        'disk'   => ['磁盘空间', '20M', '不限制', '未知', 'check'],
    ];

    //PHP环境检测
    if ($items['php'][3] < $items['php'][1]) {
        $items['php'][4] = 'del';
        session('error', true);
    }

    //附件上传检测
    if (@ini_get('file_uploads')) {
        $items['upload'][3] = ini_get('upload_max_filesize');
    }

    //GD库检测
    $tmp = function_exists('gd_info') ? gd_info() : array();
    if (empty($tmp['GD Version'])) {
        $items['gd'][3] = '未安装';
        $items['gd'][4] = 'del';
        session('error', true);
    } else {
        $items['gd'][3] = $tmp['GD Version'];
    }
    unset($tmp);

    //磁盘空间检测
    if (function_exists('disk_free_space')) {
        $items['disk'][3] = floor(disk_free_space(ROOT_PATH) / (1024 * 1024)) . 'M';
    }

    return $items;
}

/**
 * 目录，文件读写检测
 * @return array 检测数据
 */
function check_dirfile()
{
    $items = [
        ['dir', '可写', 'check', './uploads'],
        ['dir', '可写', 'check', './addons'],
        ['dir', '可写', 'check', './app'],
        ['dir', '可写', 'check', './storage'],
        ['dir', '可写', 'check', './config'],
    ];

    foreach ($items as &$val) {
        $item = ROOT_PATH . $val[3];
        if ('dir' == $val[0]) {
            if (!is_writable($item)) {
                if (is_dir($items)) {
                    $val[1] = '可读';
                    $val[2] = 'del';
                    session('error', true);
                } else {
                    $val[1] = '不存在/不可写';
                    $val[2] = 'del';
                    session('error', true);
                }
            }
        } else {
            if (file_exists($item)) {
                if (!is_writable($item)) {
                    $val[1] = '不可写';
                    $val[2] = 'del';
                    session('error', true);
                }
            } else {
                if (!is_writable(dirname($item))) {
                    $val[1] = '不存在';
                    $val[2] = 'del';
                    session('error', true);
                }
            }
        }
    }

    return $items;
}

/**
 * 函数检测
 * @return array 检测数据
 */
function check_func()
{
    $items = [
        ['pdo', '支持', 'check', '类'],
        ['pdo_mysql', '支持', 'check', '模块'],
        //['fileinfo', '支持', 'check', '模块'],
        ['file_get_contents', '支持', 'check', '函数'],
        ['curl_init', '支持', 'check', '函数'],
        ['mb_strlen', '支持', 'check', '函数'],
    ];

    foreach ($items as &$val) {
        if (('类' == $val[3] && !class_exists($val[0]))
            || ('模块' == $val[3] && !extension_loaded($val[0]))
            || ('函数' == $val[3] && !function_exists($val[0]))
        ) {
            $val[1] = '不支持';
            $val[2] = 'del';
            session('error', true);
        }
    }

    return $items;
}

/**
 * 写入配置文件
 * @param  array $config 配置信息
 */
function write_config($config, $auth)
{
    //读取配置内容
    $rootPath = APP_PATH . 'install' . DS . 'data' . DS;

    $dbConfig   = file_get_contents($rootPath . 'database.tpl');
    $viewConfig = file_get_contents($rootPath . 'view.tpl');

    //替换配置项
    foreach ($config as $name => $value) {
        $dbConfig = str_replace("[{$name}]", $value, $dbConfig);
    }

    $dbConfig = str_replace('[auth]', $auth, $dbConfig);

    $configRoot = ROOT_PATH . 'config';
    if (!is_writable($configRoot)) {
        die("config 文件加不可写，请检查！");
    }

    $uploadsRoot = ROOT_PATH . 'uploads';
    if (!is_writable($uploadsRoot)) {
        die("uploads 文件加不可写，请检查！");
    }
    //写入空文件
    file_put_contents($uploadsRoot . DS . 'index.html', '');

    //写入应用配置文件
    $dirs = [
        'attachment', 'avatar', 'down', 'import', 'listen',
        'images' => [
            'qrcode', 'article', 'attach', 'cover', 'icons',
        ],
    ];

    foreach ($dirs as $key => $dirname) {
        if (is_array($dirname)) {
            $path = $uploadsRoot . DS . $key;
            if (!file_exists($path)) {
                mkdir($path);
                if (function_exists('chmod')) {
                    chmod($path, 0777);
                }
                file_put_contents($path . DS . 'index.html', '');
            }

            foreach ($dirname as $val) {
                if (!file_exists($path . DS . $val)) {
                    mkdir($path . DS . $val);
                    if (function_exists('chmod')) {
                        chmod($path . DS . $val, 0777);
                    }
                    file_put_contents($path . DS . $val . DS . 'index.html', '');
                }
            }
        } else {
            $path = $uploadsRoot . DS . $dirname;
            if (!file_exists($path)) {
                mkdir($path);
                if (function_exists('chmod')) {
                    chmod($path, 0777);
                }
                file_put_contents($path . DS . 'index.html', '');
            }
        }
    }

    if (file_put_contents($configRoot . DS . 'database.php', $dbConfig) &&
        file_put_contents($configRoot . DS . 'view.php', $viewConfig)) {
        show_msg('配置文件写入成功');
    } else {
        show_msg('配置文件写入失败！', 'error');
        session('error', true);
    }
}

/**
 * 创建数据表
 * @param  resource $db 数据库连接资源
 * @param  string $prefix
 */
function create_tables($db, $prefix = '')
{
    //读取SQL文件
    $sql = file_get_contents(APP_PATH . 'install' . DS . 'data' . DS . 'install.sql');
    $sql = str_replace("\r", "\n", $sql);
    $sql = explode(";\n", $sql);

    //替换表前缀
    $sql = str_replace(" `jy_", " `{$prefix}", $sql);

    //开始安装
    show_msg('开始安装数据库...');
    foreach ($sql as $value) {
        $value = trim($value);
        if (empty($value)) {
            continue;
        }

        if (substr($value, 0, 12) == 'CREATE TABLE') {
            $name = preg_replace("/^CREATE TABLE IF NOT EXISTS `(\w+)` .*/s", "\\1", $value);
            $msg  = "创建数据表{$name}";
            if (false !== $db->execute($value)) {
                show_msg($msg . '...成功', 'success');
            } else {
                show_msg($msg . '...失败！', 'error');
                session('error', true);
            }
        } else {
            $db->execute($value);
        }
    }
}

/**
 * 更新数据表
 * @param  resource $db 数据库连接资源
 */
function update_tables($db, $prefix = '')
{
    //读取SQL文件
    $sql = file_get_contents(MODULE_PATH . 'Data/update.sql');
    $sql = str_replace("\r", "\n", $sql);
    $sql = explode(";\n", $sql);

    //替换表前缀
    $sql = str_replace(" `jy_", " `{$prefix}", $sql);

    //开始安装
    show_msg('开始升级数据库...');
    foreach ($sql as $value) {
        $value = trim($value);
        if (empty($value)) {
            continue;
        }

        if (substr($value, 0, 12) == 'CREATE TABLE') {
            $name = preg_replace("/^CREATE TABLE `(\w+)` .*/s", "\\1", $value);
            $msg  = "创建数据表{$name}";
            if (false !== $db->execute($value)) {
                show_msg($msg . '...成功', 'success');
            } else {
                show_msg($msg . '...失败！', 'error');
                session('error', true);
            }
        } else {
            if (substr($value, 0, 8) == 'UPDATE `') {
                $name = preg_replace("/^UPDATE `(\w+)` .*/s", "\\1", $value);
                $msg  = "更新数据表{$name}";
            } elseif (substr($value, 0, 11) == 'ALTER TABLE') {
                $name = preg_replace("/^ALTER TABLE `(\w+)` .*/s", "\\1", $value);
                $msg  = "修改数据表{$name}";
            } elseif (substr($value, 0, 11) == 'INSERT INTO') {
                $name = preg_replace("/^INSERT INTO `(\w+)` .*/s", "\\1", $value);
                $msg  = "写入数据表{$name}";
            }
            if (($db->execute($value)) !== false) {
                show_msg($msg . '...成功');
            } else {
                show_msg($msg . '...失败！', 'error');
                session('error', true);
            }
        }
    }
}

/**
 * 及时显示提示信息
 * @param  string $msg 提示信息
 * @param  string $class
 */
function show_msg($msg, $class = '')
{
    $class = !empty($class) ? $class : 'info';
    echo str_repeat(" ", 1024);
    echo "<script type=\"text/javascript\">showmsg(\"{$msg}\", \"{$class}\")</script>";
    @flush();
    @ob_flush();
}

/**
 * 生成系统AUTH_KEY
 */
function build_auth_key()
{
    $chars = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $chars .= '`~!@#$%^&*()_+-=[]{};:"|,.<>/?';
    $chars = str_shuffle($chars);
    return substr($chars, 0, 40);
}

/**
 * 系统非常规MD5加密方法
 * @param  string $str 要加密的字符串
 * @param  string $key 要加密混淆字符串
 * @return string
 */
function user_md5($str, $key = '')
{
    return '' === $str ? '' : md5(sha1($str) . $key);
}
