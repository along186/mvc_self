<?php
/**
* 基础定义类
* ==============================================
* 版权所有 2015-2025 
* ----------------------------------------------
* 这不是一个自由软件，未经授权不许任何使用和传播。
* ==============================================
* @date: 2016年4月7日  上午9:56:15
* @author: liufeilong(alonglovehome@163.com)
* @version: 1.0.0.0
*/
if(!defined('BASE_PATH')){
    if(PHP_VERSION > 5.3){
        define('BASE_PATH', __DIR__);
    }else{
        define('BASE_PATH', dirname(__FILE__));
    }
    define('DEBUG', TRUE);
    define('DS','/');
    //引入基础定义类
    require(BASE_PATH.DS.'Base'.DS.'BaseDefine.php');
    
    //引入基础类文件
    require(BASE_PATH.DS.'Base'.DS.'Base.php');
    
    //引入基础配置类文件
    require(BASE_PATH.DS.'Base'.DS.'BaseConfig.php');
    
    //引入基础路由类文件
    require(BASE_PATH.DS.'Base'.DS.'BaseRouter.php');
    
    //引入基础控制器
    require(BASE_PATH.DS.'Base'.DS.'BaseController.php');
    
    //引入基础数据库操作类
    require(BASE_PATH.DS.'Base'.DS.'BaseDatabase.php');
    
    //引入基础模型类
    require(BASE_PATH.DS.'Base'.DS.'BaseModel.php');
    
    //引入基础日志类
    require(BASE_PATH.DS.'Base'.DS.'BaseLog.php');
    
    //引入异常处理类
    require(BASE_PATH.DS.'Base'.DS.'BaseException.php');
    
    //引入基础服务类
    require(BASE_PATH.DS.'Base'.DS.'BaseComponent.php');
    
    //引入基础视图类
    require(BASE_PATH.DS.'Base'.DS.'BaseView.php');
    
    //引入基础缓存类
    require(BASE_PATH.DS.'Base'.DS.'BaseCache.php');
    
    //引入共用方法文件
    require(BASE_PATH.DS.'Base'.DS.'Functions/Functions.php');
    
    //引入驱动类文件
    require(BASE_PATH.DS.'Base'.DS.'BaseService.php');
}