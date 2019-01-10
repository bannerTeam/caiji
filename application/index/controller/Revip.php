<?php
namespace app\index\controller;

use think\Controller;
use think\Request;
use QL\QueryList;
use think\Exception;

class Revip extends Controller
{

    public $website = 'http://www.99re.com';

    public function index()
    {
        $mode = model('VodMp4');
        
        $arr = [];
        
        $w1 = [];
        $arr['total'] = $mode->getCount($w1);
        
        $w2 = [];
        $w2['source_video_url'] = '';
        $arr['video_empty'] = $mode->getCount($w2);
        
        $w3 = [];
        $w3['source_video_url'] = array(
            'neq',
            ''
        );
        $w3['state'] = 1;
        $arr['video_download'] = $mode->getCount($w3);
        
        $this->assign('count', $arr);
        
        return $this->fetch();
    }

    /**
     * 测试插件是否正常
     */
    public function test(){
        echo file_get_contents('https://www.baidu.com/');
        exit();
    }
    
    public function baidu()
    {
        echo file_get_contents('https://www.baidu.com/');
        exit();
    }

    public function demo()
    {
        echo file_get_contents($this->website);
        exit();
    }

    
    
    public function lst(){
        
       
        $param = input();
        
        $this->assign('domain', $_SERVER['SERVER_NAME']);
        
        $page = isset($param['page']) ? $param['page'] : 1;
        $limit = isset($param['limit']) ? $param['limit'] : 20;
        
        $state = isset($param['state']) ? $param['state'] : 0;        
        
        $mode = model('VodMp4');
        $where = [];
        if(!empty($status)){
            $where['state'] = $state;
        }
        
        $res = $mode->getList($where, '*',  $page , $limit, $order = 'source_data_format desc');
       
        
        
        $this->assign('list', $res['list']);
        $this->assign('res', $res);
        
      
        
        return $this->fetch();
    }
    
    /**
     * http://caiji.sex.com/index.php/index/revip/list
     * 根据分类id,查询对应的列表
     *
     * @param unknown $type_id            
     * @param unknown $type_name            
     */
    public function list()
    {
        print str_repeat(" ", 4096);
        echo ('<style type="text/css">body{font-size:12px;color: #333333;line-height:21px;}span{font-weight:bold;color:#FF0000}b{color:#4c6dec;}b.green{color:green;}</style>');
        
        set_time_limit(0);
        
        $request = input();
        // 页码开始位置
        $pagination = isset($request['pagination']) ? intval($request['pagination']) : 1;
        
        // 网站域名
        $domain = $this->website;
        
        // 首次采集先 获取总页数
        // 采集规则
        // 规则
        $rules = array(
            'page' => array(
                '.pagination a.btn:last',
                'text'
            )
        );
        
        $url = $domain . '/viplatest-updates/';
        $rt = QueryList::get($url)->rules($rules)
            ->query()
            ->removeHead()
            ->getData();
        
        $data = $rt->all();
        if (count($data) == 0) {
            echo ('<b>=========【采集失败】=======</b><br/><a href="/index.php/index/revip/index">返回>> </a><br/><br/>');
            exit();
        }
        
        // 总页数
        $pageCount = ($data[0]['page']);
        
        echo ('<b>=========【总页数:' . $pageCount . '】=======</b><br/>');
        
        // 规则
        $rules = array(
            'title' => array(
                '.thumb a.kt_imgrc',
                'title'
            ),
            'link' => array(
                '.thumb a.kt_imgrc',
                'href'
            ),
            'pic' => array(
                '.thumb .preview img',
                'src'
            ),
            'duration' => array(
                '.thumb span.duration',
                'text'
            ),
            'data' => array(
                '.thumb span.data',
                'text'
            )
        );
        
        $mode = model('VodMp4');
        
        echo ('<b>=========【开始采集】=======</b><br/>');
        for ($i = $pagination; $i <= $pageCount; $i ++) {
            print str_repeat(" ", 4096);
            echo ('=========[第' . $i . '页开始]=======<br/>');
            
            if ($i == 1) {
                $url = $domain . '/viplatest-updates/';
            } else {
                $url = $domain . '/viplatest-updates/' . $i . '/';
            }
            
            // 采集某页面
            $data = QueryList::get($url)->rules($rules)
                ->query()
                ->removeHead()
                ->getData();
            $list = $data->all();
            
            $arr = [];
            $arr['success'] = 0;
            $arr['error'] = 0;
            $arr['exists'] = 0;
            if ($list && count($list) > 0) {
                
                foreach ($list as $k => $v) {
                    print str_repeat(" ", 4096);
                    
                    if (intval($arr['exists']) > 5) {
                        echo ('<span>=====[出现5条重复数据，停止采集]=======</span><br/><a href="/index.php/index/revip/">返回>> </a><br/><br/>');
                        exit();
                    }
                    
                    if (isset($v['title'])) {
                        
                        $wInfo['source'] = $domain . $v['link'];
                        $info = $mode->getInfo($wInfo);
                        if ($info) {
                            $arr['exists'] ++;
                            echo ('<b class="green">[已存在]</b>' . $v['title'] . '<br/>');
                        } else {
                            // 标题
                            $param['title'] = $v['title'];
                            // 详细页面地址
                            $param['source'] = $domain . $v['link'];
                            // 封面图片
                            $param['source_pic'] = $domain . $v['pic'];
                            
                            // 采集视频播放时长
                            $param['source_duration'] = $v['duration'];
                            
                            // 采集视频发布的时间
                            $param['source_data'] = $v['data'];
                            
                            //格式化时间
                            $param['source_data_format'] = self::data_format($v['data']);
                                                        
                            $param['source_video_url'] = '';
                            
                            $res = $mode->saveData($param);
                            if ($res['status']) {
                                $arr['success'] ++;
                                echo ('[成功]' . $v['title'] . '<br/>');
                            } else {
                                $arr['error'][] = $res['msg'];
                                echo ('<span>[失败-' . $res['msg'] . ']</span>' . $v['title'] . '<br/>');
                            }
                            
                            unset($param);
                        }
                    }
                    ob_flush();
                    flush();
                }
            }
            
            // 每100 页跳转一次
            if (empty($i % 100)) {
                self::download_jump('/index.php/index/revip/list?pagination=' . ($i + 1) . '/');
                exit();
            }
            
            echo ('------[第' . $i . '页结束]------<br/>');
            sleep(1);
        }
        
        echo ('<b class="green">*********【结束采集】*********</b><br/><a href="/index.php/index/revip/">返回>> </a><br/><br/>');
    }

