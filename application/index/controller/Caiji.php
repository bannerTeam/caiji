<?php
namespace app\index\controller;

use think\Controller;
use think\Db;
use think\Request;
use QL\QueryList;

class Caiji extends Controller
{

    public function index()
    {
        return $this->fetch();
    }

    /**
     * 采集站点1
     */
    public function avhd101()
    {
        set_time_limit(0);
        // 规则
        $rules = array(
            // 采集id为one这个元素里面的纯文本内容
            'title' => array(
                '.list header h3 a span',
                'text'
            ),
            // 采集class为two下面的超链接的链接
            'link' => array(
                '.list header h3 a',
                'href'
            )
        );
        
        $mode = model('Vod');
        
        // 网站域名
        $domain = 'https://avhd101.com';
        
        $request = Request::instance();
        $pageCount = $request->request("pl", 1, "intval");
        
        print str_repeat(" ", 4096);
        echo ('<style type="text/css">body{font-size:12px;color: #333333;line-height:21px;}span{font-weight:bold;color:#FF0000}</style>');
        
        for ($i = 1; $i <= $pageCount; $i ++) {
            
            echo ('=========[第' . $i . '页开始]=======<br/>');
            
            // 采集某页面
            $data = QueryList::get('https://avhd101.com/hd', [
                'page' => $i
            ], [
                'headers' => [
                    'referer' => 'https://avhd101.com/hd'
                ]
            ])->rules($rules)
                ->query()
                ->getData();
            
            $list = $data->all();
            if ($list && count($list) > 0) {
                
                $arr['success'] = 0;
                $arr['error'] = array();
                
                foreach ($list as $k => $v) {
                    print str_repeat(" ", 4096);
                    if (isset($v['title'])) {
                        $param['title'] = $v['title'];
                        $param['source'] = $domain . $v['link'];
                        $param['website_id'] = 1;
                        $res = $mode->saveData($param);
                        if ($res['status']) {
                            $arr['success'] ++;
                            echo ('[成功]' . $v['title'] . '<br/>');
                        } else {
                            $arr['error'][] = $res['msg'];
                            echo ('<span>[失败-' . $res['msg'] . ']</span>' . $v['title'] . '<br/>');
                        }
                    }
                    ob_flush();
                    flush();
                }
            }
            print str_repeat(" ", 4096);
            echo ('------[第' . $i . '页结束]------<br/>');
            sleep(1);
        }
        
        echo ('*********[全部执行结束]*********<br/>');
        ob_flush();
        flush();
        exit();
    }

    /**
     * 采集站点2 www.youav.com
     */
    public function youav()
    {
        set_time_limit(0);
        // 规则
        $rules = array(
            'title' => array(
                '.well-sm .video-title',
                'text'
            ),
            'link' => array(
                '.well-sm > a',
                'href'
            )
        );
        
        $mode = model('Vod');
        
        // 网站域名
        $domain = 'https://www.youav.com';
        
        $request = Request::instance();
        $pageCount = $request->request("pl", 1, "intval");
        
        print str_repeat(" ", 4096);
        echo ('<style type="text/css">body{font-size:12px;color: #333333;line-height:21px;}span{font-weight:bold;color:#FF0000}b{color:#4c6dec;}</style>');
        
        for ($i = 1; $i <= $pageCount; $i ++) {
            print str_repeat(" ", 4096);
            echo ('=========[第' . $i . '页开始]=======<br/>');
            
            // 采集某页面
            $data = QueryList::get('https://www.youav.com/videos', [
                'page' => $i
            ])->removeHead()
                ->rules($rules)
                ->query()
                ->getData();
            
            $list = $data->all();
            if ($list && count($list) > 0) {
                
                $arr['success'] = 0;
                $arr['error'] = array();
                
                foreach ($list as $k => $v) {
                    print str_repeat(" ", 4096);
                    if (isset($v['title'])) {
                        $param['title'] = $v['title'];
                        $param['source'] = $domain . $v['link'];
                        $param['website_id'] = 2;
                        $res = $mode->saveData($param);
                        if ($res['status']) {
                            $arr['success'] ++;
                            echo ('[成功]' . $v['title'] . '<br/>');
                        } else {
                            $arr['error'][] = $res['msg'];
                            echo ('<span>[失败-' . $res['msg'] . ']</span>' . $v['title'] . '<br/>');
                        }
                    }
                    ob_flush();
                    flush();
                }
            }
            echo ('------[第' . $i . '页结束]------<br/>');
            sleep(1);
        }
        
        echo ('*********[全部执行结束]*********<br/>');
        
        exit();
    }

