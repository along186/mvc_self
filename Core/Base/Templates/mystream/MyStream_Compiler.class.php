<?php
/**
* MyStream_Compiler编译器
* ==============================================
* 版权所有 2015-2025 
* ----------------------------------------------
* 这不是一个自由软件，未经授权不许任何使用和传播。
* ==============================================
* @date: 2016年5月5日  上午11:10:44
* @author: liufeilong(alonglovehome@163.com)
* @version: 1.0.0.0
*/
define('COMPILE_FILE_FIX','.php');
define('MYDS','/');
abstract class MyStream_Compiler{
    
    public static $_data_expire = 3600;
    public static $_template_dir = NULL;
    public static $_redis_driver = NULL;
    
    //php错误编码
    private static $_php_Error_Code = array (
        '1' => 'E_ERROR',
        '2' => 'E_WARNING',
        '4' => 'E_PARSE',
        '8' => 'E_NOTICE',
        '16' => 'E_CORE_ERROR',
        '32' => 'E_CORE_WARNING',
        '64' => 'E_COMPILE_ERROR',
        '128' => 'E_COMPILE_WARNING',
        '256' => 'E_USER_ERROR',
        '512' => 'E_USER_WARNING',
        '1024' => 'E_USER_NOTICE',
        '2048' => 'E_STRICT',
        '4096' => 'E_RECOVERABLE_ERROR',
        '8192' => 'E_DEPRECATED',
        '16384' => 'E_USER_DEPRECATED' );
    
    public static $_lastIncludeErrorMsg = array();

    /**
     * 函数用途描述
     * @date: 2016年5月5日  上午11:15:43
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public static function getViewData($template){
        $compiler_key = self::getCompilerTemplateName($template);
        $compiler_content = self::$_redis_driver->get($compiler_key);
        if($compiler_content){
            return $compiler_content;
        }else{
            $compiler_content = self::_compile_resource($template,$compiler_key);
            return $compiler_content;
        }
    }
    /**
     * 编译
     * @date: 2016年4月21日  上午11:04:03
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
     */
    private static function _compile_resource($template,$compiler_key){
        //1.获得模板文件内容
        $contents = self::_readfileContent($template);
        
        //2.分析模板内容中所有的模板内容
        $content_matches = self::_getTemplateMyTemplateContent($contents);
    
        //3.处理模板内容
        $contents = self::_compileTemplate($contents,$content_matches,$compiler_key);
        
        return $contents;
    
    }
    /**
     * 函数用途描述
     * @date: 2016年5月9日  下午3:58:13
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public static function getErrorLog($errno, $errstr, $errfile, $errline){
        switch ($errno) {
            case E_NOTICE :
            case E_USER_NOTICE :
                $msg = NULL;
                break;
            default :
                $msg = "Template Parse Error : [$errno : " . self::_phpErrorCode ( $errno ) . "] $errstr In File: $errfile Line: $errline <br />";
        }
        array_push(self::$_lastIncludeErrorMsg, $msg);
        return true;
    }
    /**
     * 函数用途描述
     * @date: 2016年5月9日  下午4:00:27
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    private static function _phpErrorCode($code){
        $code = '' . $code;
        return isset ( self::$_php_Error_Code [$code] ) ? self::$_php_Error_Code [$code] : NULL;
    }
    /**
     * 读取模板内容
     * @date: 2016年5月3日  下午4:42:01
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
     */
    private static function _readfileContent($filename){
        $content = @file_get_contents($filename);
        return $content;
    }
    /**
     * 获得模板内容中所有模板标签内容
     * @date: 2016年5月3日  下午4:54:21
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
     */
    private static function _getTemplateMyTemplateContent($content){
        $content_matches = array();
        preg_match_all('~[\{]\s*(.*?)\s*[\}]~is', $content,$content_matches);
        return $content_matches;
    }
    /**
     * 替换标签内容
     * @date: 2016年5月3日  下午5:01:56
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
     */
    private static function _compileTemplate($contents,$content_matches,$compiler_key){
        $c = count($content_matches[0]);
        if($c > 0 ){
            for($i=0;$i<$c;$i++){
                $matches = array();
                preg_match_all('/\s*^([\w\/]*)\s*([\w]*)=?[\"\']?(.*)[\"\']*?/is', $content_matches[1][$i],$matches);
                $tag = !empty($matches[1][0])?trim($matches[1][0]):NULL;
                self::_compileTag($tag,$matches,$content_matches[0][$i],$contents);
            }
        }
        self::$_redis_driver->setex($compiler_key,self::$_data_expire,$contents);
        return $contents;
        
    }
    /**
     * 根据标签编译内容
     * @date: 2016年5月3日  下午5:19:17
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
     */
    private static function _compileTag($tag,$matches,$search,&$contents){
        switch($tag){
            case 'include':
                $file = trim($matches[3][0],"'");
                $contents = self::_findContentsInclude($file,$search,$contents);
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
                $contents = self::_findContentsForeach($from,$item,$key,$search,$contents); 
                break;
            case '/foreach':
                $contents = self::_findContentEndForeach($search, $contents);
                break;
            case 'if':
                $cond = trim($matches[3][0]);
                $contents = self::_findContentsIf($cond,$search,$contents);
                break;
            case 'else':
                $contents = self::_findContentsElse($search, $contents);
                break;
            case 'elseif':
                $cond = trim($matches[3][0]);
                $contents = self::_findContentsElseIf($cond,$search,$contents);
                break;
            case '/if':
                $contents = self::_findContentEndIf($search, $contents);
                break;
            default:
                $cond = trim($matches[3][0]);
                $contents = self::_findContentsParams($cond,$search,$contents);
    
        }
        return $contents;
    }
    
    /**
     * 替换模板中所有include内容
     * @date: 2016年4月21日  下午1:50:04
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
     */
    private static function _findContentsInclude($file,$search,&$contents){
        $repalce = '<?php @include(\''.MYSTREAM.'://'.self::$_template_dir.MYDS.$file.'\'); ?>';
        $contents = str_replace($search, $repalce, $contents);
        return $contents;
    }
    /**
     * 查找模板中所有{$*}变量
     * @date: 2016年4月21日  上午9:45:05
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
     */
    private static function _findContentsParams($cond,$search,&$contents){
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
    private static function _findContentsForeach($from,$item,$key,$search,&$contents){
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
    private static function _findContentEndForeach($search,&$contents){
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
    private static function _findContentsIf($cond,$search,&$contents){
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
    private static function _findContentsElseIf($cond,$search,&$contents){
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
    private static function _findContentsElse($search,&$contents){
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
    private static function _findContentEndIf($search,&$contents){
        $replace = '<?php } ?>';
        $contents = str_replace($search, $replace, $contents);
        return $contents;
    }
    /**
     * 函数用途描述
     * @date: 2016年5月5日  上午11:28:46
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    private static function getCompilerTemplateName($template){
        $pos = strrpos($template,MYDS);
        $tempalte_name = substr($template,$pos+1);
        $file_name = md5($template.filemtime($template)).'_'.$tempalte_name;
        $file_path = $file_name.COMPILE_FILE_FIX;
        return $file_path;
    }
}