    /**
     * 创建文件
     *
     * @param unknown $dir            
     * @return boolean
     */
    public function create_folders($dir)
    {
        return is_dir($dir) or (self::create_folders(dirname($dir)) and mkdir($dir, 0777));
    }

    public function get_info()
    {
        $mode = model('VodMp4');
        $where['source_video_url'] = '';
        $res = $mode->getList($where, '*', 1, 50, 'source_data_format desc');
        
        $count = $res['count'];
        $list = $res['list'];
        if (count($list) == 0) {
            echo '没有可以采集的数据<br/><a href="/index.php/index/revip/">返回>> </a><br/>';
            exit();
        }
        
        echo ('<h3>========= 剩余：[ ' . $count . ']条数据</h3><br/>');
        
        // 获取QueryList实例
        $ql = QueryList::getInstance();
        // 获取到登录表单
        $form = $ql->get('http://www.99re.com/login.php')->find('form');
        
        // 填写GitHub用户名和密码
        $form->find('input[name=username]')->val('selaba');
        $form->find('input[name=pass]')->val('admin123');
        
        // 序列化表单数据
        $fromData = $form->serializeArray();
        $postData = [];
        foreach ($fromData as $item) {
            $postData[$item['name']] = $item['value'];
        }
        
        // 提交登录表单
        $actionUrl = 'http://www.99re.com/login.php';
        $ql->post($actionUrl, $postData);
        
        // 规则
        $rules = array(
            'hd' => array(
                '#download_link_2',
                'href'
            ),
            'lq' => array(
                '#download_link_1',
                'href'
            )
        );
        
        $resData['success'] = 0;
        $resData['error'] = 0;
        foreach ($list as $k => $v) {
            print str_repeat(" ", 4096);
            // 采集需要登录才能访问的页面
            $data = $ql->get($v['source'])
                ->rules($rules)
                ->query()
                ->removeHead()
                ->getData();
            
            if (count($data) > 0) {
                $d['id'] = $v['id'];
                $d['source_video_url'] = $this->website . $data[0]['lq'];
                $d['state'] = 1;
                $mode->saves($d);
                
                $resData['success'] ++;
            } else {
                $resData['error'] ++;
            }
            
            echo ('加载：[ ' . $v['source'] . ']------<br/>');
            print str_repeat(" ", 4096);
            ob_flush();
            flush();
        }
        
        self::download_jump('/index.php/index/revip/get_info', 5);
        
        var_dump($resData);
        exit();
    }