    /**
     * 采集站点3：http://www.52lu78.com
     * 获取站点全部分类
     */
    public function lu78()
    {
        set_time_limit(0);
        // 规则
        $rules = array(
            'title' => array(
                '.small_menu li a[target=_self]',
                'text'
            ),
            'link' => array(
                '.small_menu li a[target=_self]',
                'href'
            )
        );
        
        // 采集某页面
        $data = QueryList::get('http://www.52lu78.com/')->removeHead()
            ->rules($rules)
            ->query()
            ->getData();
        
        $type_list = $data->all();
        
        if ($type_list && count($type_list) > 0) {
            
            print str_repeat(" ", 4096);
            echo ('<style type="text/css">body{font-size:12px;color: #333333;line-height:21px;}span{font-weight:bold;color:#FF0000}b{color:#4c6dec;}b.green{color:green;}</style>');
            
            echo ('*********【采集开始执行】*********<br/>');
            
            foreach ($type_list as $k => $v) {
                
                $type_id = preg_replace('/\D/', '', $v['link']);
                // 循环检索列表
                $this->lu78_type($type_id, $v['title']);
            }
            
            echo ('*********【采集完成执行】*********<br/>');
        } else {
            echo ('<span>*********【规则匹配失败】*********</span><br/>');
        }
        
        exit();
    }

    /**
     * 采集站点3：http://www.52lu78.com
     * 根据分类id,查询对应的列表
     * 
     * @param unknown $type_id            
     * @param unknown $type_name            
     */
    public function lu78_type($type_id, $type_name)
    {
        if (empty($type_id)) {
            echo ('=========参数错误=======<br/>');
            return false;
        }
        
        set_time_limit(0);
        // 规则
        $rules = array(
            'title' => array(
                'li .link-hover',
                'title'
            ),
            'link' => array(
                'li .link-hover',
                'href'
            ),
            'pic' => array(
                'li .link-hover .lazy',
                'src'
            )
        
        );
        
        $mode = model('Vod');
        
        // 网站域名
        $domain = 'http://www.52lu78.com';
        
        $request = Request::instance();
        $pageCount = $request->request("pl", 1, "intval");
        
        echo ('<b>=========【' . $type_name . '】=======</b><br/>');
        for ($i = 1; $i <= $pageCount; $i ++) {
            print str_repeat(" ", 4096);
            echo ('=========[第' . $i . '页开始]=======<br/>');
            
            if ($i == 1) {
                $url = 'http://www.52lu78.com/?m=vod-type-id-' . $type_id . '.html';
            } else {
                $url = 'http://www.52lu78.com/?m=vod-type-id-' . $type_id . '-pg-' . $i . '.html';
            }
            
            // 采集某页面
            $data = QueryList::get($url)->removeHead()
                ->rules($rules)
                ->query()
                ->getData();
            $list = $data->all();
            
            if ($list && count($list) > 0) {
                
                $arr['success'] = 0;
                $arr['error'] = array();
                
                foreach ($list as $k => $v) {
                    print str_repeat(" ", 4096);
                    if (isset($v['title'])) {
                        $param['title'] = $v['title'];
                        
                        // 详情页面规则，替换成视频播放页面的地址
                        $link = str_replace("-detail-", "-play-", $v['link']);
                        $link = str_replace(".html", "-src-1-num-1.html", $link);
                        
                        $param['source'] = $domain . $link;
                        
                        $param['source_type_name'] = $type_name;
                        
                        $param['source_video_url'] = $this->lu78_m3u8($domain . $link);
                        
                        $param['source_pic'] = $v['pic'];
                        $param['website_id'] = 3;
                        $res = $mode->saveData($param);
                        if ($res['status']) {
                            $arr['success'] ++;
                            echo ('[成功]' . $v['title'] . '<br/>');
                        } else {
                            $arr['error'][] = $res['msg'];
                            echo ('<span>[失败-' . $res['msg'] . ']</span>' . $v['title'] . '<br/>');
                        }
                    }
                    ob_flush();
                    flush();
                }
            }
            echo ('------[第' . $i . '页结束]------<br/>');
            sleep(1);
        }
        
        echo ('<b class="green">*********【' . $type_name . '】[执行结束]*********</b><br/>');
    }

