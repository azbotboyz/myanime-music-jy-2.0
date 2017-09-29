<?php
/**
 * JYmusic音乐管理系统
 *
 * @version     2.0
 * @author      战神~~巴蒂 <jyuucn@163.com>
 * @license     http://jyuu.cn [未经授权严禁私自出售，否者承担法律责任]
 * @copyright   2014 - 2017 JYmusic
 */
namespace app\user\controller;

use app\services\Actions;
use app\common\model\Songs;
use app\common\model\Member;
use app\common\model\SongsExtend;
use Spatie\UrlSigner\MD5UrlSigner;
use JYmusic\Downloader\Downloader;
use app\common\controller\BaseController;

//下载控制器
class DownController extends BaseController
{
    protected $user;
    
    public function index()
    {
        abort(404, '页面不存在！');
    }
    
    public function read($id = 0)
    {
        $urlSigner = new MD5UrlSigner(UC_AUTH_KEY);
        $url = $this->request->domain() . $this->request->url();
        if (!intval($id) || !$urlSigner->validate($url)) {
            abort(404, '文件不存在');
        }
        //获取文件
        $model = new Songs();
        $song = $model->where('id', $id)
            ->field('id,name,artist_name')
            ->with(['extend' => function($query){
                $query->field('mid,listen_url,down_url');
            }])
            ->find();
        
        if (!$song) {
            abort(404, '文件不存在');
        }
        $extend = $song->extend->getData();
        
        $filePath = !empty($extend['down_url'])? $extend['down_url'] : $extend['listen_url'];
        $filePath = ROOT_PATH . ltrim($filePath, '/');
        
        $newName = !empty($song->artist_name)? $song->artist_name . '-' . $song->name : $song->name;
        
        $downloader = new Downloader($filePath, Downloader::DOWNLOAD_FILE );
        $downloader->setDownloadName($newName)
            ->resumable( false )
            ->speedLimit(20*1024)
            //->recordDownloaded(function () { })
            ->autoExit(true)
            ->download();
        
    }
    
    /**
     * 检测用户下载权限
     * @param int $id
     * @return \think\response
     */
    public function check($id = 0)
    {
        if (Actions::isBan('down')) {
            return $this->retErr(40405);
        }
        if (!intval($id)) {
            return $this->retErr(40004);
        }
        $model = new SongsExtend();
        $song = $model->where('mid', $id)
            ->field('mid,down_rule')
            ->find();
        if (!$song) {
            return $this->retErr(40404, '歌曲不存在');
        }
        $song = $song->toArray();
        
        $res = ['msg' => 'ok', 'is_confirm' => 0];
        //获取歌曲下载规则
        if (!empty($song['down_rule'])) {
            $coin = intval($song['down_rule']['coin']);
            $score = intval($song['down_rule']['score']);
            $needGroup = $song['down_rule']['group'];
            //检测是否需要登录
            if (!$score && !$coin && in_array(0, $needGroup)) {
                return $this->retSucc();
            }
    
            if ($score > 0) {
                $res['is_confirm'] = 1;
                $res['msg'] = '下载本首音乐需要'. $score . '积分，24小时内不会重复扣除<br>是否确认下载？';
            }
    
            if ($coin > 0) {
                $res['is_confirm'] = 1;
                $res['msg'] = '下载本首音乐需要'. $coin. '金币，24小时内不会重复扣除<br>是否确认下载？';
            }
        }
        
        if (!is_login()) {
            return $this->retErr(40401);
        }
        
        return $this->retSucc($res);
    }
    
