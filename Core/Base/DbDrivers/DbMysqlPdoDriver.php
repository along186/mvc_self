<?php
/**
* mysqlPdoDirver操作类
* ==============================================
* 版权所有 2015-2025 
* ----------------------------------------------
* 这不是一个自由软件，未经授权不许任何使用和传播。
* ==============================================
* @date: 2016年4月11日  下午4:18:26
* @author: liufeilong(alonglovehome@163.com)
* @version: 1.0.0.0
*/
class DbMysqlPdoDriver extends BaseDatabase{
    
    /**
     * 获得数据库连接句柄
     * @date: 2016年4月11日  下午4:44:57
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public function getDbLinkHandle($config){
        $host=isset($config['host'])?$config['host']:'localhost';
        $port=isset($config['port'])?$config['port']:'3306';
        $username=isset($config['user'])?$config['user']:'root';
        $password=isset($config['password'])?$config['password']:'';
        $db_charset=isset($config['charset'])?$config['charset']:'utf8';
        $dbname=isset($config['dbname'])?$config['dbname']:'';
        //数据库长连接目前不处理
        if(empty($dbname)){
            throw new BaseException('config.dbname must be not empty!');
        }
        $PDO = new PDO("mysql:host=$host;port=$port;dbname=$dbname;","$username","$password");
        $PDO->exec("set names $db_charset");//设置字符集合
        return $PDO;
    }
    /**
     * record error
     * @date: 2016年4月14日  上午9:34:41
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    private function _setSqlError($sql,$bind,$preparesql){
        $error = array();
        $mysqlerror = $preparesql->errorInfo();
        if($mysqlerror[0] !='00000'){
            $error['msg'] = $mysqlerror[2];
            foreach ($bind as $key =>$row){
                if(!is_numeric($row)){
                    $row = "'".$row."'";
                    $sql = str_replace($key, $row, $sql);
                }else{
                    $sql = str_replace($key, $row, $sql);
                }
            }
            $error['sql'] = $sql;
            $error['time'] = date('Y-m-d H:i:s');
            BaseLog::writeMysqlLogs($error);
            return false;
        }else{
            return true;
        }
        
    }
    /**
     * insert
     * @date: 2016年4月11日  下午5:02:40
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public function insert($table_name,$fields,$config_key=NULL,$config_type='write',$appid=NULL){
        $bind = array();
        if(is_array($fields) && count($fields) > 0){
            $keys = array_keys($fields);
            $keystr = implode(',', $keys);
            $values = array_values($fields);
            $bindstr='';
            foreach ($fields as $key => $row){
               $k = ':'.$key;
               $bindstr = $bindstr.':'.$key.','; 
               $bind[$k] = $row;
            }
            $bindstr = trim($bindstr,',');
            $sql = 'INSERT INTO '.$table_name.' ('.$keystr.') VALUES('.$bindstr.')';
            $r = $this->exec($sql,$bind,$config_key,$config_type,$appid);
            if($r){
                $dblink = self::getDbLink($config_key,$config_type,$appid);
                $lastInsertId = $dblink->lastInsertId();
                if($lastInsertId){
                    return $lastInsertId;
                }else{
                    return true;
                }
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    /**
     * update
     * @date: 2016年4月11日  下午5:03:08
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public function update($table_name,$condition,$bind,$fields,$config_key=NULL,$config_type='write',$appid=NULL){
        if(isset($condition) && !empty($condition)){
            if(is_array($fields) && count($fields) > 0){
                $bindstr='';
                foreach ($fields as $key => $row){
                    $k = ':'.$key;
                    $bindstr = $bindstr.$key.'='.$k;
                    $bind[$k] = $row;
                }
                $bindstr = trim($bindstr,',');
                $sql = 'UPDATE '.$table_name.' SET '.$bindstr.' WHERE '.trim($condition);
                $r = $this->exec($sql,$bind,$config_key,$config_type,$appid);
                if($r){
                    return true;
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    /**
     * select
     * @date: 2016年4月11日  下午4:50:17
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public function select($sql,$bind=array(),$config_key=NULL,$config_type='read',$appid=NULL){
        $prepareSth = $this->prepareSql($sql,$bind,$config_key,$config_type,$appid);
        $prepareSth->execute();
        $flag = $this->_setSqlError($sql, $bind, $prepareSth);
        if($flag){
            $res = $prepareSth->fetchAll();
            return $res;
        }else{
            return false;
        } 
        
    }
    /**
     * select_one
     * @date: 2016年4月11日  下午5:02:13
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public function select_one($sql,$bind=array(),$config_key=NULL,$config_type='read',$appid=NULL){
        $prepareSth = $this->prepareSql($sql,$bind,$config_key,$config_type,$appid);
        $prepareSth->execute();
        $flag = $this->_setSqlError($sql, $bind, $prepareSth);
        if($flag){
            $res = $prepareSth->fetch(PDO::FETCH_BOTH);
            return $res[0];
        }else{
            return false;
        }    
    }
    /**
     * select_row
     * @date: 2016年4月11日  下午5:07:16
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public function select_row($sql,$bind=array(),$config_key=NULL,$config_type='read',$appid=NULL){
        $prepareSth = $this->prepareSql($sql,$bind,$config_key,$config_type,$appid);
        $prepareSth->execute();
        $flag = $this->_setSqlError($sql, $bind, $prepareSth);
        if($flag){
            $res = $prepareSth->fetch(PDO::FETCH_ASSOC);
            return $res;
        }else{
            return false;
        }
    }
    /**
     * preparesql
     * @date: 2016年4月11日  下午5:08:13
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public function prepareSql($sql,$bind,$config_key,$config_type,$appid){
        $dblink = self::getDbLink($config_key,$config_type,$appid);
        $sth = $dblink->prepare($sql);
        if(count($bind) > 0){
            $c = substr_count($sql, '?');
            if($c > 0){
                for($i=0;$i<$c;$i++){
                    $j=$i+1;
                    if(is_numeric($bind[$i])){
                        $type = PDO::PARAM_INT;
                    }else{
                        $type = PDO::PARAM_STR;
                    }
                    $sth->bindParam($j,$bind[$i],$type);
                }
            }else{
                foreach($bind as $key=>$row){
                    if(is_numeric($row)){
                        $type = PDO::PARAM_INT;
                    }else{
                        $type = PDO::PARAM_STR;
                    }
                    $sth->bindValue($key,$row,$type);
                }
            }
        }
        return $sth;
    }
   /**
    * exec
    * @date: 2016年4月13日  下午2:14:33
    * @author: liufeilong(alonglovehome@163.com)
    * @version: 1.0.0.0
   */
   public function exec($sql,$bind=array(),$config_key=NULL,$config_type=NULL,$appid=NULL){
       $prepareSth = $this->prepareSql($sql,$bind,$config_key,$config_type,$appid);
       $r = $prepareSth->execute();
       $flag = $this->_setSqlError($sql, $bind, $prepareSth);
       if($flag){
           return $r;
       }else{
           return false;
       }
       return $r;
   }
   /**
    * callProcedure
    * @date: 2016年4月14日  上午10:25:34
    * @author: liufeilong(alonglovehome@163.com)
    * @version: 1.0.0.0
   */
   public function callProcedure($procedure_name,$in_params,$bind,$config_key=NULL,$config_type='write',$appid=NULL){
       $error = array();
       if(!isset($in_params) || count($in_params) == 0){
           $error['time'] = date('Y-m-d H:i:s');
           $error['sql'] = 'call '.$procedure_name;
           $error['msg'] = 'in_params is not exist or empty';
       }
       //检查入参数据的是否都已赋值
       foreach($in_params as $row){
           $k = ':'.$row;
           if(!isset($bind[$k]) || empty($bind[$k])){
               $error['time'] = date('Y-m-d H:i:s');
               $error['sql'] = 'call '.$procedure_name;
               $error['msg'] = $row.' is must have';
               break;
           }
       }
       $bindkey = '';
       foreach($in_params as $key){
           $bindkey = $bindkey.':'.$key.',';
       }
       $bindkey = trim($bindkey,',');
       $sql = "call ".$procedure_name.'('.$bindkey.')';
       $r = $this->exec($sql,$bind.$config_key,$config_type,$appid);
       return $r;
   }
}