    /**
     * 采集站点：http://www.52lu78.com
     * 根据详细页面地址采集视频地址
     * 
     * @param unknown $play_url            
     * @return string
     */
    public function lu78_m3u8($play_url)
    {
        set_time_limit(0);
        
        // 第一步根据详细页面获取 视频地址
        $data = QueryList::get($play_url);
        
        if ($data) {
            $html = $data->removeHead()->getHtml();
            
            $preg = '/mac_url=unescape\(\'(.*)\'\)/';
            
            preg_match_all($preg, $html, $res);
            
            if ($res) {
                $str = ($res[1][0]);
                $str = $this->unescape($str);
                $arr = explode('$', $str);
                
                if (count($arr) == 1) {
                    $m3u8 = $str;
                } else if (count($arr) == 2) {
                    $m3u8 = $arr[1];
                } else {
                    $m3u8 = '';
                }
                return $m3u8;
            }
        }
        
        return '';
        
        if (empty($m3u8)) {
            echo '地址解析错误';
            exit();
        }
        
        $arr = explode('/', $m3u8);
        // 获取目录关键字
        $path_key = $arr[count($arr) - 2];
        // 2. 开始 下载 m3u8引导文件
        $save_dir = "./down/video/lu78/" . $path_key . '/';
        $filename = "index.m3u8";
        $url = $m3u8;
        $res = $this->getFile($url, $save_dir, $filename);
        
        // 3. 打开文件
        $file = fopen($save_dir . $filename, "r");
        $lines = array();
        $i = 0;
        // 输出文本中所有的行，直到文件结束为止。
        while (! feof($file)) {
            $lines[$i] = fgets($file); // fgets()函数从文件指针中读取一行
            $i ++;
        }
        fclose($file);
        
        $hls = $lines[count($lines) - 1];
        $hls_m3u8_url = str_replace("index.m3u8", $hls, $url);
        
        // 4. 下载 引用 ts 的m3u8文件
        $save_dir_ts = $save_dir . str_replace("index.m3u8", '', $hls);
        $filename = "index.m3u8";
        $res_m3u8 = $this->getFile($hls_m3u8_url, $save_dir_ts, $filename);
        
        // 5. 下载key
        $hls_key_url = str_replace("index.m3u8", 'key.key', $hls_m3u8_url);
        $save_dir_key = str_replace("index.m3u8", 'key.key', $save_dir_ts);
        $filename = "key.key";
        $res_key = $this->getFile($hls_key_url, $save_dir_key, $filename);
        
        $ret[] = $res;
        $ret[] = $res_m3u8;
        $ret[] = $res_key;
        
        print_r($ret);
        
        exit();
    }

