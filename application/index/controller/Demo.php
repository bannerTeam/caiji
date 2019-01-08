<?php
namespace app\index\controller;

use think\Controller;
use think\Request;
use QL\QueryList;
use think\Exception;

class Demo extends Controller
{
    
    
    public function video()
    {  
        return  $this->fetch();
       
    }
    
    
    
    /**
     * 99热 模拟登录
     */
    public function relogin(){
        
        // 获取QueryList实例
        $ql = QueryList::getInstance();
        //获取到登录表单
        $form = $ql->get('http://www.99re.com/login.php')->find('form');
        
        //填写GitHub用户名和密码
        $form->find('input[name=username]')->val('selaba');
        $form->find('input[name=pass]')->val('admin123');
        
        //序列化表单数据
        $fromData = $form->serializeArray();
        $postData = [];
        foreach ($fromData as $item) {
            $postData[$item['name']] = $item['value'];
        }
        
        //提交登录表单
        $actionUrl = 'http://www.99re.com/login.php';
        $ql->post($actionUrl,$postData);
       
        //采集需要登录才能访问的页面
        $ql->get('http://www.99re.com/videos/103577/720p399/');
        //echo $ql->getHtml();
        
        print_r($ql->getHtml());
        exit;
        
    }
    
}

