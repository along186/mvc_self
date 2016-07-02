<?php
/**
* 基本抽象类
* ==============================================
* 版权所有 2015-2025 
* ----------------------------------------------
* 这不是一个自由软件，未经授权不许任何使用和传播。
* ==============================================
* @date: 2016年4月7日  上午9:53:04
* @author: liufeilong(alonglovehome@163.com)
* @version: 1.0.0.0
*/
abstract class Base{
    
    public static $appId = NULL;
    public static $controller = NULL;
    public static $action = NULL;
    public static $module = NULL;
    public $appid = NULL;
    public $moduleid = NULL;
    /**
     * 设置当前appId
     * @date: 2016年4月7日  上午9:58:16
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public static final function setAppId($appid){
         self::$appId = ucfirst($appid);
    }
    /**
     * 获得当前的appId
     * @date: 2016年4月7日  上午10:06:56
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public static final function getAppId(){
        if(isset(self::$appId)){
            return self::$appId;
        }else{
            return false;
        }
    }
    /**
     * 设置当前model|components对应的appid
     * @date: 2016年4月12日  下午1:46:33
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public function setAppConfigId($appid){
        if(!empty($appid)){
            $this->appid = ucfirst($appid);
        }else{
            $this->appid = self::$appId;
        }
    }
    /**
     * 获得当前model|components对应的appid
     * @date: 2016年4月12日  下午6:15:28
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public function getAppConfigId(){
        if(isset($this->appid)){
            return $this->appid;
        }else{
            return self::$appId;
        }
    }
    /**
     * 设置当前model|components对应的module
     * @date: 2016年4月14日  上午11:24:43
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public function setAppModuleId($module){
        if(!empty($module)){
            $this->moduleid = ucfirst($module);
        }else{
            $this->moduleid = self::$module;
        }
    }
    /**
     * 获得当前model|components对应的module
     * @date: 2016年4月14日  上午11:26:46
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public function getAppModuleId(){
        if(isset($this->moduleid)){
            return $this->moduleid;
        }else{
            return self::$module;
        }
    }
    /**
     * 加载component
     * @date: 2016年4月7日  上午10:13:11
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public function loadComponent($class_name,$module=NULL,$appId=NULL,$singlen=TRUE){
        return BaseService::loadComponent($class_name, $module, $appId, $singlen);
    }
    /**
     * 加载model
     * @date: 2016年4月7日  上午10:15:11
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public function loadModel($class_name,$module=NULL,$appId=NULL,$singlen=TRUE){
        return BaseService::loadModel($class_name, $this->getAppModuleId(), $this->getAppConfigId(), $singlen);
    }
}