    /**
     * 下載 m3u8 相关文件
     */
    public function lu78_download()
    {
        set_time_limit(0);
        $mode = model('Vod');
        
        print str_repeat(" ", 4096);
        echo ('<style type="text/css">body{font-size:12px;color: #333333;line-height:21px;}span{font-weight:bold;color:#FF0000}b{color:#4c6dec;}b.green{color:green;}</style>');
        
        echo '**********[开始下载基础文件(m3u8、key、img)]**********<br/>';
        
        ob_flush();
        flush();
        $where['website_id'] = 3;
        $where['cut_count'] = 0;
        $where['state'] = 1;
        
        $limit = 5;
        $datas = $mode->getList($where, 'id,title,source_video_url,source_pic',1,$limit);
        $list = $datas['list'];
        
        $r = array();
        foreach ($list as $k => $v) {
            print str_repeat(" ", 4096);
            echo '**********<b>[开始下载:'.$v['title'].']</b>**********<br/>';
            
            ob_flush();
            flush();
            
            print str_repeat(" ", 4096);
            
            $ret = $this->lu78_download_m3u8($v['source_video_url'],$v['source_pic']);
            if ($ret) {                
                $data['id'] = $v['id'];
                $data['cut_count'] = $ret['count'];
                $data['cut_rule'] = $ret['rule'];
                $data['path_cut'] = $ret['cut_path'];
                $data['pic'] = $ret['pic'];
                $r[] = $mode->saves($data);
                
                echo '======<b class="green">[完成:'.$v['title'].']</b>======<br/>';
            }else{
                echo 'xxxxxxxxx<span>[失败:'.$v['title'].']</span>xxxxxxxxx<br/>';
            }
            ob_flush();
            flush();
        }
        
        echo '**********<b>[执行完成]</b>**********<br/>';
        
        exit();
    }

    /**
     *
     * @param unknown $m3u8
     *            视频地址
     */
    public function lu78_download_m3u8($m3u8,$source_pic)
    {
        
        if(empty($m3u8)){
            return false;
        }
        $arr = explode('/', $m3u8);
          
        
        if(count($arr) < 3){
            return false;
        }        
        
        
        // 获取目录关键字
        $path_key = $arr[count($arr) - 2];
        // 2. 开始 下载 m3u8引导文件
        $save_dir = "./down/video/lu78/" . $path_key . '/';
        $filename = "index.m3u8";
        $url = $m3u8;
        $res = $this->getFile($url, $save_dir, $filename);
        
        if(empty($res)){
            return false;
        }
        $pic = '';
        //下载封面
        if($source_pic){
            $save_dir_pic = "./down/video/lu78/" . $path_key . '/';
            $arr_pic = explode('/', $source_pic);
            $filename_pic = $arr_pic[count($arr_pic) - 1];
            $res = $this->getFile($source_pic, $save_dir_pic, $filename_pic);
            if($res){
                $pic = $save_dir_pic.$filename_pic;
            }
        }
        
        // 3. 打开文件
        $file = fopen($save_dir . $filename, "r");
        $lines = array();
        $i = 0;
        // 输出文本中所有的行，直到文件结束为止。
        while (! feof($file)) {
            $lines[$i] = fgets($file); // fgets()函数从文件指针中读取一行
            $i ++;
        }
        fclose($file);
        
        $hls = $lines[count($lines) - 1];
        $hls_m3u8_url = str_replace("index.m3u8", $hls, $url);
        
        // 4. 下载 引用 ts 的m3u8文件
        $save_dir_ts = $save_dir . str_replace("index.m3u8", '', $hls);
        $filename = "index.m3u8";
        $res_m3u8 = $this->getFile($hls_m3u8_url, $save_dir_ts, $filename);
        
        // 5. 下载key
        $hls_key_url = str_replace("index.m3u8", 'key.key', $hls_m3u8_url);
        $save_dir_key = str_replace("index.m3u8", 'key.key', $save_dir_ts);
        $filename = "key.key";
        $res_key = $this->getFile($hls_key_url, $save_dir_key, $filename);
        
        // 6. 打开文件
        $file = fopen($save_dir_ts . "index.m3u8", "r");
        $lines = array();
        $i = 0;
        // 输出文本中所有的行，直到文件结束为止。
        while (! feof($file)) {
            $str = fgets($file); // fgets()函数从文件指针中读取一行
            $lines[$i] = strpos($str, ".ts");
            
            if (strpos($str, ".ts") !== false) {
                $lines[$i] = $str;
                $i ++;
            }
        }
        fclose($file);        
        
        $turl = str_replace('index.m3u8', '', $hls_m3u8_url);
        $turl = str_replace('000.ts', '{num}.ts', $turl.$lines[0]);
        
        $cut_rule = $turl;
        $cut_count = $i;
        
        $ret['count'] = $cut_count;
        $ret['rule'] = $cut_rule;
        $ret['cut_path'] = $save_dir_ts;
        $ret['pic'] = $pic;
        
        return $ret;
        
        $ret[] = $res;
        $ret[] = $res_m3u8;
        $ret[] = $res_key;
    }

