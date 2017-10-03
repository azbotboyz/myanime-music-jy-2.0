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
        'os'     => ['operating system', 'not limited', 'Unix-like', PHP_OS, 'check'],
        'php'    => ['PHP version', '5.4', '5.4+', PHP_VERSION, 'check'],
        'upload' => ['Attachment upload', 'not limited', '2M+', 'unknown', 'check'],
        'gd'     => ['GD library', '2.0', '2.0+', 'unknown', 'check'],
        'disk'   => ['disk space', '20M', 'not limited', 'unknown', 'check'],
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
        $items['gd'][3] = 'Not Installed';
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
        ['dir', 'Can be written', 'check', './uploads'],
        ['dir', 'Can be written', 'check', './addons'],
        ['dir', 'Can be written', 'check', './app'],
        ['dir', 'Can be written', 'check', './storage'],
        ['dir', 'Can be written', 'check', './config'],
    ];

    foreach ($items as &$val) {
        $item = ROOT_PATH . $val[3];
        if ('dir' == $val[0]) {
            if (!is_writable($item)) {
                if (is_dir($items)) {
                    $val[1] = 'Readable';
                    $val[2] = 'del';
                    session('error', true);
                } else {
                    $val[1] = 'does not exist/Can not be written';
                    $val[2] = 'del';
                    session('error', true);
                }
            }
        } else {
            if (file_exists($item)) {
                if (!is_writable($item)) {
                    $val[1] = 'Can not be written';
                    $val[2] = 'del';
                    session('error', true);
                }
            } else {
                if (!is_writable(dirname($item))) {
                    $val[1] = 'does not exist';
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
        ['pdo', 'stand by', 'check', 'class'],
        ['pdo_mysql', 'stand by', 'check', 'Module'],
        //['fileinfo', '支持', 'check', '模块'],
        ['file_get_contents', 'stand by', 'check', 'function'],
        ['curl_init', 'stand by', 'check', 'function'],
        ['mb_strlen', 'stand by', 'check', 'function'],
    ];

    foreach ($items as &$val) {
        if (('class' == $val[3] && !class_exists($val[0]))
            || ('Module' == $val[3] && !extension_loaded($val[0]))
            || ('function' == $val[3] && !function_exists($val[0]))
        ) {
            $val[1] = 'not support';
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
        die("config File can not be written, please check!");
    }

    $uploadsRoot = ROOT_PATH . 'uploads';
    if (!is_writable($uploadsRoot)) {
        die("uploads File can not be written, please check!");
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
        show_msg('The configuration file was written successfully');
    } else {
        show_msg('Configuration file failed!', 'error');
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
    show_msg('Start installing the database...');
    foreach ($sql as $value) {
        $value = trim($value);
        if (empty($value)) {
            continue;
        }

        if (substr($value, 0, 12) == 'CREATE TABLE') {
            $name = preg_replace("/^CREATE TABLE IF NOT EXISTS `(\w+)` .*/s", "\\1", $value);
            $msg  = "Create a data table{$name}";
            if (false !== $db->execute($value)) {
                show_msg($msg . '...success', 'success');
            } else {
                show_msg($msg . '...failure!', 'error');
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
    show_msg('Start upgrading the database...');
    foreach ($sql as $value) {
        $value = trim($value);
        if (empty($value)) {
            continue;
        }

        if (substr($value, 0, 12) == 'CREATE TABLE') {
            $name = preg_replace("/^CREATE TABLE `(\w+)` .*/s", "\\1", $value);
            $msg  = "Create a data table{$name}";
            if (false !== $db->execute($value)) {
                show_msg($msg . '...success', 'success');
            } else {
                show_msg($msg . '...failure!', 'error');
                session('error', true);
            }
        } else {
            if (substr($value, 0, 8) == 'UPDATE `') {
                $name = preg_replace("/^UPDATE `(\w+)` .*/s", "\\1", $value);
                $msg  = "Update the data table{$name}";
            } elseif (substr($value, 0, 11) == 'ALTER TABLE') {
                $name = preg_replace("/^ALTER TABLE `(\w+)` .*/s", "\\1", $value);
                $msg  = "Modify the data table{$name}";
            } elseif (substr($value, 0, 11) == 'INSERT INTO') {
                $name = preg_replace("/^INSERT INTO `(\w+)` .*/s", "\\1", $value);
                $msg  = "Write the data table{$name}";
            }
            if (($db->execute($value)) !== false) {
                show_msg($msg . '...success');
            } else {
                show_msg($msg . '...failure!', 'error');
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
