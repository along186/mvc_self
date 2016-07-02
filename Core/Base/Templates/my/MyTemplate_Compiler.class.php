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
    
   
    private $_cache_dir = NULL;
    private $_template_dir = NULL;
    private $_compile_max_include_num = 10;
    private $_once_include_num = 0;
    /**
     * 构造函数
     * @date: 2016年4月21日  上午11:11:18
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public function __construct($cache_dir,$_template_dir){
        $this->_cache_dir = $cache_dir;
        $this->_template_dir = $_template_dir;
    }
    /**
     * 编辑
     * @date: 2016年4月21日  上午11:04:03
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public function compile_resource($template){
        //1.获得模板文件内容
        $contents = @file_get_contents($template);

        //2.获得模板文件编辑后文件名
        $compile_name = $this->_createCompileNameByTemplate($template);
        
        //3.替换include信息
        $contents = $this->_findContentsInclude($contents);

        //4.处理模板内容中所有foreach
        $contents = $this->_findContentsForeach($contents);

        //5.获得变量替换后内容
        $contents = $this->_findContentsParams($contents);
        
        //4.把替换后内容写入 
        $r = $this->_writeContentToFile($contents, $compile_name);
        if($r){
            return $this->_cache_dir.'/'.$compile_name;
        }else{
            return false;
        }   
    }
    /**
     * compile_source
     * @date: 2016年4月24日 下午6:26:22
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public function compile_source($template){
        //1.获得模板文件内容
        $contents = @file_get_contents($template);
        
        //2.获得模板文件编辑后文件名
        $compile_name = $this->_createCompileNameByTemplate($template);
        
        //3.获得变量替换后内容
        $contents = $this->_findContentsParams($contents);
        
        //4.把替换后内容写入
        $r = $this->_writeContentToFile($contents, $compile_name);
        if($r){
            return $this->_cache_dir.'/'.$compile_name;
        }else{
            return false;
        }
    } 
    /**
     * 替换include
     * @date: 2016年4月21日  下午1:50:04
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    private function _findContentsInclude(&$contents){
        $matches = array();
        preg_match_all('~[{]\s*include\s*file=(.*?)\s*[}]~s', $contents,$matches);
        $c = count($matches[0]);
        if($c > 0){
           for($i=0;$i<$c;$i++){
               $includes = "'".trim($matches[1][$i],"'")."'";
               $repalce = '<?php $this->_getIncludeContent('.$includes.'); ?>';
               $contents = str_replace($matches[0][$i], $repalce, $contents);
               $this->_once_include_num++;
               if($this->_once_include_num > $this->_compile_max_include_num){
                   throw new Exception('once_include_num more than max_include_num!');
               }else{
                   continue;
               }
           }
           $this->_findContentsInclude($contents);
        }
        return $contents;
    }
    /**
     * 查找模板中所有{$*}变量
     * @date: 2016年4月21日  上午9:45:05
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
     */
    private function _findContentsParams(&$contents){
        $matches = array();
        preg_match_all('~[{]\s*\$+([a-zA-Z0-9]*)[\.]?([^]]*?)[\]]?\s*[}]~s', $contents,$matches);
        if(count($matches) > 0){
            $c = count($matches[0]);
            for($i=0;$i<$c;$i++){
                $matches[1][$i] = "'".$matches[1][$i]."'";
                $matches[2][$i] = "'".$matches[2][$i]."'";
                $replace = '<?php echo $this->_getTplValue('.$matches[1][$i].','.$matches[2][$i].') ?>';
                $contents = str_replace($matches[0][$i], $replace, $contents);
            }
        }
        return $contents;
    }
    /**
     * 查找模板中所有foreach
     * @date: 2016年4月21日  下午4:04:33
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    private function _findContentsForeach(&$contents){
        $matches = array();
        preg_match_all('~(({foreach\s*(.*?)})(.*?)({\s*/foreach\s*}))~s', $contents,$matches);
        $c = count($matches[0]);
        if($c > 0){
            for($i=0;$i<$c;$i++){
                $params = $this->_getContentsForeachParam($matches[3][$i]);
                $from = "'".substr($params['from'], 1)."'";
                $eachkey = $params['item'].'_mytemplate';
                $key = !empty($params['key'])?$params['key']:'';
                if(!empty($key)){
                    $repalce = '<?php foreach($this->_getTplValue('.$from.') as $'.$key.' =>'.$eachkey.' ){?>';
                }else{
                    
                }
                $repalce = '<?php foreach($this->_getTplValue('.$from.') as $'.$eachkey.' >){ $'.$key.'++;?>';
                $contents = str_replace($matches[2][$i], $repalce, $contents); 
                $repalce = $this->_findContentsForeachParam($matches[4][$i],$eachkey);
                $contents = str_replace($matches[4][$i], $repalce, $contents); 
                //var_dump($contents);die;
                $repalce = '<?php } ?>';
                $contents = str_replace($matches[5][$i], $repalce, $contents);
            }
        }
        return $contents;
    }
    /**
     * 函数用途描述
     * @date: 2016年4月21日  下午6:07:55
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    private function _getContentsForeachParam($str){
        $params = explode(' ',trim($str));
        $count = count($params);
        $item = array();
        for($j=0;$j<$count;$j++){
            $itemarr = array();
            $itemarr = explode('=', $params[$j]);
            $item[$itemarr[0]] = $itemarr[1];
        }
        return $item;
    }
    /**
     * 函数用途描述
     * @date: 2016年4月23日 下午8:33:00
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    private function _findContentsForeachParam($str,$key){
        $matches = array();
        preg_match_all('~[{]\s*\$+([a-zA-Z0-9]*?)\.([a-zA-Z0-9]*?)\s*[}]~s', $str,$matches);
        //var_dump($matches);die;
        $c = count($matches[2]);
        if($c > 0){
            for($a=0;$a<$c;$a++){
                $matches[2][$a] = "'".$matches[2][$a]."'";
                $rep = '<?php echo $'.$key.'['.$matches[2][$a].']; ?>';
                $str = str_replace($matches[0][$a], $rep, $str);
            }
        }
        return $str;
    }
    /**
     * 返回编辑后文件名称
     * @date: 2016年4月21日  上午11:12:48
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    private function _createCompileNameByTemplate($template){
        $pos = strrpos($template,MYDS);
        $template_name = substr($template,$pos+1);
        $file_name = md5($template.filemtime($template)).'_'.$template_name.COMPILE_FILE_FIX;
        return $file_name;
    }
    /**
     * 函数用途描述
     * @date: 2016年4月21日  上午11:14:50
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    private function _writeContentToFile($content,$file_name){
        $file_path = $this->_cache_dir.MYDS.$file_name;
        $fp = fopen($file_path, 'a');
        $r = fwrite($fp, $content);
        fclose($fp);
        if($r){
            return true;
        }else{
            return false;
        }
    }
    
}