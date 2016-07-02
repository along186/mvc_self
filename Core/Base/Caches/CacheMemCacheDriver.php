<?php
/**
* memcache缓存类
* ==============================================
* 版权所有 2015-2025 
* ----------------------------------------------
* 这不是一个自由软件，未经授权不许任何使用和传播。
* ==============================================
* @date: 2016年4月14日  下午3:41:00
* @author: liufeilong(alonglovehome@163.com)
* @version: 1.0.0.0
*/
class CacheMemCacheDriver extends BaseCache{
    
    /**
     * getLink
     * @date: 2016年4月14日  下午1:38:30
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
     */
    public function getCacheLinkHandle($config){
        $Memcache = new Memcache();
        $host = !empty($config['host'])?$config['host']:'127.0.0.1';
        $port = !empty($config['port'])?$config['port']:'11211';
        $link = $Memcache->connect($host,$port);
        if($link){
            return $Memcache;
        }else{
            return false;
        }
    }
    /**
     * get
     * @date: 2016年4月14日  下午1:38:40
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
     */
    public function get($key,$config_key=NULL,$appid=NULL){
        $cachelink = self::getCacheLink($config_key,$appid);
        $r = $cachelink->get($key);
        return $r;
    }
    /**
     * set
     * @date: 2016年4月14日  下午1:38:45
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
     */
    public function set($key,$value,$cache_time=NULL,$config_key=NULL,$appid=NULL){
        $cachelink = self::getCacheLink($config_key,$appid);
        if(isset($cache_time) && is_numeric($cache_time) && $cache_time > 0){
            $r = $cachelink->setex($key,$cache_time,$value);
        }else{
            $r = $cachelink->set($key,$value);
        }
        return $r;
    }
    /**
     * flushAll
     * @date: 2016年4月14日  下午1:38:48
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
     */
    public function flushAll($key=NULL,$config_key=NULL,$appid=NULL){
        $cachelink = self::getCacheLink($config_key,$appid);
        if(isset($key)){
            $r = $cachelink->delete($key);
        }else{
            $r = $cachelink->flush();
        }
        return $r;
    }
    /**
     * add
     * @date: 2016年4月14日  下午1:38:53
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
     */
    public function add($key,$value,$flag=FALSE,$expire=0,$config_key=NULL,$appid=NULL){
        $cachelink = self::getCacheLink($config_key,$appid);
        $r = $cachelink->add($key,$value,$flag,$expire);
        return $r;
    }
    /**
     * close
     * @date: 2016年4月14日  下午3:51:17
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public function close($config_key=NULL,$appid=NULL){
        $cachelink = self::getCacheLink($config_key,$appid);
        $cachelink->close();
    }
    
}