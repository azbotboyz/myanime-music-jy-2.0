<?php
/**
 * JYmusic音乐管理系统
 *
 * @version     2.0
 * @author      战神~~巴蒂 <jyuucn@163.com>
 * @license     http://jyuu.cn [未经授权严禁私自出售，否者承担法律责任]
 * @copyright   2014 - 2017 JYmusic
 */

namespace app\api\controller\v1;

use think\Db;
use think\Request;
use app\services\Actions;

class ActionsController extends ApiController
{

    protected $uid = false;

    protected $beforeActionList = [
        'check' => ['except' => 'listen'],
    ];

    /**
     * 显示资源列表
     * @return void
     */
    protected function check()
    {
        $this->uid = is_login();
    }
    
    /**
     * 显示指定的资源
     * @param  int  $id
     * @return \think\Response
     */
    public function listen($id = 0)
    {
        if (Actions::isBan('listens')) {
            return $this->retErr(40405);
        }
        
        if (!$id = intval($id)) {
            return $this->retErr(40004, 'ID参数错误');
        }
        //TODO 一系列试听
        //记录天是试听数量
        
        //记录本周
        
        //记录本月
        $res = $this->updateData('songs', 'listens', $id);
        if (false === $res) {
            return $this->retErr(40500);
        }
        return $this->retSucc($res);
    }
    