    public function log()
    {
        $dir = '99re/log/';
        
        $param = input();
        
        $is_del = isset($param['del']) ? $param['del'] : 0;
        if (intval($is_del) === 1) {
            $this->deleteDir($dir);
        }
        
        $file_count = 0;
        if (is_dir($dir)) {
            
            if ($dh = opendir($dir)) {
                
                while (($file = readdir($dh)) != false) {
                    
                    if ($file != '.' && $file != '..') {
                        
                        $file_count ++;
                        
                        // 文件名的全路径 包含文件名
                        $filePath = $dir . $file;
                        
                        // 获取文件修改时间
                        $fmt = filemtime($filePath);
                        
                        echo "<span style='color:#666'>(" . date("Y-m-d H:i:s", $fmt) . ")</span> " . $filePath . "<br/>";
                    }
                }
                
                closedir($dh);
            }
        }
        
        if ($file_count > 0) {
            echo '<br/><a href="?del=1">删除日志</a><br/><br/><a href="/index.php/index/revip/">返回>> </a><br/>';
        } else {
            echo '没有日志文件<br/><a href="/index.php/index/revip/">返回>> </a><br/>';
        }
        
        exit();
    }

    /**
     * 删除目录及文件
     *
     * @param unknown $dir            
     * @return boolean
     */
    private function deleteDir($dir)
    {
        if (! $handle = @opendir($dir)) {
            return false;
        }
        while (false !== ($file = readdir($handle))) {
            if ($file !== "." && $file !== "..") { // 排除当前目录与父级目录
                $file = $dir . '/' . $file;
                if (is_dir($file)) {
                    self::deleteDir($file);
                } else {
                    @unlink($file);
                }
            }
        }
        @rmdir($dir);
    }

    /**
     * 下载
     */
    public function download()
    {
        set_time_limit(0);
        
        $param = input();
        
        // 下载总数
        $count = isset($param['count']) ? $param['count'] : 10;
        
        // 分页
        $pagination = isset($param['pagination']) ? $param['pagination'] : 1;
        
        $limit = 10;
        
        if ($pagination * $limit > $count) {
            echo ('=========[全部完成，执行了 ' . ($count) . '个任务]=======<br/><br/><a href="/index.php/index/revip/">返回>> </a><br/>');
            exit();
        }
        
        $mode = model('VodMp4');
        
        $where['source_video_url'] = array(
            'neq',
            ''
        );
        $where['state'] = 1;
        $res = $mode->getList($where, 'id,title,source,source_video_url', 1, $limit, 'source_data_format desc');
        
        $list = $res['list'];
        
        if (count($list) == 0) {
            echo '没有可下载的视频';
            exit();
        }
        
        $save_dir = '99re/video/' . date('Ymd') . '/';
        $log_dir = '99re/log/' . date('Ymd') . '/';
        
        self::create_folders($save_dir);
        self::create_folders($log_dir);
        
        $host = $this->website;
        
        echo ('=========[开始 下载总数:' . $count . ']=======<br/>');
        
        if ($pagination > 1) {
            echo ('=========[已执行 ' . ($limit * ($pagination - 1)) . '个任务]=======<br/>');
        }
        
        foreach ($list as $k => $v) {
            
            print str_repeat(" ", 4096);
            
            echo ('[<a href="' . $v['source'] . '" target="_blank"> ' . $v['title'] . '</a>]------<br/>');
            
            $aa = explode("/", trim($v['source'], '/'));
            
            $name = $aa[count($aa) - 2] . '_' . $aa[count($aa) - 1];
            
            $url = $v['source_video_url'];
            
            $save_dir = '99re/video/' . date('Ymd') . '/' . $name . '.mp4';
            $log_dir = '99re/log/' . date('Ymd') . '/' . $name . '.log';
            
            $wget = "wget -b -N --no-check-certificate -o " . $log_dir . ' ' . $url . ' -O ' . $save_dir;
            
            echo ('保存路径:  ' . $save_dir . '<br/>');
            //echo $wget;
            echo '<br/>';
            // 下载数据
            $rel = shell_exec($wget);
            //var_dump($rel);
            echo '<br/>';
            
            ob_flush();
            flush();
            
            // 更新视频下载状态
            $d = [];
            $d['id'] = $v['id'];
            $d['path'] = $save_dir;
            $d['state'] = 2;
            $mode->saves($d);
            
            sleep(1);
        }
        
        if ($limit * $pagination >= $count) {
            echo ('=========[全部完成，执行了 ' . ($count) . '个任务]=======<br/><br/><a href="/index.php/index/revip/index">返回>> </a><br/>');
            exit();
        }
        
        self::download_jump('/index.php/index/revip/download?count=' . $count . '&pagination=' . ($pagination + 1), 5);
        
        exit();
    }

    
    private function data_format($str){
        
        // 一天
        $day = (60 * 60 * 24);
        
        // 默认当前时间
        $data_format = time();
        
        $add_time = time();
        
        if (strpos($str, '天') !== false) {
            $data_format = $add_time - $day * intval($str);
        } else if (strpos($str, '周') !== false) {
            $data_format = $add_time - $day * 7 * intval($str);
        } else if (strpos($str, '月') !== false) {
            $data_format = $add_time - $day * 30 * intval($str);
        } else if (strpos($str, '年') !== false) {
            $data_format = $add_time - $day * 365 * intval($str);
        } else if (strpos($str, '小时') !== false) {
            $data_format = $add_time - (60 * 60) * intval($str) ;
        }
        
        return $data_format;
    }
    
