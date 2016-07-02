<?php
/**
* 
* ==============================================
* 版权所有 2015-2025 
* ----------------------------------------------
* 这不是一个自由软件，未经授权不许任何使用和传播。
* ==============================================
* @date: 2016年5月16日  下午12:59:19
* @author: liufeilong(alonglovehome@163.com)
* @version: 1.0.0.0
*/
class ComController extends BaseController{
    
    /**
     * 动作开始之前的操作
     * @date: 2016年5月16日  下午1:07:41
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public function beforeAction(){
        BaseService::getSession();
        return true;
    }
    
}