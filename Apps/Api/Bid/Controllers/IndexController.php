<?php
/**
* IndexController
* ==============================================
* 版权所有 2015-2025 
* ----------------------------------------------
* 这不是一个自由软件，未经授权不许任何使用和传播。
* ==============================================
* @date: 2016年4月11日  上午10:11:26
* @author: liufeilong(alonglovehome@163.com)
* @version: 1.0.0.0
*/
class IndexController extends ComController{
    
    /**
     * indexAction
     * @date: 2016年4月11日  上午10:12:18
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public function indexAction(){   
        $_SESSION['name'] = 'liufeilong';
        $UserInfoComponent = $this->loadComponent('UserInfoComponent','User');
        $info = $UserInfoComponent->getUserInfoList();
        $this->assign('flag',1);
        $this->assign('info',$info);
        $this->assign('sessionname', $_SESSION['name']);
        $this->display();
    }
}