    public function print(){
        
        set_time_limit(0);
        $count  = 10;
        
        //ob_end_clean();     //在循环输出前，要关闭输出缓冲区   
       
        echo str_repeat(" ", 4096);  
        
        print str_repeat(" ", 4096);
        echo ('<style type="text/css">body{font-size:12px;color: #333333;line-height:21px;}span{font-weight:bold;color:#FF0000}b{color:#4c6dec;}b.green{color:green;}</style>');
        
        
        for ($i = 0; $i < $count; $i++) {
            
            echo "#$i 完毕<hr>";
            print "#$i 完毕<hr>";
            
            echo str_pad(" ", 4096); 
            print str_repeat(" ", 4096);
            
            
            ob_flush();
            flush();
            sleep(1);
        }        
        
        exit();
        
    }
    
    public function demo_data_format()
    {
        $mode = model('VodMp4');
        
        $where = [];
        $where['source_data_format'] = '0';
        $res = $mode->getList($where, 'id,source_data,add_time', 1, 13000);
        
        $list = $res['list'];
        if (count($list) == 0) {
            echo '全部更新完成';
            exit();
        }
        
        $find = [
            '周',
            '天',
            '年',
            '月',
            '小时'
        ];
        
        // 一天
        $day = (60 * 60 * 24);
        
        foreach ($list as $k => $v) {
            
            $str = $v['source_data'];
            
            $add_time = $v['add_time'];
            
            // 默认当前时间
            $data_format = time();
            
            if (strpos($str, '天') !== false) {
                $data_format = $add_time - $day * intval($str);
            } else if (strpos($str, '周') !== false) {
                $data_format = $add_time - $day * 7 * intval($str);
            } else if (strpos($str, '月') !== false) {
                $data_format = $add_time - $day * 30 * intval($str);
            } else if (strpos($str, '年') !== false) {
                $data_format = $add_time - $day * 365 * intval($str);
            } else if (strpos($str, '小时') !== false) {
                $data_format = $add_time - (60 * 60) * intval($str) ;
            }
            
            $d['id'] = $v['id'];
            $d['source_data_format'] = $data_format;
            
            $r = $mode->saves($d);
        }
        
        // self::download_jump('/index.php/index/revip/data_format');
    }

    /**
     * 页面自动跳转
     *
     * @param unknown $url
     *            跳转地址
     * @param number $sec
     *            秒
     */
    public function download_jump($url, $sec = 2)
    {
        echo '<script>setTimeout(function (){location.href="' . $url . '";},' . ($sec * 1000) . ');</script><br><span>暂停' . $sec . '秒后继续  >>>  </span><a href="' . $url . '" >如果您的浏览器没有自动跳转，请点击这里</a><br>';
    }
}

