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

use app\common\model\Songs;
use think\Request;

class SongsController extends ApiController
{
    /**
     * 显示资源列表
     * @return \think\Response
     */
    public function index(Request $request)
    {
        $param = $request->get();
        if (empty($param)) {
            return json([ 'code' => 40404, 'error' => '参数错误', ], 404);
        }

        $model = new Songs;
        $list  = $model->getList($param);
        if ($list) {
            if (isset($param['page'])) {
                $list = $list->items();
            }
            
            foreach ($list as &$val) {
                if (isset($param['sub'])) {
                    $val['sub'] = $val->sub;
                }
                $val = $model->parseData($val);
            }
            
            return $this->retSucc(['result' => $list]);
        }
        return $this->retErr(40407);
    }

    /**
     * 显示指定的资源
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id = 0)
    {
        if (!intval($id)) {
            return json([ 'code' => 40404, 'error' => '参数错误', ], 404);
        }
        //缓存数据
        $model = new Songs;
        $data  = $model->hidden(['create_time', 'update_time'])->getInfo($id);
        if ($data) {
            return $this->retSucc(['result' => $model->parseData($data)]);
        }
        return $this->retErr(40407);
    }

    /**
     * 保存更新的资源
     * @param  \think\Request $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
    }

    /**
     * 保存更新的资源
     * @param  \think\Request $request
     * @param  int  $id
     * @return \think\Response
     */
    public function delete(Request $request, $id)
    {
    }

}
