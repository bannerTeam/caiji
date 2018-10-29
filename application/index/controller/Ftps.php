<?php
namespace app\index\controller;

use think\Controller;
use app\common\util\FtpTool;

class Ftps extends Controller
{

    public function index()
    {
        //项目磁盘路径
        $project_path = $_SERVER['DOCUMENT_ROOT'];
        
       

        $ftp = new FtpTool();
        
        $file_path = $project_path.'/robots.txt';
        
        $r = $ftp->up_file($file_path, 'robots.txt');
        
        
        var_dump($r);
        
        exit();
    }
}

