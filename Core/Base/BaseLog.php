<?php
/**
* 日志操作类
* ==============================================
* 版权所有 2015-2025 
* ----------------------------------------------
* 这不是一个自由软件，未经授权不许任何使用和传播。
* ==============================================
* @date: 2016年4月12日  下午5:05:22
* @author: liufeilong(alonglovehome@163.com)
* @version: 1.0.0.0
*/
abstract class BaseLog{
    
    /**
     * 获得最后一次出错日志并记录下来以备后续查错
     * @date: 2016年4月12日  下午5:09:54
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public static final function getLastErrorMsg(){
        $e = error_get_last();
        if(isset($e) && count($e) > 0){
            self::writePhpLogs($e['message'].' '.$e['file'].' '.$e['line']);
        }
    }
    /**
     * 记录PHP错误日志
     * @date: 2016年4月12日  下午5:11:30
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public static final function writePhpLogs($msg){
        $file = LOG_PATH.DS.'php'.DS.PHP_LOG_NAME;
        $fp = fopen($file, 'a');
        $msg = "[".date('Y-m-d H:i:s')."]: ".$msg." \r\n";
        fwrite($fp, $msg);
        fclose($fp);
    }
    /**
     * 记录MYSQL错误日志
     * @date: 2016年4月14日  上午9:47:54
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public static final function writeMysqlLogs($msg){
        $file = LOG_PATH.DS.'data'.DS.MYSQL_LOG_NAME;
        $fp = fopen($file, 'a');
        $msg = "time: ".$msg['time']." \r\nsql : ".$msg['sql']."\r\nerror: ".$msg['msg']."\r\n";
        fwrite($fp, $msg);
        fwrite($fp, "================================================================================ \r\n");
        fclose($fp);
    } 
}