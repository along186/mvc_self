<?php
/**
* 自定义模板引擎（MyTemplate）
* ==============================================
* 版权所有 2015-2025 
* ----------------------------------------------
* 这不是一个自由软件，未经授权不许任何使用和传播。
* ==============================================
* @date: 2016年4月20日  下午3:38:42
* @author: liufeilong(alonglovehome@163.com)
* @version: 1.0.0.0
*/
include_once('MyTemplate_Exception.class.php');
define('COMPILE_FILE_FIX','.php');
define('MYDS','/');
class MyTemplate{
    
    public $_tpl_vars = array();
    public $_template_dir = NULL;
    public $_cache_dir = NULL;
   
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
        if(!isset($this->_template_dir)){
            throw new MyTemplate_Exception('MyTemplate template_dir must be set');
        }
        if($display){
            $compiler_file = $this->_compile_resource($template);
            ob_start();
            @include($compiler_file);
            $results = ob_get_contents();
            ob_end_clean();
            echo $results;
        }else{
            return $this->_compile_resource($template);
        }       
    }
    /**
     * 函数用途描述
     * @date: 2016年5月4日  下午3:41:39
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    private function _includeTemplate($file){ 
        @include $file;
    }
    /**
     * compile_resource
     * @date: 2016年4月20日  下午4:14:07
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    private function _compile_resource($template){
        //1.检查编辑文件是否存在
        $compiler_file = $this->_checkCompileFileExist($template);
        if($compiler_file){
            return $compiler_file;
        }else{
            include_once('MyTemplate_Compiler.class.php');
            $compiler_file = $this->_getCompileFileName($template);
            $compiler = new MyTemplate_Compiler();
            $compiler_file = $compiler->compile_resource($template,$compiler_file);
        } 
        return $compiler_file;
        
        
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
     * 获得include内容
     * @date: 2016年4月25日  上午9:17:50
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    private function _getIncludeContent($includes){
        $compiler_file = $this->_compile_resource($this->_template_dir.$includes);
        @include($compiler_file);
    }
    /**
     * 检查编译文件是否存在
     * @date: 2016年4月21日  上午11:38:00
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    private function _checkCompileFileExist($tempalte){ 
       if(!isset($this->_cache_dir)){
           $cache_dir = $this->_template_dir."caches";
           if(!is_dir($cache_dir)){
              $r = mkdir($cache_dir,'775');
              chmod($cache_dir,0755);
              if($r){
                 $this->_cache_dir = $cache_dir;
              }else{
                 throw new MyTemplate_Exception('MyTemplate create cache directory failed');
              }
           }else{
              $this->_cache_dir = $cache_dir;
           }
        }
        $file_path = $this->_getCompileFileName($tempalte);
        if(file_exists($file_path)){
            return $file_path;
        }else{
            return false;
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
}
