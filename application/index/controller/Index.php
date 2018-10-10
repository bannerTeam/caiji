<?php
namespace app\index\controller;

use QL\QueryList;

class Index
{
    public function index()
    {  
        

        $data = QueryList::get('http://api.cao2018.com/inc/api.php??m=collect-list-ac2--hour--xt-1-ct--group--flag-520ziyuancom_');
        
        //?m=collect-list-ac2--hour--xt-1-ct--group--flag-520ziyuancom_-apiurl-
        //http://api.cao2018.com/inc/api.php
        
        var_dump($data);
        exit;
        $json_str = ($data->getHtml());
        
        
        
        $json = json_decode($json_str , true);
        
        
        
        
        $this->vod_add($json['data']);        
        
        
        
        //http://www.sex8.com/admin/index.php?
        //m=collect-cj-ac2-day-hour-24-xt-1-ct--group--flag-dasenlin_top-
        //apiurl-http://www.520ziyuan.com//inc/api.php
        
        
    }
    
    /**
     * 微信采集
     */
    public function wx(){
        
        
        $url = 'https://mp.weixin.qq.com/s?src=11&timestamp=1523173327&ver=803&signature=6PCxJ*3ojH2ZM8pm56Lquward0mQMwSkPnqCvYlrDkQmL2kAEjGcFJMj2lzvpHyuyT30lczb2Ld0npUWmp*2Gj7bPJY3SCWrpRKlXJA0p4eQWPpAzMPJVmxPcRV5TtLS&new=1';
        
        // 采集规则
        $rules = [
            'title' => ['.rich_media_title','text'],
            'date' => ['#post-date','text'],
            'author' => ['#meta_content>.rich_media_meta:eq(2)','text'],
            'content' => ['.rich_media_content','html']
        ];
        
        $data = QueryList::get($url)->rules($rules)->query()->getData();
        
        print_r($data->all());
    }
    
    
    function vod_add($data){
        
        if(empty($data)){
            echo 'error';
            exit();
        }
        
        
        
        $vod = new \app\index\model\Vod();
        
        //视频名称
        $sdata['vod_name'] = $data['title'];
        //视频分类id
        $sdata['type_id'] = '1';
        //视频介绍
        $sdata['vod_content'] = $data['title'];
        
       
       
        //视频图片
        $sdata['vod_pic'] =  $data['thumbUrls'][0];
        
        
        $sdata['vod_play_from'][0] = 'swf'; 
        $sdata['vod_play_server'][0] = '';        
        $sdata['vod_play_note'][0] = '';
        $sdata['vod_play_url'][0] = $data['playUrl'];
        
        //发布状态
        $sdata['vod_status'] = '1';
        
        
        $result = $vod->saveData($sdata);
        
        var_dump($result);
        exit();
    }
    
    /**
     * php模拟post请求
     */
    function get_post_curl($url, $data){
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false); // required as of PHP 5.6.0
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch); /*释放*/
        return $result;
    }
    
}

