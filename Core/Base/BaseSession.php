<?php
/**
* session适配器控制类
* ==============================================
* 版权所有 2015-2025 
* ----------------------------------------------
* 这不是一个自由软件，未经授权不许任何使用和传播。
* ==============================================
* @date: 2016年5月12日  下午4:47:59
* @author: liufeilong(alonglovehome@163.com)
* @version: 1.0.0.0
*/
abstract class BaseSession{
    
    /**
     * 函数用途描述
     * @date: 2016年5月12日  下午4:48:19
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public static function getInterface($session_id=NULL){
        $config = BaseService::getAppConfigData('default',BaseService::getAppId());
        if(empty($config['session'])){
            $class_name = 'SessionFileDriver';
            $class_path = BASE_PATH.DS.'Base'.DS.'Sessions'.DS.$class_name.'.php';
            BaseService::includeClassFile($class_path);
        }else{
            $class_name = 'Session'.$config['session'].'Driver';
            $class_path = BASE_PATH.DS.'Base'.DS.'Sessions'.DS.$class_name.'.php';
            BaseService::includeClassFile($class_path);
        }
        $session = new $class_name();
        if(!empty($session_id)){
            $session->setSessionId($session_id);
        } 
        $session->init($config['session_config']);
        return $session;
    }
    /**
     * 函数用途描述
     * @date: 2016年5月16日  上午11:32:46
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public function setSessionId($session_id=NULL){
        session_id($session_id);
    }
    /**
     * 函数用途描述
     * @date: 2016年5月12日  下午5:40:38
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public function init(){
        session_set_save_handler(array($this,'open'),
                                 array($this,'close'),
                                 array($this,'read'),
                                 array($this,'write'),
                                 array($this,'destroy'),
                                 array($this,'gc'));
        @session_start();
    }
    /**
     * 函数用途描述
     * @date: 2016年5月16日  上午10:54:02
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    abstract public function open($savePath, $sessionName);
    /**
     * 函数用途描述
     * @date: 2016年5月16日  上午10:56:02
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    abstract public function close();
    
    /**
     * 函数用途描述
     * @date: 2016年5月16日  上午10:57:03
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    abstract public function write($sessionid, $data);
    /**
     * 函数用途描述
     * @date: 2016年5月16日  上午10:57:27
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    abstract public function destroy($sessionId);
    /**
     * 函数用途描述
     * @date: 2016年5月16日  上午10:58:04
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    abstract public function gc($maxlifetime);
}