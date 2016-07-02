<?php
/**
* 基础路由类
* ==============================================
* 版权所有 2015-2025 
* ----------------------------------------------
* 这不是一个自由软件，未经授权不许任何使用和传播。
* ==============================================
* @date: 2016年4月7日  下午1:51:49
* @author: liufeilong(alonglovehome@163.com)
* @version: 1.0.0.0
*/
abstract class BaseRouter{
    
    /**
     * 路由分发
     * @date: 2016年4月7日  下午1:55:03
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public static function &getUrlRequest(){
       $request = array();
       $request = self::_getData();
       //判断url模式(0:普通模式  1:pathinfo模式  2:rewrite重写模式  3:命令行模式)
       $type = self::_getUrlType();
       $r_params = array();
       $request = &$_GET;
       switch ($type){
           case 0:
               $r_params = self::_getNormalUrlParams();
               break;
           case 1:
               $r_params = self::_getPathinfoUrlParams();
               break;
           case 2:
               $r_params = self::_getRewriteUrlParams();
               break;
           case 3:
               $r_params = self::_getCliUrlParams();
               break;
           default:
               $r_params = self::_getNormalUrlParams();
               break;
       }
       $request+=$r_params;
       if(!empty($request['router'])){
           $router_arr = explode('/', $request['router']);
           $count = count($router_arr);
           $default = BaseService::getAppConfigData('default');
           if($count == 0){
               $request[MODULE] = $default['module'];
               $request[CONTROLLER] = $default['controller'];
               $request[ACTION] = $router_arr['action'];
           }elseif($count == 1){
               $request[MODULE] = $default['module'];
               $request[CONTROLLER] = $default['controller'];
               $request[ACTION] = $router_arr[0];
           }elseif($count == 2){
               $request[MODULE] = $default['module'];
               $request[CONTROLLER] = $router_arr[0];
               $request[ACTION] = $router_arr[1];    
           }else{
               $request[MODULE] = $router_arr[0];
               $request[CONTROLLER] = $router_arr[1];
               $request[ACTION] = $router_arr[2];
           } 
       }else{
           $default = BaseService::getAppConfigData('default');
           if(!isset($request[MODULE])){
               $request[MODULE] = $default['module'];
           }
           if(!isset($request[CONTROLLER])){
               $request[CONTROLLER] = $default['controller'];
           }
           if(!isset($request[ACTION])){
               $request[ACTION] = $default['action'];
           }
       }
       return $request;
    }
    /**
     * 获得普通模式下url信息
     * @date: 2016年4月8日  下午4:10:36
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    private static function _getNormalUrlParams(){
        $query_string = trim($_SERVER['QUERY_STRING'],DS);
        $script_name = trim($_SERVER['SCRIPT_NAME'],DS);
        $pathinfo = str_replace($script_name, '', $query_string);
        $params = array();
        $params = self::_getUrlParams($pathinfo);
        return $params;
        
    }
    /**
     * 获得pathinfo模式下url信息
     * @date: 2016年4月8日  下午4:11:06
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
     private static function _getPathinfoUrlParams(){
         $request_uri = preg_replace('$[/\\\\]{2,}$', '/', trim($_SERVER['REQUEST_URI'],DS));
         $script_name = trim($_SERVER['SCRIPT_NAME'],DS);
         $pathinfo = str_replace($script_name, '', $request_uri);
         $params = array();
         $params = self::_getUrlParams($pathinfo);
         return $params;
     }
     /**
      * 获得rewrite模式下url信息
      * @date: 2016年4月8日  下午4:12:14
      * @author: liufeilong(alonglovehome@163.com)
      * @version: 1.0.0.0
     */
     private static function _getRewriteUrlParams(){
         $request_uri = preg_replace('$[/\\\\]{2,}$', '/', trim($_SERVER['REQUEST_URI'],DS));
         $script_name = trim($_SERVER['SCRIPT_NAME'],DS);
         $pathinfo = str_replace($script_name, '', $request_uri);
         $params = array();
         $params = self::_getUrlParams($pathinfo);
         return $params;
     }
     /**
      * 获得cli模式下url信息
      * @date: 2016年4月8日  下午4:13:33
      * @author: liufeilong(alonglovehome@163.com)
      * @version: 1.0.0.0
     */
     private static function _getCliUrlParams(){
         if(!isset($_SERVER['argv'])){
             $pathinfo = trim($_SERVER['argv'],DS);
         }
         $params = array();
         $params = self::_getUrlParams($pathinfo);
         return $params;
     }
     /**
      * 函数用途描述
      * @date: 2016年4月8日  下午4:28:37
      * @author: liufeilong(alonglovehome@163.com)
      * @version: 1.0.0.0
     */
     private static function _getUrlParams($pathinfo){
         $params = array();
         $pathinfo = trim($pathinfo,DS);
         $c = preg_match('/^(([a-z0-9\_\-]+\/?){3})(([a-z0-9\_\-]+\/?)*)/i', $pathinfo,$r);
         $router='';
         $parameter='';
         if($c){
             $router = trim($r[1],DS);
             $parameter = trim($r[3],DS);
         }
         if(!empty($parameter)){
             $parameter_arr = explode('/', $parameter);
             $count = count($parameter_arr);
             for($i=0;$i<$count;$i+=2){
                 $j = $i+1;
                 if($j == $count){
                     $params[$parameter_arr[$i]] = '';
                 }else{
                     $params[$parameter_arr[$i]] = $parameter_arr[$j];
                 }             
             }
         }
         $params['router'] = $router;
         return $params;
     }
    /**
     * 判断当前url模式
     * @date: 2016年4月8日  下午3:43:23
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
     private static function _getUrlType(){
        if(isset($_SERVER['argv'])){
            $type = 3;
        }else{
            $request_uri = trim($_SERVER['REQUEST_URI'],DS);
            if(empty($request_uri)){
                $type = 1;
            }else{
                $script_name = trim($_SERVER['SCRIPT_NAME'],DS);
                if(strpos($request_uri, $script_name) === FALSE){
                    $type = 2;
                }else{
                    if(isset($_SERVER['PATH_INFO'])){
                        $type = 1;
                    }else{
                        $type = 0;
                    }
                }
            }  
        }
        return $type;
    }
    /**
     * 获得基本路由信息
     * @date: 2016年4月8日  下午3:29:15
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    private static function _getData(){
        $data = array();
        $data['scheme'] = (!empty($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) == 'on'))? 'https':'http';
        $data['host'] = !empty($_SERVER['HTTP_HOST'])? $_SERVER['HTTP_HOST']:'';
        $data['port'] = $_SERVER['SERVER_PORT'];
        return $data;
    }
}