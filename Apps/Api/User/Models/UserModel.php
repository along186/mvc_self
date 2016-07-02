<?php
/**
* 
* ==============================================
* 版权所有 2015-2025 
* ----------------------------------------------
* 这不是一个自由软件，未经授权不许任何使用和传播。
* ==============================================
* @date: 2016年4月12日  下午5:49:03
* @author: liufeilong(alonglovehome@163.com)
* @version: 1.0.0.0
*/
class UserModel extends BaseModel{
    /**
     * (non-PHPdoc)
     * @see RDModel::tableName()
     */
    public function tableName(){
        return 'self_user_info';
    }
    
    /**
     * 函数用途描述
     * @date: 2016年4月12日  下午1:52:07
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
     */
    public function getUserInfoList(){
        $sql = "select * from self_user_info";
        return $this->select($sql,array(),3600);
    }
}