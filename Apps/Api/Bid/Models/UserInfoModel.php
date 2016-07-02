<?php
/**
* 
* ==============================================
* 版权所有 2015-2025 
* ----------------------------------------------
* 这不是一个自由软件，未经授权不许任何使用和传播。
* ==============================================
* @date: 2016年4月15日  下午4:08:35
* @author: liufeilong(alonglovehome@163.com)
* @version: 1.0.0.0
*/
class UserInfoModel extends BaseModel{
    
    /**
     * (non-PHPdoc)
     * @see BaseModel::tableName()
     */
    public function tableName(){
        return 'yaf_users';
    }
    /**
     * (non-PHPdoc)
     * @see BaseModel::setConfigKey()
     */
    public function setConfigKey(){
        return 'db_localhost';
    }
    /**
     * 函数用途描述
     * @date: 2016年4月15日  下午4:11:36
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public function getUserInfoList(){
        $sql = "select * from yaf_users";
        return $this->select($sql);
    }
}