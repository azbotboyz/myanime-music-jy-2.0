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

use think\Db;
use app\services\PclZip;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;

/**
 * 在线更新
 */
class UpdateController extends AdminController
{

	/**
	 * 初始化页面
	 */
	public function index()
    {
        $this->assign('meta_title', 'Online update');
		$data = check_update();
		if($this->request->isPost()){
			echo $this->fetch();
			$this->update($data['update_time'],$data['app_version'],$data['url']);
		}else{
			$this->assign('data', $data);
			return $this->fetch();
		}	
	}


	/**
	 * 在线更新
	 */
	private function update($upTime, $version, $updateUrl)
    {

		$date  			= date('YmdHis');
		$backupFile 	= $this->request->post('backupfile');
		$backupDatabase = $this->request->post('backupdatabase');
		sleep(1);

		$this->showMsg('JYmusic update log:');
		$this->showMsg('Update start time:'.date('Y-m-d H:i:s'));
		sleep(1);
    
        $adapter = new Local(ROOT_PATH . 'storage');
        $filesystem = new Filesystem($adapter);
        
        /* 建立更新文件夹 */
        $folder = 'update/' . $date;
        if (!$filesystem->createDir($folder)) {
            $this->showMsg('Upgrade directory creation failed, Please confirm whether storage or its update directory is writable', 'text-danger');
            exit;
        }
        
		//备份重要文件
		if($backupFile){
			$this->showMsg('Start backing up important program files...');
            debug('begin');
            $folder = 'backup/' . $date;
            if (!$filesystem->createDir($folder)) {
                $this->showMsg('Backup directory creation failed,Please confirm whether storage or its backup directory is writable', 'text-danger');
                exit;
            }
			$backupZip = $folder.'/backupAll.zip';
			$zip = new PclZip($backupZip);
			$zip->create('app,core,config');
			$this->showMsg('Successful completion of important program backup,Backup file path:'.$backupZip.', 耗时:'.debug('begin','end').'s','text-success');
		}

		//下载并保存
		$this->showMsg('Start getting the remote update package...');
		sleep(1);
		//$this->showMsg($updatedUrl);
		$zipPath = $folder.'/update.zip';
		$downZip = $this->getRemoteUrl($updateUrl);
		
		if(empty($downZip)){
			$this->showMsg('Download update package error, please try again!', 'text-danger');
			exit;
		}

        $filesystem->write($zipPath, $downZip);
		
		$this->showMsg('Get the remote update package successfully,Update package path:' .$zipPath, 'text-success');
		sleep(1);

		/* 解压缩更新包 */ //TODO: 检查权限
		$this->showMsg('Update package decompression...');
		sleep(1);
		
		$zip = new PclZip($zipPath);
		$res = $zip->extract(PCLZIP_OPT_PATH, ROOT_PATH);
		
		if($res === 0){
			$this->showMsg('Unzip failed:'.$zip->errorInfo(true).'------Update is terminated', 'text-danger');
			exit;
		}
		$this->showMsg('The update package decompresses successfully', 'text-success');
		sleep(1);

		/* 更新数据库 */
		$updateSql = ROOT_PATH . 'update.sql';
		
		if(is_file($updateSql)){
			$this->showMsg('Update the database to start...');
			if(file_exists($updateSql)){
			
				$sql = $filesystem->read($updateSql);
				$sql = str_replace("\r\n", "\n", $sql);
    
				foreach(explode(";\n", trim($sql)) as $query){
					$prefix = config('database.prefix');
					$query = str_replace('jy_',$prefix,trim($query));
					Db::execute($query);
				}
			}
			unlink($updateSql);
			$this->showMsg('Update the database is complete', 'text-success');
		}

		/* 系统版本号更新 */
		$model	= Db::name('config');
			
		$upTime	= $model->where('name','jymusic_update_time')
            ->setField('value',$upTime);
		$version= $model->where(array('name' =>'jymusic_version'))
            ->setField('value',$version);

		if($upTime){
			$this->showMsg('The system version number was updated successfully', 'text-success');
		}else{
			$this->showMsg('System version update failed', 'text-danger');
		}
		sleep(1);
		
		//自动清理缓存
        if ($filesystem->deleteDir('runtime')) {
            //清文件缓存
            $this->showMsg('Cache clean up!', 'text-success');
        } else {
            $this->showMsg('Cache automatic cleanup failed, please manually delete the storage directory under the runtime folder!', 'text-danger');
        }
        
		sleep(1);
		$this->showMsg('----------------------------------------------------------------------------');
		$this->showMsg('Online update all completed, if backup, please keep the backup file to non-web directory!', 'success');
		
	}
	/**
	 * 获取远程数据
	 */
	private function getRemoteUrl($url = '', $method = '', $param = ''){
		$opts = array(
			CURLOPT_TIMEOUT        => 20,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_URL            => $url,
			CURLOPT_USERAGENT      => $_SERVER['HTTP_USER_AGENT'],
		);
		if($method === 'post'){
			$opts[CURLOPT_POST] = 1;
			$opts[CURLOPT_POSTFIELDS] = $param;
		}

		/* 初始化并执行curl请求 */
		$ch = curl_init();
		curl_setopt_array($ch, $opts);
		$data  = curl_exec($ch);
		$error = curl_error($ch);
		curl_close($ch);
		return $data;
	}

	/**
	 * 实时显示提示信息
     * @param $msg
     * @param string $class
     */
	private function showMsg($msg, $class = ''){
        echo str_repeat(" ",1024);
		echo "<script type=\"text/javascript\">showmsg(\"{$msg}\",\"{$class}\")</script>";
		flush();
		ob_flush();
	}
 

	/**
	 * Ajax检查新版本升级
	 */
    public 	function check(){
		if(extension_loaded('curl')){
			$data = check_update();
			return json($data);
		}else{
			$this->error('The program can not be upgraded automatically,Please support curl');
		}
	}

}