    /**
     * 采集站点：http://www.52lu78.com
     * 下载切片
     */
    public function lu78_download_ts()
    {
        set_time_limit(0);
        $mode = model('Vod');
        
        print str_repeat(" ", 4096);
        echo ('<style type="text/css">body{font-size:12px;color: #333333;line-height:21px;}span{font-weight:bold;color:#FF0000}b{color:#4c6dec;}b.green{color:green;}</style>');
        
        
        $request = Request::instance();
        $id = $request->request("id", 0, "intval");
        
        if($id){
            
            $where['id'] = $id;  
            $where['website_id'] = 3;
            $where['cut_rule'] = array('neq','');
            $where['path_cut'] = array('neq','');
            $data = $mode->getInfo($where);
            
            if(empty($data)){
                echo '**********<span>[视频不存在]</span>**********<br/>';
                exit;
            }else if($data['state'] == 3){
                echo '**********<b>[视频已全部下载]</b>**********<br/>';
                exit;
            }else if($data['cut_count'] == $data['download_count']){
                echo '**********<b>[视频已全部下载!]</b>**********<br/>';
                exit;
            }
            
        }else{
            
            //ID不存在就获取 符合条件的数据进行下载
                       
            $where['website_id'] = 3;
            $where['state'] = 1; 
            $where['cut_count']  = array('>',0);
            $where['cut_rule'] = array('neq','');
            $where['path_cut'] = array('neq','');
            $data = $mode->getInfo($where);
            
            if(empty($data)){
                echo '**********<span>[没有等待下载的视频]</span>**********<br/>';
                exit;
            }
            
        }
                
        print str_repeat(" ", 4096);        
        
        echo '**********[开始下载任务]**********<br/>';        
        
        echo '======[开始下载：'.$data['title'].']=====<br/>';
        
        echo '切片保存路径：'.$data['path_cut'].'<br/>';
        
        $this->lu78_download_ts_line($data);
        
        echo '======[下载完成：'.$data['title'].']=====<br/>';
        
        ob_flush();
        flush();
        
        echo '**********<b>[准备执行下个任务]</b>**********<br/>';        
        $url = '/index.php/index/caiji/lu78_download_ts';
        $this->download_jump($url,$sec=3);
              
        
        exit();
    }
    
    /**
     * 采集站点：http://www.52lu78.com
     * 逐个下载切片
     */
    public function lu78_download_ts_line($v)
    {
        $mode = model('Vod');        
        
        
        $rule = $v['cut_rule'];
        $cut_count = $v['cut_count'];
        $download_count = $v['download_count'];  
        
        
        if(empty($download_count)){
            $download_count = 0;
        }
        // 切片保存路径
        $save_dir = $v['path_cut'];     
        $num = 1;
        
        for ($i = $download_count; $i < $cut_count; $i++) {
            
            if($num == 1){
                $data['id'] = $v['id'];
                $data['state'] = 2;
                $mode->saves($data);
            }
            
            print str_repeat(" ", 4096);
            
            echo "下载切片[".$i."]<br/>";
            $j = $i;
            if($j<100){
                //生成3位数，不足前面补0 
                $j = str_pad($i,3,"0",STR_PAD_LEFT);
            }               
            
            $url = str_replace('{num}', $j, $rule);
            //分割地址
            $arr = explode('/', $url);
            //获取文件名
            $filename = $arr[count($arr)-1];
                       
            $res = $this->getFile($url, $save_dir, $filename);
                        
            if($res){
               //下载完成，切片+1
               $w['id']=$v['id'];
               $r = $mode->setFildInc('vod',$w,'download_count'); 
               
               if($i == ($cut_count-1)){
                   $data['id'] = $v['id'];
                   $data['state'] = 3;
                   $mode->saves($data);
               }
               
            }
            ob_flush();
            flush();
            
            $num++;
            if($num == 50){
                $url = '/index.php/index/caiji/lu78_download_ts?id='.$v['id'];
                $this->download_jump($url,$sec=2);
                exit();
            }
        }
        //var_dump($res);
    }
    
