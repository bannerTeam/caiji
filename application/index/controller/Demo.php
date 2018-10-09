<?php
namespace app\index\controller;

use think\Controller;

class Demo extends Controller
{
    public function video()
    {  
        return  $this->fetch();
       
    }
    
}

