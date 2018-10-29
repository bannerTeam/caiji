<?php
namespace app\index\model;

use think\Validate;
use think\Db;

/**
 * 采集视频表
 *
 * @author DEFAULT
 *        
 */
class Vod extends Base
{

    protected $autoWriteTimestamp;

    protected $name = 'vod';

    protected $createTime = 'add_time';

    protected $rule = [
        'title' => 'require',
        'source' => 'require|unique:vod'
    ];

    protected $message = [
        'title' => '标题不能为空',
        'source.require' => '来源地址不能为空',
        'source.unique' => '来源地址已存在'
    ];

    /**
     * 返回详情
     * 
     * @param unknown $w            
     * @return unknown
     */
    public function getInfo($w, $filed = '*')
    {
        return Db::name($this->name)->field($filed)
            ->where($w)
            ->find();
    }

    /**
     * 插入数据
     *
     * @param unknown $data            
     * @return unknown
     */
    public function saveData($data)
    {
        $res['status'] = 0;
        $res['msg'] = '';
        
        $validate = new Validate($this->rule, $this->message);
        $result = $validate->check($data);
        if ($result !== true) {
            $res['msg'] = $validate->getError();
        } else {
            $res['status'] = 1;
            
            $data['add_time'] = time();
            $this->insert($data);
        }
        
        return $res;
    }

    /**
     * 保存字段
     *
     * @param unknown $data            
     * @return unknown
     */
    public function saves($data)
    {
        $res['status'] = 0;
        $res['msg'] = '';
        
        $id = $data['id'];
        unset($data['id']);
        
        $data['last_time'] = time();
        
        $result = Db::name('vod')->where('id=' . $id)->update($data);
        
        if ($result !== false) {
            $res['status'] = 1;
        } else {
            $res['msg'] = '保存失败';
        }
        
        return $res;
    }

    /**
     * 獲取列表
     *
     * @param unknown $where
     *            条件
     * @param string $field
     *            字段
     * @param number $page
     *            页码
     * @param number $limit
     *            每页显示条数
     * @return unknown
     */
    public function getList($where, $field = '*', $page = 1, $limit = 10, $order = 'id desc')
    {
        
        $list =  array();
        // ->where('download_count','<','cut_count')
        // ->where('cut_count', 'exp', Db::raw('>download_count'))
        // $where['download_count'][] = ['cut_count', 'exp', Db::raw('>download_count')];
        $count = $this->field('id')
            ->where($where)
            ->count();
        
        $first = ($page - 1) * $limit;
        if ($count) {
            
            $list = Db::name('vod')->field($field)
                ->where($where)
                ->limit($first, $limit)
                ->order($order)
                ->select();
        }
        
        $ret['count'] = $count;
        $ret['list'] = $list;
        
        return $ret;
    }
}