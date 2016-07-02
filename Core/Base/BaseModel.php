<?php
/**
* 基础模型类
* ==============================================
* 版权所有 2015-2025 
* ----------------------------------------------
* 这不是一个自由软件，未经授权不许任何使用和传播。
* ==============================================
* @date: 2016年4月11日  上午10:09:35
* @author: liufeilong(alonglovehome@163.com)
* @version: 1.0.0.0
*/
abstract class BaseModel extends Base{
    
    protected $_validate = array();
    
    /**
     * 抽象方法子类必须继续，表示子类对应的表名
     * @date: 2016年4月12日  上午9:34:29
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    abstract public function tableName();
    /**
     * 设置数据库配置节点
     * @date: 2016年4月12日  上午9:34:04
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public function setConfigKey(){
        return DB_CONFIG_KEY;
    }
    /**
     * getConfigKey
     * @date: 2016年4月12日  上午10:45:36
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public function getConfigKey($config_key=NULL){
        if(!isset($config_key)){
            return $this->setConfigKey();
        }
        return $config_key;
    }
    /**
     * insert
     * @date: 2016年4月12日  上午9:29:30
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public final function insert($feilds,$config_key=NULL,$config_type='write'){
        $r = $this->_beforeInsert($feilds);
        if($r['code'] != 0){
            return $r;
        }
        $dbDriver = $this->getDbDriver($this->getConfigKey($config_key));
        $r = $dbDriver->insert($this->tableName(),$feilds,$this->getConfigKey($config_key),$config_type,$this->getAppConfigId());
        $this->_afterInsert($feilds);
        return $r;
        
    }
    /**
     * beforeInsert
     * @date: 2016年4月13日  下午1:24:56
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    private final function _beforeInsert($fields){
        //进行插入检查
        if(isset($this->_validate) && count($this->_validate) > 0){
            foreach($this->_validate as $key => $row){
                if($row['required'] == TRUE){
                    if(isset($fields[$key])){
                        if(isset($row['preg']) && !empty($row['preg'])){
                             if(!preg_match($row['preg'], $fields[$key])){
                                 return array('code'=>1,'msg'=>$key.' is not preg validate');
                             }
                        }else{
                            $c = strlen($fields[$key]);
                            if($c > $row['max'] || $c < $row['min']){
                                return array('code'=>1,'msg'=>$key.' leng is not validate');
                            }
                        }
                    }else{
                        return array('code'=>1,'msg'=>$key.' is must have');
                    }   
                }
            }
        }
        return array('code'=>0,'msg'=>'success');
    }
    /**
     * afterInsert
     * @date: 2016年4月13日  下午1:25:33
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    private final function _afterInsert(){
        //todo数据已经插入，检查无意义，只好进行记录了
        return true;
    }
    /**
     * update
     * @date: 2016年4月12日  上午9:30:15
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public final function update($cond,$bind,$fields,$config_key=NULL,$config_type='read'){
        $r = $this->_beforeUpdate($fields);
        if($r['code'] != 0){
            return $r;
        }
        $dbDriver = $this->getDbDriver($this->getConfigKey($config_key));
        $r = $dbDriver->update($this->tableName(),$cond,$bind,$fields,$this->getConfigKey($config_key),$config_type,$this->getAppConfigId());
        $this->_afterUpdate($fields);
        return $r;
    }
    /**
     * beforeUpdate
     * @date: 2016年4月13日  下午5:42:09
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    private final function _beforeUpdate($fields){
        //进行更新检查
        if(isset($this->_validate) && count($this->_validate) > 0){
            foreach($this->_validate as $key => $row){
                if($row['required'] == TRUE){
                    if(isset($fields[$key])){
                        if(isset($row['preg']) && !empty($row['preg'])){
                            if(!preg_match($row['preg'], $fields[$key])){
                                return array('code'=>1,'msg'=>$key.' is not preg validate');
                            }
                        }else{
                            $c = strlen($fields[$key]);
                            if($c > $row['max'] || $c < $row['min']){
                                return array('code'=>1,'msg'=>$key.' leng is not validate');
                            }
                        }
                    }else{
                        return array('code'=>1,'msg'=>$key.' is must have');
                    }
                }
            }
        }
        return array('code'=>0,'msg'=>'success');
    }
    /**
     * afterUpdate
     * @date: 2016年4月13日  下午5:43:15
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    private final function _afterUpdate($fields){
        //todo数据已经更新，检查无意义，只好进行记录了
        return true;
    }
    /**
     * select
     * @date: 2016年4月12日  上午9:30:43
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public final function select($sql,$bind=array(),$cache_time=0,$config_key=NULL,$config_type='read'){
        if($cache_time !=0){//如果设置了缓存时间
            $cachedriver = BaseService::getCacheDriver($this->getConfigKey($config_key),$this->getAppConfigId());
            $cache_key = $this->getCacheKey($sql,$bind);
            $data = $cachedriver->get($cache_key,$this->getConfigKey($config_key),$this->getAppConfigId());
            if($data){//存在，直接取出
                return json_decode($data,true);
            }else{//不存在，从数据库取出，然后缓存起来
                $dbDriver = $this->getDbDriver($config_key);
                $data = $dbDriver->select($sql,$bind,$this->getConfigKey($config_key),$config_type,$this->getAppConfigId());
                $jsondata = json_encode($data);
                $cachedriver->set($cache_key,$jsondata,$cache_time,$this->getConfigKey($config_key),$this->getAppConfigId());
                return $data;
            }
        }else{//如果没有设置缓存时间
            $dbDriver = $this->getDbDriver($this->getConfigKey($config_key));
            $r = $dbDriver->select($sql,$bind,$this->getConfigKey($config_key),$config_type,$this->getAppConfigId());
            return $r;
        }
    }
    /**
     * select_one
     * @date: 2016年4月12日  上午9:31:02
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public final function select_one($sql,$bind=array(),$cache_time=0,$config_key=NULL,$config_type='read'){
        if($cache_time !=0){//如果设置了缓存时间
            $cachedriver = BaseService::getCacheDriver($this->getConfigKey($config_key),$this->getAppConfigId());
            $cache_key = $this->getCacheKey($sql,$bind);
            $data = $cachedriver->get($cache_key,$this->getConfigKey($config_key),$this->getAppConfigId());
            if($data){//存在，直接取出
                return json_decode($data,true);
            }else{//不存在，从数据库取出，然后缓存起来
                $dbDriver = $this->getDbDriver($this->getConfigKey($config_key));
                $data = $dbDriver->select_one($sql,$bind,$this->getConfigKey($config_key),$config_type,$this->getAppConfigId());
                $jsondata = json_encode($data);
                $cachedriver->set($cache_key,$jsondata,$cache_time,$this->getConfigKey($config_key),$this->getAppConfigId());
                return $data;
            }
        }else{//如果没有设置缓存时间
            $dbDriver = $this->getDbDriver($config_key);
            $r = $dbDriver->select_one($sql,$bind,$this->getConfigKey($config_key),$config_type,$this->getAppConfigId());
            return $r;
        }
    }
    /**
     * select_row
     * @date: 2016年4月12日  上午9:31:27
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public final function select_row($sql,$bind=array(),$cache_time=0,$config_key=NULL,$config_type='read'){
        if($cache_time !=0){//如果设置了缓存时间
            $cachedriver = BaseService::getCacheDriver($this->getConfigKey($config_key),$this->getAppConfigId());
            $cache_key = $this->getCacheKey($sql,$bind);
            $data = $cachedriver->get($cache_key,$this->getConfigKey($config_key),$this->getAppConfigId());
            if($data){//存在，直接取出
                return json_decode($data,true);
            }else{//不存在，从数据库取出，然后缓存起来
                $dbDriver = $this->getDbDriver($this->getConfigKey($config_key));
                $data = $dbDriver->select_row($sql,$bind,$this->getConfigKey(),$config_type,$this->getAppConfigId());
                $jsondata = json_encode($data);
                $cachedriver->set($cache_key,$jsondata,$cache_time,$this->getConfigKey($config_key),$this->getAppConfigId());
                return $data;
            }
        }else{//如果没有设置缓存时间
            $dbDriver = $this->getDbDriver($config_key);
            $r = $dbDriver->select_row($sql,$bind,$this->getConfigKey($config_key),$config_type,$this->getAppConfigId());
            return $r;
        }
    }
    /**
     * getDbdriver
     * @date: 2016年4月12日  上午9:54:50
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    private final function getDbDriver($config_key=NULL){
        $config_key = $this->getConfigKey($config_key);
        $dbDriver = BaseDatabase::getDbDriverHandle($config_key,$this->getAppConfigId());
        return $dbDriver;  
    }
    /**
     * callProcedure(目前没有测试过,使用请测试)
     * @date: 2016年4月14日  上午10:19:11
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public final function callProcedure($procedure_name,$in_params,$bind,$config_key=NULL,$config_type='write'){
        $dbDriver = $this->getDbDriver($config_key);
        $r = $dbDriver->callProcedure($procedure_name,$in_params,$bind,$this->getConfigKey($config_key),$config_type,$this->getAppConfigId());
        return $r;
    }
    /**
     * 获得缓存key
     * @date: 2016年4月15日  下午3:30:59
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public final function getCacheKey($sql,$bind=array()){
        if(isset($bind) && count($bind)> 0){
            foreach($bind as $key =>$row){
                $sql = str_replace($key,$row,$sql);   
            }
            $cache_key = BaseService::baseMd5(strtolower($sql));
        }else{
            $cache_key = BaseService::baseMd5(strtolower($sql));
        }
        return $cache_key;
    }
}