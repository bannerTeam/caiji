<?php
namespace app\index\model;
use think\Model;
use think\Db;
use think\Cache;
use app\common\util\Pinyin;

class Base extends Model {

    //自定义初始化
    protected function initialize()
    {        
        parent::initialize();
        //TODO:自定义的初始化
    }
    
    
    public function setFildInc($tab_name,$where,$field){
       
        return Db::name($tab_name)->where($where)->setInc($field);
    }
    
    public function setFildDec($tab_name,$field){
        return Db::name($tab_name)->where($where)->setDec($field);
    }
    
}