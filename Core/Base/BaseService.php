<?php
/**
* 入口驱动类
* ==============================================
* 版权所有 2015-2025 
* ----------------------------------------------
* 这不是一个自由软件，未经授权不许任何使用和传播。
* ==============================================
* @date: 2016年4月7日  上午10:12:17
* @author: liufeilong(alonglovehome@163.com)
* @version: 1.0.0.0
*/
abstract class BaseService{
    
    private static $_router = NULL;
    private static $_include_files_arr = array();
    private static $_session = NULL;
    /**
     * 对外入口方法
     * @date: 2016年4月7日  下午2:16:47
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public static final function run($appId){
        try{
            self::_run($appId);
        }catch(Exception $e){
            if(DEBUG){
                echo $e->__toString();die;
            }
        }
    }
    /**
     * 真正的入口方法
     * @date: 2016年4月7日  下午1:36:39
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    private static final function _run($appId){
        $appId = ucfirst($appId);
        self::setAppId($appId);
        
        $app_list = self::_getBaseConfigData('app_list');
        if(!isset($app_list[self::getAppId()])){
            throw new BaseException($appId.' is not found in app_list');
        }
        $request = &self::getUrlRequest();
        //加载comcontroller
        $path = self::getComControllerPath($appId);
        if($path){
            self::includeClassFile($path);
        }
        return self::_runController($request);
        
        
    }
    /**
     * 获得ComController路径
     * @date: 2016年5月16日  下午1:01:06
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    private static final function getComControllerPath($appId){
        $apps_config = self::_getBaseConfigData('apps_path');
        $apps_path = '';
        if($apps_config['type'] == 'abs'){
            $apps_path = rtrim($apps_config['path'],DS);
        }else{
            $apps_path = dirname(BASE_PATH).DS.ucfirst($apps_config['apps']);
        }
        $ComController_path = $apps_path.DS.ucfirst($appId).DS.'ComController.php';
        if(is_readable($ComController_path) && file_exists($ComController_path)){
            return $ComController_path;
        }else{
            return false;
        }
    }
    /**
     * 运行controller
     * @date: 2016年4月11日  上午10:20:23
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    private static final function _runController($request){
        if(!isset($request[MODULE])){
            throw new BaseException('module is not found');
        }
        if(!isset($request[CONTROLLER])){
            throw new BaseException('controller is not found');
        }
        if(!isset($request[ACTION])){
            throw new BaseException('action is not found');
        }
        $module = ucfirst($request[MODULE]);
        $controller = ucfirst($request[CONTROLLER]).CONTROLLER_FIX;
        $action =  ucfirst($request[ACTION]).ACTION_FIX;
        $controller_file_path = self::_getClassFilePath($controller, $module, 'Controllers');
        self::includeClassFile($controller_file_path);
        $r = self::_newClass($controller);
        $r->initAction($request);
        BaseService::ob_start();
        if(!$r->beforeAction()){
            throw new BaseException('beforeAction is not success');
        }
        if(!method_exists($r, $action)){
            throw new BaseException($action.' is not found in '.$controller);
        }
        self::_callClassMethod($r, $action, $request);
        if(!$r->afterAction()){
            throw new BaseException('beforeAction is not success');
        }
        $result = BaseService::ob_get_clean_all();
        echo $result; 
    }
    /**
     * 打开缓存区同时关闭刷出
     * @date: 2016年4月11日  下午3:24:40
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public static final function ob_start(){
        ob_start();
        ob_implicit_flush(false);
    } 
    /**
     * 获得缓存区域内容并删除当前输出缓
     * @date: 2016年4月11日  下午3:27:49
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public static final function ob_get_clean_all(){
        $s = '';
        while (($stmp = ob_get_clean()) == !FALSE){
            $s =$stmp . $s;
        }
        return $s;
    }
    /**
     * 调用方式
     * @date: 2016年4月11日  下午3:04:04
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    private static final function _callClassMethod($object_name,$method,$params){
        $r = @call_user_func_array(array($object_name,$method), $params);
        return $r;
    }
    /**
     * 获得类文件路径
     * @date: 2016年4月11日  上午10:31:17
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    private static final function _getClassFilePath($class_name,$module,$class_path_dir,$appid=NULL){
        if(!isset($appid)){
            $appid = self::getAppId();
        }
        if(!isset($module)){
            $module = Base::$module;
        }
        $apps_config = self::_getBaseConfigData('apps_path');
        $apps_path = '';
        if($apps_config['type'] == 'abs'){
            $apps_path = rtrim($apps_config['path'],DS);
        }else{
            $apps_path = dirname(BASE_PATH).DS.ucfirst($apps_config['apps']);
        }
        $app_cur_path_config = self::getAppConfigData('app_path',$appid,$appid);
        $app_cur_path = '';
        if($app_cur_path_config['type'] == 'abs'){
            $app_cur_path = rtrim($app_cur_path_config['path'],DS);
        }else{
            $app_cur_path = $apps_path.DS.ucfirst($appid);
        }
        $pos = strpos($class_name, '.');
        if($pos > 0){
            $file_path = $app_cur_path.DS.ucfirst($module).DS.ucfirst($class_path_dir);
            $class_name_arr = explode('.', $class_name);
            $c = count($class_name_arr);
            $j = $c-1;
            for($i=0;$i<$c;$i++){
                if($i == $j){
                    $file_path = $file_path.DS.$class_name_arr.'.php';
                }else{
                    $file_path = $file_path.DS.$class_name_arr[$i];
                }
            }
        }else{
            $file_path = $app_cur_path.DS.ucfirst($module).DS.ucfirst($class_path_dir).DS.$class_name.'.php';
        }
        if(!file_exists($file_path)){
            throw new BaseException($file_path.' class file is not exist');
        }
        return $file_path;
    }  
    /**
     * 引用文件
     * @date: 2016年4月11日  下午1:34:05
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public static final function includeClassFile($files,$singlen=TRUE,$appid=NULL){
        $files = preg_replace('$[/\\\\]{1,}$', '/', $files);
        $md5files = strtolower($files);
        if(!isset($appid)){
            $appid = self::getAppId();
        }
        if($singlen == TRUE){
            if(!isset(self::$_include_files_arr[$appid][$md5files])){
                @include($files);
                self::$_include_files_arr[$appid][$md5files] = 1;
            }
        }else{
            @include_once($files);
        }
        return true;
    }
    /**
     * 函数用途描述
     * @date: 2016年4月11日  下午4:39:27
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public static final function newClass($class_name,$singlen=TRUE,$appid=NULL){
        if(!isset($appid)){
            $appid = BaseService::getAppId();
        }
        if(isset($singlen)){
            if($singlen == TRUE){
                $singlen = TRUE;
            }else{
                $singlen = FALSE;
            }
        }else{
            $singlen = TRUE;
        }
        return self::_newClass($class_name,$singlen,$appid);
    }
    /**
     * 实例化类
     * @date: 2016年4月11日  下午1:57:41
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    private static final function _newClass($class_name,$singlen=TRUE,$appid=NULL){
        $r = null;
        if(class_exists($class_name,FALSE)){
           $r = new $class_name(); 
        }
        return $r;
    } 
    /**
     * 系统md5加密
     * @date: 2016年4月11日  下午1:44:35
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public static final function baseMd5($md5str){
        if(is_file($md5str)){
            $fileupdatetime = filemtime($md5str);
            $md5 = md5($md5str.$fileupdatetime);
        }else{
            $md5 = md5($md5str);
        }
        return $md5;
    }
    /**
     * 设置系统 appid
     * @date: 2016年4月7日  下午2:25:36
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public static final function setAppId($appid){
        Base::setAppId($appid);
    }
    /**
     * 获得系统appid
     * @date: 2016年4月7日  下午2:28:09
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public static final function getAppId(){
        return Base::getAppId();
    }
    /**
     * 获得base配置文件指定节点数据
     * @date: 2016年4月7日  下午2:18:31
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    private static final function _getBaseConfigData($node){
        return BaseConfig::getBaseConfigData($node);
    }
    /**
     * 获得指定app配置信息
     * @date: 2016年4月7日  下午2:33:27
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public static final function getAppConfig($files){
        return BaseConfig::getAppConfig($files);
    }
    /**
     * 获得指定app配置文件指定节点
     * @date: 2016年4月8日  下午5:26:55
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public static final function getAppConfigData($node,$files=NULL){ 
        if(!isset($files)){
            $files = BaseService::getAppId();
        }
        return BaseConfig::getAppConfigData($node, $files);
    }
    /**
     * 
     * @date: 2016年4月7日  下午1:47:55
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public static final function &getUrlRequest(){
        if(!isset(self::$_router)){
            self::$_router = &BaseRouter::getUrlRequest();
        }
        return self::$_router;
    }
    /**
     * 加载component
     * @date: 2016年4月7日  上午10:26:04
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public static function loadComponent($class_name,$module=NULL,$appid=NULL,$singlen=TRUE){
        $r = self::_loadClass($class_name, $module,'Components',$appid,$singlen);
        $r->setAppConfigId($appid);
        $r->setAppModuleId($module);
        return $r;
    }
    /**
     * 加载model
     * @date: 2016年4月7日  上午10:27:01
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public static function loadModel($class_name,$module=NULL,$appid=NULL,$singlen=TRUE){
        $r = self::_loadClass($class_name, $module,'Models',$appid,$singlen);
        $r->setAppConfigId($appid);
        $r->setAppModuleId($module);
        return $r;
    }
    /**
     * 函数用途描述
     * @date: 2016年4月7日  上午10:27:20
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public static function loadExtension($class_name,$module=NULL,$appId=NULL,$singlen=TRUE){
        
    }
    /**
     * 获得类实例
     * @date: 2016年4月12日  下午1:27:32
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public static function _loadClass($class_name,$module=NULL,$class_path_dir,$appid=NULL,$singlen=TRUE){
        if(!isset($appid)){
            $appid = self::getAppId();
        }
        $pos = strpos($class_name, '.');
        $class_true_name = '';
        if($pos > 0){
            $class_arr = explode('.', $class_name);
            $c = count($class_arr);
            if($c > 0){
                for($i=0;$i<$c;$i++){
                    $j = $i+1;
                    if($j == $c){
                        $class_true_name = $class_arr[$i];
                        break;
                    }else{
                        continue;
                    }
                }
            }
        }else{
            $class_true_name = $class_name;
        }
        $class_file = self::_getClassFilePath($class_name, $module, $class_path_dir,$appid);
        self::includeClassFile($class_file,$singlen,$appid);
        $r = self::newClass($class_true_name,$singlen,$appid);
        return $r;
    }
    /**
     * 获得缓存驱动
     * @date: 2016年4月14日  下午1:26:11
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public static function getCacheDriver($config_key=NULL,$appid=NULL){
        return BaseCache::getCacheDriver($config_key,$appid);
    }
    /**
     * retJson
     * @date: 2016年4月15日  下午3:47:45
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public static final function retJson($code,$msg,$data=NULL){
        $res = array();
        $res['code'] = $code;
        $res['msg'] = $msg;
        if(!is_null($data)){
            $res['data'] = $data;
        }
        echo json_encode($res);
    }
    /**
     * 函数用途描述
     * @date: 2016年5月12日  下午4:41:58
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public static final function getSession($sessionid=NULL){
        if(!isset(self::$_session)){
            self::includeClassFile(BASE_PATH.DS.'Base'.DS.'BaseSession.php');
            self::$_session = BaseSession::getInterface($sessionid);
        }
        return self::$_session;   
    }
}