    /**
     * 公共点击事件
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function hits(Request $request)
    {
        if (Actions::isBan('hits')) {
            return $this->retErr(40405);
        }
    
        $id = $request->param('id');
        if (!$id = intval($id)) {
            return $this->retErr(40004, 'ID参数错误');
        }
    
        $type = $request->param('type');
        $actions = ['album', 'artist', 'tags', 'genre', 'ranks', 'member', 'user', 'article', 'news'];
    
        if (!in_array($type, $actions)) {
            return $this->retErr(40004, '类型参数错误');
        }
        
        if ('user' == $type) {
            $type = 'member';
        }
        if ('news' == $type) {
            $type = 'article';
        }
        $res = $this->updateData($type, 'hits', $id);
        return $this->retSucc($res);
    }
    
    /**
     * 用户点赞
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function digg(Request $request)
    {
        if (!Actions::isDayAllow('digg', session_id())) {
            return $this->retErr(40406, '今天已经点过赞了');
        }
        
        $id = $request->param('id');
        if (!$id = intval($id)) {
            return $this->retErr(40004, 'ID参数错误');
        }
        
        $type    = $request->param('type', 'songs');
        $actions = ['songs', 'album', 'artist'];
        if (!in_array($type, $actions)) {
            return $this->retErr(40004, '类型参数错误');
        }
        
        $name  = strtolower($type);
        $model = Db::name($name);
        
        $title = $model->where('id', $id)->value('name');
        if (empty($title)) {
            return $this->retErr(40404);
        }
    
        $res = Actions::update($name, 'digg', $id);
        if (false === $res) {
            return $this->retErr(40500);
        }
        $res['msg'] = '成功点赞！';
        return $this->retSucc($res);
    }
    
    
    /**
     * 用户收藏
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function fav(Request $request)
    {
        if (Actions::isBan('fav')) {
            return $this->retErr(40405);
        }
        
        if (!$this->uid) {
            return $this->retErr(40401);
        }
        
        $id = $request->param('id');
        if (!$id = intval($id)) {
            return $this->retErr(40004, 'ID参数错误');
        }
        
        $type    = $request->param('type', 'songs');
        $actions = ['songs', 'album', 'artist'];
        if (!in_array($type, $actions)) {
            return $this->retErr(40004, '类型参数错误');
        }
        
        $name  = strtolower($type);
        $table = Db::name($name . '_fav');
        $model = Db::name($name);
        
        //判断收藏上限
        $max = config('fav_max_limit');
        
        if ($table->where('uid', $this->uid)->count() >= $max) {
            return $this->retErr(40011);
        }
        
        $title = $model->where('id', $id)->value('name');
        if (empty($title)) {
            return $this->retErr(40404);
        }
        
        $data = [
            'uid' => $this->uid,
            $name . '_id' => $id,
        ];
        
        //判断是否收藏
        $dataId = $table->where($data)->value('id');
        $isRemove = false;
        if (!$dataId) {
            //添加收藏
            $data['create_time'] = time();
            if (!$table->insert($data)) {
                return $this->retErr(40500, $title . '收藏失败！');
            }
            $res = $this->updateData($name, 'favtimes', $id);
            $msg = $title . '收藏成功';
        } else {
            //移除收藏
            if (!$table->where($data)->delete()) {
                return $this->retErr(40500, $title . '移除收藏失败！');
            }
            $res = $this->updateData($name, 'favtimes', $id, false);
            $msg = $title . '收藏成功移除';
            $isRemove = true;
        }
        
        return $this->retSucc(['code' => 0,'msg' => $msg,'remove' => $isRemove,'result' => $res,]);
    }

    /**
     * 检查是否已经收藏
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function isfav(Request $request)
    {
        if (Actions::isBan('is_fav')) {
            return $this->retErr(40405);
        }

        if (!$this->uid) {
            return $this->retErr(40401);
        }

        $id = $request->param('id');
        if (!$id = intval($id)) {
            return $this->retErr(40004, 'ID参数错误');
        }

        $type    = $request->param('type', 'songs');
        $actions = ['songs', 'album', 'artist'];
        if (!in_array($type, $actions)) {
            return $this->retErr(40004, '类型参数错误');
        }

        $name  = strtolower($type);
        $table = Db::name($name . '_fav');

        $map = ['uid' => $this->uid, $name . '_id' => $id];
        //判断是否收藏
        $dataId = $table->where($map)->value('id');

        $isFav = $dataId? true : false;
        return $this->retSucc([
            'code'   => 0,
            'msg'    => 'ok',
            'is_fav' => $isFav,
        ], 200);
    }

    /**
     * 用户关注
     * @param  \think\Request $request
     * @return \think\Response
     */
    public function follow(Request $request)
    {
        if (Actions::isBan('follow')) {
            return $this->retErr(40405);
        }

        if (!$this->uid) {
            return $this->retErr(40401);
        }

        $followUid = $request->param('follow_uid');
        if (!$uid = intval($followUid)) {
            return $this->retErr(40004, 'UID参数错误');
        }

        if ($this->uid == $followUid) {
            return $this->retErr(40016);
        }

        $table = Db::name('user_follow');
        $model = Db::name('member');

        //判断收藏上限
        $max = config('follow_limit');
        //修复旧版本一个错误
        $max = $max? $max : config('follow_mix_limit');
 
        if ($table->where('uid', $this->uid)->count() >= $max) {
            return $this->retErr(40012);
        }

        $title = $model->where('uid', $followUid)->value('nickname');
        if (empty($title)) {
            return $this->retErr(40404);
        }

        $data = [
            'uid' => $this->uid,
            'follow_uid' => $followUid,
        ];

        //判断是否收藏
        $dataId = $table->where($data)->value('id');
        $isRemove = false;
        if (!$dataId) {
            //添加收藏
            $data['create_time'] = time();
            if (!$table->insert($data)) {
                return $this->retErr(40500, $title . '关注失败！');
            }
            $this->updateData('member', 'fans',  $followUid);
            $res = $this->updateData('member', 'follows', $this->uid, true, 'uid');
            $msg = $title . '关注成功';
        } else {
            //移除收藏
            if (!$table->where($data)->delete()) {
                return $this->retErr(40500, $title . '取消关注失败！');
            }
            $this->updateData('member', 'fans', $followUid, false, 'uid');
            $res = $this->updateData('member', 'follows', $this->uid, false, 'uid');
            $msg = $title . '成功取消关注';
            $isRemove = true;
        }
    
        return $this->retSucc(['code' => 0,'msg' => $msg,'remove' => $isRemove,'result' => $res,]);
    }

    /**
     * 检查是否已经关注
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function isfollow(Request $request)
    {
        if (Actions::isBan('is_follow')) {
            return $this->retErr(40405);
        }

        if (!$this->uid) {
            return $this->retErr(40401);
        }

        $followUid = $request->param('follow_uid');
        if (!$uid = intval($followUid)) {
            return $this->retErr(40004, 'UID参数错误');
        }

        if ($this->uid == $followUid) {
            $isFollow = false;
        } else {
            $map= ['uid' => $this->uid,'follow_uid' => $followUid];
            $dataId = Db::name('user_follow')->where($map)->value('id');
            $isFollow = $dataId? true : false;
        }
        
        return $this->retSucc(['code'   => 0,'msg'    => 'ok','is_follow' => $isFollow]);
    }
    
    /**
     * 公共操作延迟更新，避免频繁写入数据库
     * @param  int  $id
     * @return mixed
     */
    protected function updateData($table, $field, $id, $isInc = true, $pk ='id')
    {
        $res = Actions::update($table, $field, $id, $isInc, $pk);
        if (false == $res) {
            return $this->retErr(40004);
        }
        return $res;
    }

    public function miss()
    {
        return $this->retErr(40404);
    }
}