    /**
     * 创建下载
     * @param int $id
     * @return \think\Response
     */
    public function get($id = 0)
    {
        if (!intval($id)) {
            return $this->retErr(40004);
        }
    
        $model = new Songs;
        $original = $model->where('id', $id)
            ->field('id,name')
            ->with(['extend' =>  function($query){
                $query->field('mid,down_rule,server_id,listen_url,down_url,disk_url,disk_pass');
            }])
            ->find();
        
        if (!$original) {
            return $this->retErr(40404, '歌曲不存在');
        }

        $song = $original->toArray();
        
        //有规则
        if (!empty($song['extend']['down_rule'])) {
            //获取歌曲下载规则
            $coin = intval($song['extend']['down_rule']['coin']);
            $score = intval($song['extend']['down_rule']['score']);
            $needGroup = $song['extend']['down_rule']['group'];
    
            //检测是否需要登录
            if (!$score && !$coin && in_array(0, $needGroup)) {
                return $this->retSucc(['result' => $this->getDownUrl($original)]);
            }
    
            if (!$uid = is_login()) {
                return $this->retErr(40401);
            }
            
            //判断重下载
            if ($this->isRepeat($id, $uid)) {
                return $this->retSucc(['result' => $this->getDownUrl($original)]);
            }
    
            $member = new Member();
            $user = $member->where('uid', $uid)
                ->field('uid,nickname,coin,score')
                ->find();
            $userGroup = $member->getGroup($uid);
            
            if (!in_array($userGroup['id'], $needGroup)) {
                $msg = '抱歉你当前是【' . $userGroup['name'] . '】你所在的用户组无权下载';
                return $this->retErr(40403, $msg);
            }
            
            $res = true;
            if ($coin > 0) {
                if ($user->coin < $coin) {
                    $msg = '抱歉当前音乐需要支付金币【'. $coin .'】你余额为【' . $user->coin . '】无法下载';
                    return $this->retErr(40403, $msg);
                } else {
                   $res = $this->deduct($id, $coin, $user);
                }
            }
            
            if ($score > 0) {
                if ($user->score < $score) {
                    $msg = '抱歉当前音乐需要支付积分【' . $score . '】你余额为【' . $user->score . '】无法下载';
                    return $this->retErr(40403, $msg);
                } else {
                    $res = $this->deduct($id, $score, $user, 'score');
                }
            }
            
            if ($res) {
                return $this->retSucc(['result' => $this->getDownUrl($original)]);
            } else {
                return $this->retErr(40500, '文件获取失败，请稍后重试！');
            }
        } else {
            if (!$uid = is_login()) {
                return $this->retErr(40401);
            }
            return $this->retSucc(['result' => $this->getDownUrl($original)]);
        }
    }
    
    /**
     * 判断重复下载
     * @param int $sid
     * @param int $uid
     * @return mixed
     */
    protected function isRepeat($sid = 0, $uid = 0)
    {
        $startTime = time() - 86400;
        $map['uid'] = $uid;
        $map['songs_id'] = $sid;
        $map['create_time'] = ['>', $startTime];
        return db('user_down')->where($map)->find();
    }
    
    /**
     * 扣除费用
     * @param int $sid
     * @param int $free
     * @param object $user
     * @param string $type
     * @return boolean
     */
    protected function deduct($sid, $free, $user, $type = 'coin')
    {
        if ('coin' == $type) {
            $res = $user->setDec('coin', $free);
        } else {
            $res = $user->setDec('score', $free);
        }
       
        if (!$res) {
            return false;
        }
        
        $data['uid'] = $user->uid;
        $data['songs_id'] = $sid;
        $data['create_time'] = time();
        $data['user_ip'] = get_client_ip(1);
        db('user_down')->insert($data);
        //增加下载次数
        return true;
    }
    
    /**
     * 获取下载地址
     * @param array $original
     * @return array
     */
    protected function getDownUrl($original)
    {
        $data = $original->extend;
        $res = ['name' => $original->name, 'is_disk' => 0, 'is_local' => 0];

        //判断是否有网盘
        if (!empty($data['disk_url'])) {
            $res['is_disk'] = 1;
            $res['disk_url'] = $data['disk_url'];
            $res['disk_pass'] = $data['disk_pass'];
            return $res;
        }

        $res['down_url'] = !empty($data['down_url'])? $data['down_url'] : $data['listen_url'];

        //判断外链
        if (0 === strpos($res['down_url'], 'http')) {
            return $res;
        }

        //本地
        if ($data['server_id'] == 0) {
            $res['is_local']  = 1;
            $res['down_url'] = $this->getSignUrl($data['mid']);
            return $res;
        }

        //有服务器
        $data = $data->toArray();
        $res['down_url'] = !empty($down['down_url'])? $down['down_url'] : $down['listen_url'];
        return $res;
    }
    
    /**
     * 获取签名下载地址
     * @param $id
     * @return string
     */
    protected function getSignUrl($id)
    {
        $urlSigner = new MD5UrlSigner(UC_AUTH_KEY);
        $url = url('user/Down/read', ['id' => $id], '', true);
        $expirationDate = (new \DateTime)->modify('+30 minutes');
        return $urlSigner->sign($url, $expirationDate);
    }
}