    /**
     * 页面自动跳转
     * @param unknown $url 跳转地址
     * @param number $sec 秒
     */
    public function download_jump($url,$sec=2)
    {
        echo '<script>setTimeout(function (){location.href="'.$url.'";},'.($sec*1000).');</script><span>暂停'.$sec.'秒后继续  >>>  </span><a href="'.$url.'" >如果您的浏览器没有自动跳转，请点击这里</a><br>';
    }
    
    /**
     * 采集站点：http://www.52lu78.com
     * 下载切片
     */
    public function lu78_m3u8_ts()
    {        
        set_time_limit(0);
        // 获取切片路径，进行下载
        $save_dir = "./down/video/lu78/JRea6V8t/500kb/hls/";
        
        for ($i = 281; $i < 282; $i ++) {
            $ts = "HZMdn3964" . $i . ".ts";
            $filename = $ts;
            $url = 'http://video2.jiagew762.com:8091/20181006/JRea6V8t/500kb/hls/' . $ts;
            $res = $this->getFile($url, $save_dir, $filename);
        }
        
        var_dump($res);
    }

    function echo()
    {
        for ($i = 1; $i < 10; $i ++) {
            print str_repeat(" ", 4096);
            echo $i . '<br>';
            ob_flush();
            flush();
            sleep(1);
        }
        
        echo '全部执行完成';
        exit();
        
        // http://www.sex.com/tiancai.php/admin/collect/api.html?
        // xt=1&ct=&rday=&cjflag=zimuwang&cjurl=http%3A%2F%2Fzmwcj8.com%2Finc%2Fapi.php
        // &t=&page=%7Bpage%7D&limit=%7Blimit%7D&h=24&ac=cjday&ids=
    }

    function demo()
    {
        
        // 2. 开始 下载 m3u8引导文件
        $save_dir = './down/video/lu78/JRea6V8t/500kb/hls/';
        $filename = "HZMdn3964000.ts";
        $url = 'http://video2.jiagew762.com:8091/20181006/JRea6V8t/500kb/hls/HZMdn3964000.ts';
        $res = $this->getFile($url, $save_dir, $filename);
        exit();
        
        // $url = "http://www.baidu.com/img/baidu_jgylogo3.gif";
        $save_dir = "./down/";
        $filename = time() . ".gif";
        // $res = getFile($url, $save_dir, $filename, 1); // 0 1 都是好使的
        
        $url = 'https://t11.baidu.com/it/u=2090892775,2730930627&fm=76';
        $res = $this->getFile($url, $save_dir, $filename);
        var_dump($res);
    }

    /**
     * 实现远程网络文件下载到服务器指定目录
     *
     * @param unknown $url            
     * @param string $save_dir            
     * @param string $filename            
     * @param number $type            
     * @return boolean|string[]|number[]
     */
    function getFile($url, $save_dir = '', $filename = '', $type = 0)
    {
        $url = trim($url);
        $save_dir = trim($save_dir);
        $filename = trim($filename);
        if (trim($url) == '') {
            return false;
        }
        if (trim($save_dir) == '') {
            $save_dir = './';
        }
        if (0 !== strrpos($save_dir, '/')) {
            if(strlen($save_dir)>5){
                $save_dir = rtrim($save_dir,"/");
            }
            $save_dir .= '/';
        }
        // 创建保存目录
        if (! file_exists($save_dir) && ! mkdir($save_dir, 0777, true)) {
            return false;
        }        
        
        if (file_exists($save_dir . $filename)) {
            // 已存在就删除
            unlink($save_dir . $filename);
        }
        
        // 获取远程文件所采用的方法
        if ($type) {
            $ch = curl_init();
            $timeout = 60;
            curl_setopt($ch, CURLOPT_URL, trim($url));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $content = curl_exec($ch);
            curl_close($ch);
        } else {
            ob_start();
            readfile(trim($url));
            $content = ob_get_contents();
            ob_end_clean();
        }
        // echo $content;
        $size = strlen($content);
       
        // 文件大小
        $fp2 = @fopen($save_dir . $filename, 'w');
        
        if($size && $fp2){
            fwrite($fp2, $content);
            fclose($fp2);
            unset($content, $url);
            return array(
                'file_name' => $filename,
                'save_path' => $save_dir . $filename,
                'file_size' => $size
            );
        }
        return false;
        
    }

