<?php
namespace app\index\model;
use think\Model;
use think\Db;

class Website extends Model
{
    protected $table = 'cj_website';
    
    public function getList(){
        
        
        $model = model('Website');
        
        $datas = $model->limit(2)->select();
        
        //$datas = Website::select();
        
        $datas = Db::name('website')->select();
        
        
        var_dump($datas);
        exit;
        
    }
   
    
    
}