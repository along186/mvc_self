<?php
/**
* SessionFileDriver
* ==============================================
* 版权所有 2015-2025 
* ----------------------------------------------
* 这不是一个自由软件，未经授权不许任何使用和传播。
* ==============================================
* @date: 2016年5月12日  下午5:24:12
* @author: liufeilong(alonglovehome@163.com)
* @version: 1.0.0.0
*/
class SessionFileDriver extends BaseSession{
    
    private $savePath;
    
    public function init($session_config){
        //1.获得session基本配置
        $config = BaseService::getAppConfigData($session_config);
        
        //2.设置session过期时间 分钟 
        session_cache_expire($config['session_cache_expire']);
        
        //3.设置session存储位置
        if($config['session_type'] == 'abs'){
            session_save_path($config['session_path']);
        }else{
            $path = BASE_PATH.DS.'Base'.DS.'Log/session';
            session_save_path($path);
        }
        
        //4.设置session在cookie中的配置信息
        $lifetime = !empty($config['session_cookie_lifetime'])?$config['session_cookie_lifetime']:ini_get('session.cookie_lifetime');
        $path     = !empty($config['session_cookie_path'])?$config['session_cookie_path']:ini_get('session.cookie_path');
        $domain   = !empty($config['session_cookie_domain'])?$config['session_cookie_domain']:ini_get('session.cookie_domain');
        $secure   = !empty($config['session_cookie_secure'])?$config['session_cookie_secure']:ini_get('session.cookie_secure');
        $httponly = !empty($config['session_cookie_httponly'])?$config['session_cookie_httponly']:ini_get('session.cookie_httponly');
        session_set_cookie_params($lifetime,$path,$domain,$secure,$httponly);
        
        //5.调用父方法
        parent::init();
    }
    /**
     * 这是自动开始会话或者通过调用 session_start() 手动开始会话 之后第一个被调用的回调函数。 此回调函数操作成功返回 TRUE，反之返回 FALSE。
     * @date: 2016年5月12日  下午5:25:47
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public function open($savePath, $sessionName){
        $this->savePath = $savePath;
        if (!is_dir($this->savePath)) {
            mkdir($this->savePath, 0777);
        }
        return true;
    }
    /**
     * close 回调函数类似于类的析构函数。 在 write 回调函数调用之后调用。 当调用 session_write_close() 函数之后，
     * 也会调用 close 回调函数。 
     * 此回调函数操作成功返回 TRUE，反之返回 FALSE。 
     * @date: 2016年5月12日  下午5:27:34
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public function close(){
        return true;
    }
    /**
     * 如果会话中有数据，read 回调函数必须返回将会话数据编码（序列化）后的字符串。 如果会话中没有数据，read 回调函数返回空字符串。
     * @date: 2016年5月12日  下午5:28:04
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public function read($sessionId){
        return (string)@file_get_contents("$this->savePath/sess_$sessionId");
    }
    /**
     * 在会话保存数据时会调用 write 回调函数。
     * @date: 2016年5月12日  下午5:29:19
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public function write($sessionId, $data){
        return file_put_contents("$this->savePath/sess_$sessionId", $data) === false ? false : true;
    }
    /**
     * 当调用 session_destroy() 函数，
     * @date: 2016年5月12日  下午5:30:32
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public function destroy($sessionId){
        $file = "$this->savePath/sess_$sessionId";
        if (file_exists($file)) {
            unlink($file);
        }
        return true;
    }
    /**
     * 为了清理会话中的旧数据，PHP 会不时的调用垃圾收集回调函数
     * @date: 2016年5月12日  下午5:31:26
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public function gc($lifetime){
        foreach (glob("$this->savePath/sess_*") as $file) {
            if (filemtime($file) + $lifetime < time() && file_exists($file)) {
                unlink($file);
            }
        } 
        return true;
    }
}