<?php
/**
* 
* ==============================================
* 版权所有 2015-2025 
* ----------------------------------------------
* 这不是一个自由软件，未经授权不许任何使用和传播。
* ==============================================
* @date: 2016年5月5日  上午10:36:22
* @author: liufeilong(alonglovehome@163.com)
* @version: 1.0.0.0
*/
include_once('MyStream_Compiler.class.php');
include_once('MyStream_Exception.class.php');
include_once('MyStream.class.php');
define('MYSTREAM','mystream');
class MyStreamView{
    
    public $_tpl_vars = array();
    public $_template_dir = NULL;
    public $_cache_redis_host = '127.0.0.1';
    public $_cache_redis_port = 6379;
    public $_cache_data_expire = 3600;
    public $debug = TRUE;
     
    /**
     * 函数用途描述
     * @date: 2016年5月5日  上午11:41:26
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public function __construct($_template_dir,$cache_redis_host=NULL,$cache_redis_port=NULL,$cache_data_expire=NULL){
        MyStream_Compiler::$_template_dir = $_template_dir;
        MyStream_Compiler::$_data_expire = $cache_data_expire;
        $this->_cache_redis_host = $cache_redis_host;
        $this->_cache_redis_port = $cache_redis_port;
        $this->_cache_data_expire = $cache_data_expire;
        self::_getRedisDriver();
        
    }
    /**
     * assign
     * @date: 2016年4月20日  下午3:39:57
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
     */
    public function assign($tpl_val,$value=NULL){
        if(is_array($tpl_val)){
            foreach($tpl_val as $key => $row){
                if($key != ''){
                    $this->_tpl_vars[$key] = $row;
                }
            }
        }else{
            if($tpl_val !=''){
                $this->_tpl_vars[$tpl_val] = $value;
            }
        }
    }
    /**
     * display
     * @date: 2016年4月20日  下午3:40:36
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
     */
    public function display($template){
        $this->render($template,true);
    }
    /**
     * render
     * @date: 2016年4月20日  下午3:41:03
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
     */
    public function render($template,$display=false){
        if($display){
            ob_start();
            $lastErrorLevel = error_reporting(0);
            set_error_handler(array('MyStream_Compiler','getErrorLog'));
            $this->_readTemplate($template);
            if(count(MyStream_Compiler::$_lastIncludeErrorMsg) > 0){
                $msg = '';
                foreach(MyStream_Compiler::$_lastIncludeErrorMsg as $row){
                    $msg .= $row;
                }
                if($this->debug){
                    echo $msg;
                }
            }
            restore_error_handler();
            $r = $this->_getObAllContent();
            header( 'Content-type: text/html; charset=utf-8');
            echo $r;
        }else{
            ob_start();
            $this->_readTemplate($template);
            $r = $this->_getObAllContent();
            return $r;
        }
    }
    
    /**
     * 读取文件
     * @date: 2016年5月5日  上午10:54:49
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    private function _readTemplate($file){
        if(!in_array('MYSTREAM',stream_get_wrappers())){
            stream_register_wrapper(MYSTREAM, get_class(new MyStream()));
        }
        $file = MYSTREAM.'://'.$file;
        @include $file;
    }
    /**
     * 函数用途描述
     * @date: 2016年5月5日  上午10:56:57
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    private function _getObAllContent(){
        $s = '';
        while ( FALSE !== ($_stemp_ = ob_get_clean ()) ) {
            $s = $_stemp_ . $s;
        }
        return $s;
    }  
    /**
     * 获得数据
     * @date: 2016年4月20日  下午5:44:29
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
     */
    private function _getTplValue($arr,$key=NULL){
        if(isset($this->_tpl_vars[$arr])){
            if(is_array($this->_tpl_vars[$arr])){
                if(isset($key)){
                    return $this->_tpl_vars[$arr][$key];
                }else{
                    return $this->_tpl_vars[$arr];
                }
            }else{
                return $this->_tpl_vars[$arr];
            }
        }else{
            return null;
        }
    
    }
    /**
     * 获得编译模板名称
     * @date: 2016年5月4日  上午9:53:49
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
     */
    private function _getCompileFileName($tempalte){
        $pos = strrpos($tempalte,MYDS);
        $tempalte_name = substr($tempalte,$pos+1);
        $file_name = md5($tempalte.filemtime($tempalte)).'_'.$tempalte_name;
        $file_path = $this->_cache_dir.MYDS.$file_name.COMPILE_FILE_FIX;
        return $file_path;
    }
    /**
     * 函数用途描述
     * @date: 2016年5月12日  下午3:53:27
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    private function _getRedisDriver(){
        if(!isset(MyStream_Compiler::$_redis_driver)){
            MyStream_Compiler::$_redis_driver = new Redis();
            $link = MyStream_Compiler::$_redis_driver->connect($this->_cache_redis_host,$this->_cache_redis_port);
            if(!$link){
                throw new MyStream_Exception('redis connect failed!');
            }
        }
    }
}