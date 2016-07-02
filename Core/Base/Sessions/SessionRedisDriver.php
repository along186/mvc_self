<?php
/**
* SessionRedisDriver
* ==============================================
* 版权所有 2015-2025 
* ----------------------------------------------
* 这不是一个自由软件，未经授权不许任何使用和传播。
* ==============================================
* @date: 2016年5月16日  下午2:32:58
* @author: liufeilong(alonglovehome@163.com)
* @version: 1.0.0.0
*/
class SessionRedisDriver extends BaseSession{
    
    private $_cache_key = NULL;
    private $_timeout = NULL;
    private $_config_key = NULL;
    private $_cache = NULL;
    
    public function init($session_config){
        //1.获得session基本配置
        $config = BaseService::getAppConfigData($session_config);
        $this->_config_key = $session_config;
        $this->_cache_key = !empty($config['cache_key'])?$config['cache_key']:REDISSESSION;
        //2.设置session过期时间 分钟
        session_cache_expire($config['session_cache_expire']);
        
        //3.设置session在cookie中的配置信息
        $lifetime = !empty($config['session_cookie_lifetime'])?$config['session_cookie_lifetime']:ini_get('session.cookie_lifetime');
        $this->_timeout = $lifetime*60;
        $path     = !empty($config['session_cookie_path'])?$config['session_cookie_path']:ini_get('session.cookie_path');
        $domain   = !empty($config['session_cookie_domain'])?$config['session_cookie_domain']:ini_get('session.cookie_domain');
        $secure   = !empty($config['session_cookie_secure'])?$config['session_cookie_secure']:ini_get('session.cookie_secure');
        $httponly = !empty($config['session_cookie_httponly'])?$config['session_cookie_httponly']:ini_get('session.cookie_httponly');
        session_set_cookie_params($lifetime,$path,$domain,$secure,$httponly);
    
        //4.获得cacheDriver
        $this->_cache = BaseService::getCacheDriver($this->_config_key);

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
        $key = $this->_cache_key.$sessionId;
        $data = $this->_cache->get($key,$this->_config_key);
        if($data){
            return (string)$data;
        }else{
            return '';
        }
    }
    /**
     * 在会话保存数据时会调用 write 回调函数。
     * @date: 2016年5月12日  下午5:29:19
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
     */
    public function write($sessionId, $data){
        $key = $this->_cache_key.$sessionId;
        //$data = addslashes($data);
        $flag = $this->_cache->set($key,$data,$this->_timeout,$this->_config_key);
        return (bool)$flag;
    }
    /**
     * 当调用 session_destroy() 函数，
     * @date: 2016年5月12日  下午5:30:32
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
     */
    public function destroy($sessionId){
        $key = $this->_cache_key.$sessionId;
        $flag = $this->_cache->flushAll($key,$this->_config_key);
        return true;
    }
    /**
     * 为了清理会话中的旧数据，PHP 会不时的调用垃圾收集回调函数
     * @date: 2016年5月12日  下午5:31:26
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
     */
    public function gc($lifetime){
        return true;
    }
}