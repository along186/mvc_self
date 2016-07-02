<?php
/**
* redis缓存类
* ==============================================
* 版权所有 2015-2025 
* ----------------------------------------------
* 这不是一个自由软件，未经授权不许任何使用和传播。
* ==============================================
* @date: 2016年4月14日  下午1:35:52
* @author: liufeilong(alonglovehome@163.com)
* @version: 1.0.0.0
*/
class CacheRedisDriver extends BaseCache{
    
    /**
     * getLink
     * @date: 2016年4月14日  下午1:38:30
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public function getCacheLinkHandle($config){
        $redis = new Redis();
        $host = !empty($config['cache_host'])?$config['cache_host']:'127.0.0.1';
        $port = !empty($config['cache_port'])?$config['cache_port']:'6379';
        $link = $redis->connect($host,$port);
        if($link){
            return $redis;
        }else{
            throw new BaseException('redis cannot link');
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
            $r = $cachelink->flushAll();
        }
        return $r;
    }
    /**
     * exist
     * @date: 2016年4月14日  下午1:38:53
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public function exist($key,$config_key=NULL,$appid=NULL){
        $cachelink = self::getCacheLink($config_key,$appid);
        $r = $cachelink->exists($key);
        return $r;
    }
    /**
     * lPush
     * @date: 2016年4月14日  下午1:38:57
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public function lPush($key,$value,$config_key=NULL,$appid=NULL){
        $cachelink = self::getCacheLink($config_key,$appid);
        $r = $cachelink->lPush($key,$value);
        return $r;
        
    }
    /**
     * rPush
     * @date: 2016年4月14日  下午1:39:00
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public function rPush($key,$value,$config_key=NULL,$appid=NULL){
        $cachelink = self::getCacheLink($config_key,$appid);
        $r = $cachelink->lPush($key,$value);
        return $r;
    }
    /**
     * lPop
     * @date: 2016年4月14日  下午1:39:08
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public function lPop($key,$config_key=NULL,$appid=NULL){
        $cachelink = self::getCacheLink($config_key,$appid);
        $r = $cachelink->lPop($key);
        return $r;
    }
    /**
     * rPop
     * @date: 2016年4月14日  下午1:39:11
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public function rPop($key,$config_key=NULL,$appid=NULL){
        $cachelink = self::getCacheLink($config_key,$appid);
        $r = $cachelink->rPop($key);
        return $r;
    }
    /**
     * lSize
     * @date: 2016年4月14日  下午1:39:15
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public function lSize($key,$config_key=NULL,$appid=NULL){
        $cachelink = self::getCacheLink($config_key,$appid);
        $r = $cachelink->lSize($key);
        return $r;
    }
    /**
     * lRange
     * @date: 2016年4月14日  下午1:39:19
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public function lRange($key,$start,$end,$config_key=NULL,$appid=NULL){
        $cachelink = self::getCacheLink($config_key,$appid);
        $r = $cachelink->lRange($key,$start,$end);
        return $r;
    }
}