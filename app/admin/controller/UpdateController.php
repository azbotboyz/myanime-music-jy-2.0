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
        $this->assign('meta_title', '在线更新');
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

		$this->showMsg('JYmusic更新日志：');
		$this->showMsg('更新开始时间:'.date('Y-m-d H:i:s'));
		sleep(1);
    
        $adapter = new Local(ROOT_PATH . 'storage');
        $filesystem = new Filesystem($adapter);
        
        /* 建立更新文件夹 */
        $folder = 'update/' . $date;
        if (!$filesystem->createDir($folder)) {
            $this->showMsg('升级目录创建失败, 请确认storage或者旗下update目录是否有可写权限', 'text-danger');
            exit;
        }
        
		//备份重要文件
		if($backupFile){
			$this->showMsg('开始备份重要程序文件...');
            debug('begin');
            $folder = 'backup/' . $date;
            if (!$filesystem->createDir($folder)) {
                $this->showMsg('备份目录创建失败, 请确认storage或者旗下backup目录是否有可写权限', 'text-danger');
                exit;
            }
			$backupZip = $folder.'/backupAll.zip';
			$zip = new PclZip($backupZip);
			$zip->create('app,core,config');
			$this->showMsg('成功完成重要程序备份,备份文件路径:'.$backupZip.', 耗时:'.debug('begin','end').'s','text-success');
		}

		//下载并保存
		$this->showMsg('开始获取远程更新包...');
		sleep(1);
		//$this->showMsg($updatedUrl);
		$zipPath = $folder.'/update.zip';
		$downZip = $this->getRemoteUrl($updateUrl);
		
		if(empty($downZip)){
			$this->showMsg('下载更新包出错，请重试！', 'text-danger');
			exit;
		}

        $filesystem->write($zipPath, $downZip);
		
		$this->showMsg('获取远程更新包成功,更新包路径：' .$zipPath, 'text-success');
		sleep(1);

		/* 解压缩更新包 */ //TODO: 检查权限
		$this->showMsg('更新包解压缩...');
		sleep(1);
		
		$zip = new PclZip($zipPath);
		$res = $zip->extract(PCLZIP_OPT_PATH, ROOT_PATH);
		
		if($res === 0){
			$this->showMsg('解压缩失败：'.$zip->errorInfo(true).'------更新终止', 'text-danger');
			exit;
		}
		$this->showMsg('更新包解压缩成功', 'text-success');
		sleep(1);

		/* 更新数据库 */
		$updateSql = ROOT_PATH . 'update.sql';
		
		if(is_file($updateSql)){
			$this->showMsg('更新数据库开始...');
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
			$this->showMsg('更新数据库完毕', 'text-success');
		}

		/* 系统版本号更新 */
		$model	= Db::name('config');
			
		$upTime	= $model->where('name','jymusic_update_time')
            ->setField('value',$upTime);
		$version= $model->where(array('name' =>'jymusic_version'))
            ->setField('value',$version);

		if($upTime){
			$this->showMsg('系统版本号更新成功', 'text-success');
		}else{
			$this->showMsg('系统版本更新失败', 'text-danger');
		}
		sleep(1);
		
		//自动清理缓存
        if ($filesystem->deleteDir('runtime')) {
            //清文件缓存
            $this->showMsg('缓存清理完毕！', 'text-success');
        } else {
            $this->showMsg('缓存自动清理失败，请手动删除storage目录下的runtime文件夹！', 'text-danger');
        }
        
		sleep(1);
		$this->showMsg('----------------------------------------------------------------------------');
		$this->showMsg('在线更新全部完成，如有备份，请及时将备份文件移动至非web目录下！', 'success');
		
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
			$this->error('程序无法自动升级,请配置支持curl');
		}
	}

}

