<?php
/**
* 
* ==============================================
* 版权所有 2015-2025 
* ----------------------------------------------
* 这不是一个自由软件，未经授权不许任何使用和传播。
* ==============================================
* @date: 2016年4月14日  上午11:46:22
* @author: liufeilong(alonglovehome@163.com)
* @version: 1.0.0.0
*/
class UserInfoComponent extends BaseComponent{
    
    /**
     * 函数用途描述
     * @date: 2016年4月14日  上午11:46:47
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public function getUserInfoList(){
        $UserModel = $this->loadModel('UserModel');
        $r = $UserModel->getUserInfoList();
        return $r;
    }
}