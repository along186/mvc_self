<?php
/**
* 基础视图类
* ==============================================
* 版权所有 2015-2025 
* ----------------------------------------------
* 这不是一个自由软件，未经授权不许任何使用和传播。
* ==============================================
* @date: 2016年4月15日  下午4:30:32
* @author: liufeilong(alonglovehome@163.com)
* @version: 1.0.0.0
*/
abstract class BaseView extends Base{
    
    private static $_view_driver_arr = array();
    private static $_template_driver_arr = array();
    
    /**
     * newDriver
     * @date: 2016年4月20日  上午10:32:53
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    private static function _getViewDriver($config_key){
        $config = BaseService::getAppConfig($config_key);
        if(empty($config['default']['template'])){
            $template = DEFAULT_TEMPLATE;
        }
        $class_name = 'Template'.ucfirst($config['default']['template']).'Driver';
        $class_name_path = BASE_PATH.DS.'Base'.DS.'Templates'.DS.$class_name.'.php';
        BaseService::includeClassFile($class_name_path);
        $driver = BaseService::newClass($class_name);
        return $driver;
    }
    /**
     * getdriver
     * @date: 2016年4月20日  上午10:33:53
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public final static function getViewDriver($config_key=NULL){
        if(!isset($config_key)){
            $config_key = BaseService::getAppId();
        }
        if(!isset(self::$_view_driver_arr[$config_key])){
            self::$_view_driver_arr[$config_key] = self::_getViewDriver($config_key);
        }
        return self::$_view_driver_arr[$config_key];  
    }
    /**
     * 函数用途描述
     * @date: 2016年4月20日  下午2:11:29
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public final static function getTemplateDriver(){
        $config_key = BaseService::getAppId();
        if(!isset(self::$_template_driver_arr[$config_key])){
            self::$_template_driver_arr[$config_key] = self::getViewDriver($config_key)->getTemplateHandle($config_key);
        }
        return self::$_template_driver_arr[$config_key];
    }
    /**
     * getTemplateHandle
     * @date: 2016年4月20日  上午11:12:12
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    abstract public function getTemplateHandle($config_key); 
    /**
     * assign
     * @date: 2016年4月20日  上午11:08:28
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    abstract public function assign($key,$value);
    /**
     * display
     * @date: 2016年4月20日  上午11:08:42
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    abstract public function display($template);
    /**
     * render
     * @date: 2016年4月20日  上午11:09:38
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    abstract public function render($template);
    
}