<?php
/**
 * JYmusic音乐管理系统
 *
 * @version     2.0
 * @author      战神~~巴蒂 [jyuucn@163.com]
 * @license     http://jyuu.cn [未经授权严禁私自出售，否者承担法律责任]
 * @copyright   2014 - 2017 JYmusic
 */

namespace app\admin\controller;

use Think\Db;
use app\services\Database;

/**
 * 数据库备份还原控制器
 */
class DatabaseController extends AdminController
{

    /**
     * 数据库备份/还原列表
     * @param  String $type import-还原，export-备份
     */
    public function index($type = null)
    {
        switch ($type) {
            /* 数据还原 */
            case 'import':
                //列出备份文件列表
                $backupDir = config('data_backup_path');
                $backupDir = str_replace(['./', '/'], DS,  $backupDir);
                
                $path = ROOT_PATH . trim($backupDir, DS) . DS;
                if (!file_exists($path)) {
                    $this->error('Backup directory' . config('data_backup_path'). 'does not exist');
                }
                
                $flag = \FilesystemIterator::KEY_AS_FILENAME;
                $glob = new \FilesystemIterator($path, $flag);

                $list = array();
                foreach ($glob as $name => $file) {
                    if (preg_match('/^\d{8,8}-\d{6,6}-\d+\.sql(?:\.gz)?$/', $name)) {
                        $name = sscanf($name, '%4s%2s%2s-%2s%2s%2s-%d');

                        $date = "{$name[0]}-{$name[1]}-{$name[2]}";
                        $time = "{$name[3]}:{$name[4]}:{$name[5]}";
                        $part = $name[6];

                        if (isset($list["{$date} {$time}"])) {
                            $info         = $list["{$date} {$time}"];
                            $info['part'] = max($info['part'], $part);
                            $info['size'] = $info['size'] + $file->getSize();
                        } else {
                            $info['part'] = $part;
                            $info['size'] = $file->getSize();
                        }
                        $extension        = strtoupper(pathinfo($file->getFilename(), PATHINFO_EXTENSION));
                        $info['compress'] = ($extension === 'SQL') ? '-' : $extension;
                        $info['time']     = strtotime("{$date} {$time}");

                        $list["{$date} {$time}"] = $info;
                    }
                }
                $title = 'Data reduction';
                break;

            /* 数据备份 */
            case 'export':
                $list  = Db::query('SHOW TABLE STATUS');
                $list  = array_map('array_change_key_case', $list);
                $title = 'data backup';
                break;

            default:
                $this->error('Parameter error!');
        }

        //渲染模板
        $this->assign('meta_title', $title);
        $this->assign('list', $list);
        return $this->fetch($type);
    }

    /**
     * 优化表
     * @param  String $tables 表名
     */
    public function optimize($tables = null)
    {
        if ($tables) {
            if (is_array($tables)) {
                $tables = implode('`,`', $tables);
                $list   = Db::query("OPTIMIZE TABLE `{$tables}`");

                if ($list) {
                    $this->success("Data sheet optimization is done!");
                } else {
                    $this->error("Data table optimization error Please try again!");
                }
            } else {
                $list = Db::query("OPTIMIZE TABLE `{$tables}`");
                if ($list) {
                    $this->success("data sheet'{$tables}'Optimized!");
                } else {
                    $this->error("data sheet'{$tables}'Optimization error Please try again!");
                }
            }
        } else {
            $this->error("Please specify the table to be optimized!");
        }
    }

    /**
     * 修复表
     * @param  String $tables 表名
     */
    public function repair($tables = null)
    {
        if ($tables) {
            if (is_array($tables)) {
                $tables = implode('`,`', $tables);
                $list   = Db::query("REPAIR TABLE `{$tables}`");

                if ($list) {
                    $this->success("数据表修复完成！");
                } else {
                    $this->error("数据表修复出错请重试！");
                }
            } else {
                $list = Db::query("REPAIR TABLE `{$tables}`");
                if ($list) {
                    $this->success("数据表'{$tables}'修复完成！");
                } else {
                    $this->error("数据表'{$tables}'修复出错请重试！");
                }
            }
        } else {
            $this->error("请指定要修复的表！");
        }
    }

    //执行sql
    public function execute()
    {
        if ($this->request->isPost()) {
            $sql = $this->request->post('sql');
            $Db  = Db::getInstance();
            if (!empty($sql)) {
                $list = $Db->query($sql);
                if ($list) {
                    $this->success("sql执行完成！");
                } else {
                    $this->error("sql执行出错请重试！");
                }
            } else {
                $this->error("输入你要执行的sql");
            }
        } else {
            return  $this->ftech();
        }
    }

    /**
     * 删除备份文件
     * @param  Integer $time 备份时间
     */
    public function del($time = 0)
    {
        if ($time) {
            $name = date('Ymd-His', $time) . '-*.sql*';
            $path = realpath(config('data_backup_path')) . DS . $name;
            array_map("unlink", glob($path));
            if (count(glob($path))) {
                $this->error('Backup file deleted failed, please check permissions!');
            } else {
                $this->success('Backup file deleted successfully!');
            }
        } else {
            $this->error('Parameter error!');
        }
    }

