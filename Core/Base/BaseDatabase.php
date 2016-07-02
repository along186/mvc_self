<?php
/**
* 数据库抽象层操作类
* ==============================================
* 版权所有 2015-2025 
* ----------------------------------------------
* 这不是一个自由软件，未经授权不许任何使用和传播。
* ==============================================
* @date: 2016年4月11日  下午3:59:01
* @author: liufeilong(alonglovehome@163.com)
* @version: 1.0.0.0
*/
abstract class BaseDatabase extends Base{
    
    private static $_dbDriver_arr = array();
    private static $_dbLink_arr = array();
    
    /**
     * 获得数据引擎
     * @date: 2016年4月11日  下午4:06:04
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    private static final function _getDbDriver($appid){       
        $config = BaseService::getAppConfig($appid);
        if($config['db_config']['dbtype'] == ''){
            $type = DBTYPE;
        }else{
            $type = $config['db_config']['dbtype'];
        }
        $class_name = 'Db'.ucfirst($type).'Driver';
        $dirver_path = BASE_PATH.DS.'Base'.DS.'DbDrivers'.DS.$class_name.'.php';
        BaseService::includeClassFile($dirver_path);
        $dbDriver = BaseService::newClass($class_name);   
        return $dbDriver;   
    }
    /**
     * 获得DbDriver
     * @date: 2016年4月12日  上午10:06:29
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public static final function getDbDriverHandle($config_key,$appid){
        if(!isset(self::$_dbDriver_arr[$appid][$config_key])){
            self::$_dbDriver_arr[$appid][$config_key] = self::_getDbDriver($appid);
        }
        return self::$_dbDriver_arr[$appid][$config_key];
    }
    /**
     * 获得Dblink
     * @date: 2016年4月12日  上午10:29:59
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public static final function getDbLink($config_key,$config_type,$appid){
        $db_config_id = self::_getDbLinkConfigKeyId($config_key,$config_type);
        $dbdriver = self::getDbDriverHandle($config_key,$appid);
        $db_config = BaseService::getAppConfigData($db_config_id,$appid);
        if(!isset(self::$_dbLink_arr[$appid][$db_config_id])){
            self::$_dbLink_arr[$appid][$db_config_id] = $dbdriver->getDbLinkHandle($db_config);
        }
        return self::$_dbLink_arr[$appid][$db_config_id];
    } 
    /**
     * 函数用途描述
     * @date: 2016年4月12日  上午11:19:48
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    private static function _getDbLinkConfigKeyId($config_key=NULL,$config_type=NULL){
        if(!isset($config_key)){
            $config_key = DB_CONFIG_KEY;
        }
        if(isset($config_type)){
            $config_key = $config_key.'_'.$config_type;
        }
        return $config_key;
    }
    /**
     * getDbLink 子类必须继承
     * @date: 2016年4月12日  上午10:14:20
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    abstract public function getDbLinkHandle($config); 
    /**
     * insert
     * @date: 2016年4月11日  下午4:03:57
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    abstract public function insert($table_name,$fields,$config_key=NULL,$config_type='write',$appid=NULL);
    /**
     * update
     * @date: 2016年4月11日  下午4:04:13
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    abstract public function update($table_name,$condition,$bind,$fields,$config_key=NULL,$config_type='write',$appid=NULL);
    /**
     * select
     * @date: 2016年4月11日  下午4:04:42
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    abstract public function select($sql,$bind=array(),$config_key=NULL,$config_type='read',$appid=NULL);
    /**
     * select_one
     * @date: 2016年4月11日  下午4:05:13
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    abstract public function select_one($sql,$bind=array(),$config_key=NULL,$config_type='read',$appid=NULL);
    /**
     * select_row
     * @date: 2016年4月11日  下午4:05:35
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    abstract public function select_row($sql,$bind=array(),$config_key=NULL,$config_type='read',$appid=NULL);
    /**
     * callProcedure
     * @date: 2016年4月14日  上午10:24:20
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    abstract public function callProcedure($procedure_name,$in_params,$bind,$config_key=NULL,$config_type='write',$appid=NULL);
}