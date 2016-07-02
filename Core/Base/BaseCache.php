<?php
/**
* 基础缓存抽象层
* ==============================================
* 版权所有 2015-2025 
* ----------------------------------------------
* 这不是一个自由软件，未经授权不许任何使用和传播。
* ==============================================
* @date: 2016年4月14日  上午11:56:50
* @author: liufeilong(alonglovehome@163.com)
* @version: 1.0.0.0
*/
abstract class BaseCache extends Base{
    
    private static $_cache_driver_list = array();
    private static $_cache_link_list = array();
    /**
     * 获得缓存驱动
     * @date: 2016年4月14日  下午1:13:56
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public static final function getCacheDriver($config_key=NULL,$appid=NULL){
        if(!isset($appid)){
            $appid = BaseService::getAppId();
        }
        if(!isset($config_key)){
            $config = BaseService::getAppConfig($appid);
            if(!isset($config['default']['cache'])){
                $config_key = DEFAULT_CACHE;
            }else{
                $config_key = $config['default']['cache'];
            }
        }
        $config = BaseService::getAppConfigData($config_key,$appid);
        if(isset($config) && count($config) > 0){
            if(!isset($config['cachetype'])){
                return false;
            }else{
                $class_name = 'Cache'.ucfirst($config['cachetype']).'Driver';
                if(!isset(self::$_cache_driver_list[$class_name])){
                    $class_name_file = BASE_PATH.DS.'Base'.DS.'Caches'.DS.$class_name.'.php';
                    BaseService::includeClassFile($class_name_file);
                    self::$_cache_driver_list[$class_name] = BaseService::newClass($class_name); 
                }
                return self::$_cache_driver_list[$class_name];
            }
        }else{
            return false;
        }
        
    }
    /**
     * 获得link
     * @date: 2016年4月14日  下午2:00:18
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public static final function getCacheLink($config_key=NULL,$appid=NULL){
        if(!isset($appid)){
            $appid = BaseService::getAppId();
        }
        if(!isset($config_key)){
            $config = BaseService::getAppConfig($appid);
            if(!isset($config['default']['cache'])){
                $config_key = DEFAULT_CACHE;
            }else{
                $config_key = $config['default']['cache'];
            }
        }
        if(!isset(self::$_cache_link_list[$appid][$config_key])){
            $driver = self::getCacheDriver($config_key,$appid);
            $config = BaseService::getAppConfigData($config_key,$appid);
            $link = $driver->getCacheLinkHandle($config);
            if($link){
                self::$_cache_link_list[$appid][$config_key] = $link;
            }else{
                return false;
            }
        }
        return self::$_cache_link_list[$appid][$config_key];
        
    } 
    /**
     * getLink
     * @date: 2016年4月14日  下午1:17:40
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    abstract public function getCacheLinkHandle($config);
    /**
     * get
     * @date: 2016年4月14日  下午1:18:59
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    abstract public function get($key,$config_key=NULL,$appid=NULL);
    /**
     * set
     * @date: 2016年4月14日  下午1:19:58
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    abstract public function set($key,$value,$cache_time=NULL,$config_key=NULL,$appid=NULL);
    /**
     * flushAll
     * @date: 2016年4月14日  下午1:20:30
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    abstract public function flushAll($key=NULL,$config_key=NULL,$appid=NULL);
}