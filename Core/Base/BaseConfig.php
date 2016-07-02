<?php
/**
* 基础配置操作类
* ==============================================
* 版权所有 2015-2025 
* ----------------------------------------------
* 这不是一个自由软件，未经授权不许任何使用和传播。
* ==============================================
* @date: 2016年4月7日  上午10:35:13
* @author: liufeilong(alonglovehome@163.com)
* @version: 1.0.0.0
*/
abstract class BaseConfig{
    
    private static $_config_data = array();
    
    /**
     * 获Base.ini配置信息
     * @date: 2016年4月7日  上午10:37:18
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public static final function getBaseConfig(){
        if(!isset(self::$_config_data['Base'])){
            $file_path = self::_getConfigFilePath('Base');
            self::$_config_data['Base'] = self::_getConfigData($file_path);
        }
        return self::$_config_data['Base'];
    }
    /**
     * 获得Base配置中指定节点数据
     * @date: 2016年4月7日  下午1:10:24
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public static final function getBaseConfigData($node){
        $config = self::getBaseConfig();
        return $config[$node];
    }
    /**
     * 获得appId配置信息
     * @date: 2016年4月7日  上午10:56:57
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public static final function getAppConfig($files){
        if(!isset(self::$_config_data[$files])){
            $file_path = self::_getConfigFilePath($files);
            self::$_config_data[$files] = self::_getConfigData($file_path);
        }
        return self::$_config_data[$files];
    }
    /**
     * 获得app配置中指定节点数据
     * @date: 2016年4月7日  下午1:13:03
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public static final function getAppConfigData($node,$files){
        $config = self::getAppConfig($files);
        if(!isset($config[$node]) || empty($config[$node])){
            $config[$node] = NULL;
        }
        return $config[$node];
    }
    
    /**
     * 获得配置文件路径
     * @date: 2016年4月7日  上午11:01:16
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    private static final function _getConfigFilePath($files){
        $file_path= BASE_PATH.DS.'Config';
        $pos = strpos($files,'.'); 
        if($pos > 0){
            $files_arr = explode('.',$files);
            $count = count($files_arr);
            for($i=0;$i<$count;$i++){
                $j=$i+1;
                if($j == $count){
                    $file_path = $file_path.DS.$files_arr[$i].'.'.CONFIG_SUFFIX;
                }else{
                   $file_path = $file_path.DS.ucfirst($files_arr[$i]);
                }
            }
        }else{
            $file_path = $file_path.DS.ucfirst($files).'.'.CONFIG_SUFFIX;
        }
        return $file_path;
    }
    /**
     * 获得base配置文件路径
     * @date: 2016年4月7日  上午11:57:54
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    private static final function _getBaseConfilePath(){
        return BASE_PATH.DS.'Config'.DS.'Base.'.CONFIG_SUFFIX;
    }
    /**
     * 获得配置信息
     * @date: 2016年4月7日  上午10:49:05
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    private static final function _getConfigData($file_path){
       return parse_ini_file($file_path,TRUE);
    }
}