    public function download()
    {
        echo THINK_VERSION;
        exit();
        $download = new \think\response\Download('./down/baidu_jgylog1o31.gif');
        return $download->name('my');
        // 设置300秒有效期
        return download('./down/baidu_jgylog1o31.gif', 'my')->expire(300);
    }

    public function hls()
    {
        // $preg= '/mac_url=unescape(\'[\s\S]*?\');/i';
        $preg = '/mac_url=unescape\(\'(.*)\'\)/';
        
        $str = "var mac_flag='play',mac_link='/?m=vod-play-id-90639-src-{src}-num-{num}.html', mac_name='超清黑丝多毛少妇椅子上香蕉自慰.mp4',mac_from='ckplayer',mac_server='0',mac_note='',mac_url=unescape('http%3A%2F%2Fvideo.yjf138.com%3A8091%2F20181003%2FG8aUttSH%2Findex.m3u8'); ";
        
        preg_match_all($preg, $str, $res);
        
        if ($res) {
            var_dump($res[1]);
        }
        
        exit();
        
        $data = QueryList::get('https://youapi.ml/api/sources/4lo07rx0x9q', [], [
            'headers' => [
                'accept' => '*/*',
                'accept-encoding' => 'gzip, deflate, br',
                'accept-language' => 'zh-CN,zh;q=0.9',
                'cache-control' => 'no-cache',
                'content-length' => '0',
                'cookie' => '__cfduid=dabd77c01e1b7d3aa79d152c96123e7411538551053; _ym_uid=1538551057232538868; _ym_d=1538551057',
                'origin' => 'https://youapi.ml',
                'pragma' => 'no-cache',
                'referer' => 'https://youapi.ml/v/4lo07rx0x9q',
                'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.106 Safari/537.36',
                'x-requested-with' => 'XMLHttpRequest'
            ]
        ]);
        
        print_r($data);
        exit();
        // 采集某页面
        $data = QueryList::get('https://www.youav.com/ajax/hls.php?server=2&pid=14105');
        
        $video_url = $data->getHtml();
        
        if ($video_url) {
            $arr = explode("/", $video_url);
            $key = $arr[count($arr) - 1];
            
            $data = QueryList::get('https://youapi.ml/api/sources/' . $key);
            
            print_r($data->getHtml());
        }
        
        exit();
    }

    public function unescape($str)
    {
        $ret = '';
        $len = strlen($str);
        for ($i = 0; $i < $len; $i ++) {
            if ($str[$i] == '%' && $str[$i + 1] == 'u') {
                $val = hexdec(substr($str, $i + 2, 4));
                if ($val < 0x7f)
                    $ret .= chr($val);
                else if ($val < 0x800)
                    $ret .= chr(0xc0 | ($val >> 6)) . chr(0x80 | ($val & 0x3f));
                else
                    $ret .= chr(0xe0 | ($val >> 12)) . chr(0x80 | (($val >> 6) & 0x3f)) . chr(0x80 | ($val & 0x3f));
                $i += 5;
            } else if ($str[$i] == '%') {
                $ret .= urldecode(substr($str, $i, 3));
                $i += 2;
            } else
                $ret .= $str[$i];
        }
        return $ret;
    }
}