    /**
     * 备份数据库
     * @param  String  $tables 表名
     * @param  Integer $id     表ID
     * @param  Integer $start  起始行数
     */
    public function export($tables = null, $id = null, $start = null)
    {
        if ($this->request->isPost() && !empty($tables) && is_array($tables)) {
            //初始化
            //读取备份配置
            $backupDir = config('data_backup_path');
            $backupDir = str_replace(['./', '/'], DS,  $backupDir);
    
            $config = [
                'path'     => ROOT_PATH . trim($backupDir, DS) . DS,
                'part'     => config('data_backup_part_size'),
                'compress' => config('data_backup_compress'),
                'level'    => config('data_backup_compress_level'),
            ];

            //检查是否有正在执行的任务
            $lock = "{$config['path']}backup.lock";
            if (is_file($lock)) {
                $this->error('Detected that a backup task is being executed, please try again later!');
            } else {
                
                if (!file_exists($config['path'])) {
                    mkdir($config['path']);
                    if (function_exists('chmod')) {
                        chmod($config['path'], 0777);
                    }
                }
                //创建锁文件
                file_put_contents($lock, time());
            }

            //检查备份目录是否可写
            is_writeable($config['path']) || $this->error('Backup directory does not exist or can not be written, please check and try again!');
            session('backup_config', $config);

            //生成备份文件信息
            $file = array(
                'name' => date('Ymd-His', time()),
                'part' => 1,
            );
            session('backup_file', $file);
            //缓存要备份的表
            session('backup_tables', $tables);

            //创建备份文件
            $Database = new Database($file, $config);
            if (false !== $Database->create()) {
                $tab = array('id' => 0, 'start' => 0);
                $this->success('Initialize success!', '', array('tables' => $tables, 'tab' => $tab));
            } else {
                $this->error('Initialization failed, backup file creation failed!');
            }
        } elseif ($this->request->isGet() && is_numeric($id) && is_numeric($start)) {
            //备份数据
            $tables = session('backup_tables');
            //备份指定表
            $Database = new Database(session('backup_file'), session('backup_config'));
            $start    = $Database->backup($tables[$id], $start);
            if (false === $start) {
                //出错
                $this->error('备份出错！');
            } elseif (0 === $start) {
                //下一表
                if (isset($tables[++$id])) {
                    $tab = array('id' => $id, 'start' => 0);
                    $this->success('备份完成！', '', array('tab' => $tab));
                } else {
                    //备份完成，清空缓存
                    unlink(session('backup_config.path') . 'backup.lock');
                    session('backup_tables', null);
                    session('backup_file', null);
                    session('backup_config', null);
                    $this->success('Backup complete!');
                }
            } else {
                $tab  = array('id' => $id, 'start' => $start[0]);
                $rate = floor(100 * ($start[0] / $start[1]));
                $this->success("Is backing up...({$rate}%)", '', array('tab' => $tab));
            }
        } else {
            //出错
            $this->error('Parameter error!');
        }
    }

    /**
     * 还原数据库
     */
    public function import($time = 0, $part = null, $start = null)
    {
        if (is_numeric($time) && is_null($part) && is_null($start)) {
            //初始化
            //获取备份文件信息
            $name  = date('Ymd-His', $time) . '-*.sql*';
            $path  = realpath(config('data_backup_path')) . DIRECTORY_SEPARATOR . $name;
            $files = glob($path);
            $list  = array();
            foreach ($files as $name) {
                $basename        = basename($name);
                $match           = sscanf($basename, '%4s%2s%2s-%2s%2s%2s-%d');
                $gz              = preg_match('/^\d{8,8}-\d{6,6}-\d+\.sql.gz$/', $basename);
                $list[$match[6]] = array($match[6], $name, $gz);
            }
            ksort($list);

            //检测文件正确性
            $last = end($list);
            if (count($list) === $last[0]) {
                session('backup_list', $list); //缓存备份列表
                $this->success('Initialize done!', '', array('part' => 1, 'start' => 0));
            } else {
                $this->error('The backup file may have been damaged, please check!');
            }
        } elseif (is_numeric($part) && is_numeric($start)) {
            $list = session('backup_list');
            $db   = new Database($list[$part], array(
                'path'     => realpath(config('data_backup_path')) . DIRECTORY_SEPARATOR,
                'compress' => $list[$part][2]));

            $start = $db->import($start);

            if (false === $start) {
                $this->error('Restore data error!');
            } elseif (0 === $start) {
                //下一卷
                if (isset($list[++$part])) {
                    $data = array('part' => $part, 'start' => 0);
                    $this->success("Is being restored...#{$part}", '', $data);
                } else {
                    session('backup_list', null);
                    $this->success('Restore finished!');
                }
            } else {
                $data = array('part' => $part, 'start' => $start[0]);
                if ($start[1]) {
                    $rate = floor(100 * ($start[0] / $start[1]));
                    $this->success("Is being restored...#{$part} ({$rate}%)", '', $data);
                } else {
                    $data['gz'] = 1;
                    $this->success("Is being restored...#{$part}", '', $data);
                }
            }
        } else {
            $this->error('Parameter error!');
        }
    }
}
