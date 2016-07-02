<?php
/**
* MyTemplate_Compiler
* ==============================================
* 版权所有 2015-2025 
* ----------------------------------------------
* 这不是一个自由软件，未经授权不许任何使用和传播。
* ==============================================
* @date: 2016年4月21日  上午10:56:19
* @author: liufeilong(alonglovehome@163.com)
* @version: 1.0.0.0
*/
class MyTemplate_Compiler{
    
    private $_compile_max_include_num = 10;
    private $_once_include_num = 0;
    private $_content_matches = array();
    
    /**
     * 构造函数
     * @date: 2016年4月21日  上午11:11:18
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public function __construct($cache_redis_host=NULL,$cache_redis_port=NULL,$cache_redis_time=NULL){
      
        $this->_cache_redis_port = $cache_redis_port;
        $this->_cache_redis_time = $cache_redis_time;
      
    }
    /**
     * 编译
     * @date: 2016年4月21日  上午11:04:03
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public function compile_resource($template,$compiler_file){
        //1.获得模板文件内容
        $contents = $this->_readfileContent($template);
        
        //2.分析模板内容中所有的模板内容
        $this->_getTemplateMyTemplateContent($contents);

        //3.处理模板内容
        $contents = $this->_compileTemplate($contents);

        //4.把替换后内容写入 
        $r = $this->_writeContentToFile($contents, $compiler_file);
        if($r){
            return $compiler_file;
        }else{
            return false;
        }
        
    } 
    /**
     * 读取模板内容
     * @date: 2016年5月3日  下午4:42:01
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    private function _readfileContent($filename){
        if(file_exists($filename) && is_readable($filename) && $fb = @fopen($filename, 'rb')){
            $content = '';
            while(!feof($fb)){
                $content .= fread($fb, 8192);
            }
        }else{
           throw new MyTemplate_Exception($filename.' cannot be read');
        }
        return $content;
    }
    /**
     * 获得模板内容中所有模板标签内容
     * @date: 2016年5月3日  下午4:54:21
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    private function _getTemplateMyTemplateContent($content){
        preg_match_all('~[\{]\s*(.*?)\s*[\}]~is', $content,$this->_content_matches);  
    }
    /**
     * 替换标签内容
     * @date: 2016年5月3日  下午5:01:56
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    private function _compileTemplate($contents){
        $c = count($this->_content_matches[0]);
        if($c > 0 ){
            for($i=0;$i<$c;$i++){
                $matches = array();
                preg_match_all('/\s*^([\w\/]*)\s*([\w]*)=?[\"\']?(.*)[\"\']*?/is', $this->_content_matches[1][$i],$matches);
                $tag = !empty($matches[1][0])?trim($matches[1][0]):NULL;
                $this->_compileTag($tag,$matches,$this->_content_matches[0][$i],$contents);
            }
        }
        return $contents;
    }
    /**
     * 根据标签编译内容
     * @date: 2016年5月3日  下午5:19:17
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    private function _compileTag($tag,$matches,$search,&$contents){
        switch($tag){
           case 'include':
                $file = trim($matches[3][0],"'");
                if(isset($file)){
                   $contents = $this->_findContentsInclude($file,$search,$contents);
                }else{
                        throw new MyTemplate_Exception('include file is not empty');
                }
                break;
           case 'foreach':
                $fcontent = trim(str_replace($tag,'',$matches[0][0]));
                $farr = array();
                $farr = explode(' ', $fcontent);
                $value = array();
                for($i=0,$c = count($farr);$i<$c;$i++){
                   $varr = array();
                   $varr = explode('=', $farr[$i]);
                   $value[$varr[0]] = $varr[1];
                }
                list($from,$item,$key) = array(ltrim($value['from'],'$'),$value['item'],$value['key']);
                if(!empty($from) && !empty($item)){
                  $contents = $this->_findContentsForeach($from,$item,$key,$search,$contents);
                }else{
                  throw new MyTemplate_Exception('Foreach label incomplete');
                }
                break;
           case '/foreach':
                $contents = $this->_findContentEndForeach($search, $contents);
                break;
           case 'if':
               $cond = trim($matches[3][0]);
               $contents = $this->_findContentsIf($cond,$search,$contents);
                break;
           case 'else':
                $contents = $this->_findContentsElse($search, $contents);
                break;
           case 'elseif':
                $cond = trim($matches[3][0]);
                $contents = $this->_findContentsElseIf($cond,$search,$contents);
                break;
           case '/if':
                $contents = $this->_findContentEndIf($search, $contents);
                break;      
           default:
              $cond = trim($matches[3][0]);
              $contents = $this->_findContentsParams($cond,$search,$contents);      
                
            }
            return $contents;
    }
    
    /**
     * 替换模板中所有include内容
     * @date: 2016年4月21日  下午1:50:04
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    private function _findContentsInclude($file,$search,&$contents){
        $repalce = '<?php $this->_getIncludeContent(\''.$file.'\'); ?>';
        $contents = str_replace($search, $repalce, $contents);
        $this->_once_include_num++;
        if($this->_once_include_num > $this->_compile_max_include_num){
           throw new MyTemplate_Exception('once_include_num more than max_include_num!');
        }
        return $contents;
    }
    /**
     * 查找模板中所有{$*}变量
     * @date: 2016年4月21日  上午9:45:05
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
     */
    private function _findContentsParams($cond,$search,&$contents){
        $matches = array();
        preg_match_all('/\$+([a-zA-Z0-9\.\_\-]+)/is', $cond,$matches);
        $c = count($matches[0]);
        if($c > 0){
           for($i=0;$i<$c;$i++){
               $pos = strpos($matches[1][$i],'.');
               if($pos > 0){
                   list($arr,$key) = explode('.', $matches[1][$i]);
                   $replace = '$this->_getTplValue(\''.$arr.'\',\''.$key.'\')';
                   $cond = str_replace($matches[0][$i], $replace, $cond);
               }else{
                   $arr = $matches[1][$i];
                   $replace = '$this->_getTplValue(\''.$arr.'\')';
                   $cond = str_replace($matches[0][$i], $replace, $cond);
               } 
           }
           
           $replace = '<?php echo ('.$cond.') ?>';
           $contents = str_replace($search, $replace, $contents);
        }
        return $contents;
    }
    /**
     * 查找模板中所有开始foreach
     * @date: 2016年4月21日  下午4:04:33
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    private function _findContentsForeach($from,$item,$key,$search,&$contents){
        if(empty($key)){
           $replace = '<?php foreach($this->_getTplValue(\''.$from.'\') as $this->_tpl_vars[\''.$item.'\']){?>';
         }else{
           $replace = '<?php foreach($this->_getTplValue(\''.$from.'\') as $this->_tpl_vars[\''.$key.'\'] => $this->_tpl_vars[\''.$item.'\']){?>';
         }
         $contents = str_replace($search, $replace, $contents);   
         return $contents;
    }
    /**
     * 替换foreach结束标签
     * @date: 2016年5月3日  下午5:57:03
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    private function _findContentEndForeach($search,&$contents){
        $replace = '<?php } ?>';
        $contents = str_replace($search, $replace, $contents);
        return $contents;
    }
    
    /**
     * _findContentsIf
     * @date: 2016年4月25日  下午1:46:15
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    private function _findContentsIf($cond,$search,&$contents){
        $matches = array();
        preg_match_all('/\$+([a-zA-Z0-9\.\_\-]+)/', $cond,$matches);
        $c = count($matches[0]);
        if($c > 0){
            for($i=0;$i<$c;$i++){
                $pos = strpos($matches[1][$i],'.');
                if($pos > 0){
                    list($arr,$key) = explode('.', $matches[1][$i]);
                    $replace = '$this->_getTplValue(\''.$arr.'\',\''.$key.'\')';
                    $cond = str_replace($matches[0][$i], $replace, $cond);
                }else{
                    $arr = $matches[1][$i];
                    $replace = '$this->_getTplValue(\''.$arr.'\')';
                    $cond = str_replace($matches[0][$i], $replace, $cond);
                }
            }
        }
        $replace = '<?php if '.$cond.'{ ?>';
        $contents = str_replace($search, $replace, $contents);
        return $contents;
    }
    /**
     * 替换elseif标签
     * @date: 2016年5月4日  下午12:58:49
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    private function _findContentsElseIf($cond,$search,&$contents){
        $matches = array();
        preg_match_all('/\$+([a-zA-Z0-9\.\_\-]+)/', $cond,$matches);
        $c = count($matches[0]);
        if($c > 0){
            for($i=0;$i<$c;$i++){
                $pos = strpos($matches[1][$i],'.');
                if($pos > 0){
                    list($arr,$key) = explode('.', $matches[1][$i]);
                    $replace = '$this->_getTplValue(\''.$arr.'\',\''.$key.'\')';
                    $cond = str_replace($matches[0][$i], $replace, $cond);
                }else{
                    $arr = $matches[1][$i];
                    $replace = '$this->_getTplValue(\''.$arr.'\')';
                    $cond = str_replace($matches[0][$i], $replace, $cond);
                }
            }
        }
        $replace = '<?php }elseif '.$cond.'{ ?>';
        $contents = str_replace($search, $replace, $contents);
        return $contents;
    }
    /**
     * 替换else标签
     * @date: 2016年5月4日  下午12:01:00
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    private function _findContentsElse($search,&$contents){
        $replace = '<?php }else{ ?>';
        $contents = str_replace($search, $replace, $contents);
        return $contents;
    }
    /**
     * 替换if结束标签
     * @date: 2016年5月4日  下午12:02:26
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    private function _findContentEndIf($search,&$contents){
        $replace = '<?php } ?>';
        $contents = str_replace($search, $replace, $contents);
        return $contents;
    }
    /**
     * 把替换后内容写入文件中
     * @date: 2016年4月21日  上午11:14:50
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    private function _writeContentToFile($content,$file_name){
        $fp = fopen($file_name, 'a');
        $r = fwrite($fp, $content);
        fclose($fp);
        if($r){
            return true;
        }else{
            return false;
        }
    }
}
