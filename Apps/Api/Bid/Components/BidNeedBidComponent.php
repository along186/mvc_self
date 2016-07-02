<?php
/**
* 
* ==============================================
* 版权所有 2015-2025 
* ----------------------------------------------
* 这不是一个自由软件，未经授权不许任何使用和传播。
* ==============================================
* @date: 2016年4月14日  上午10:55:29
* @author: liufeilong(alonglovehome@163.com)
* @version: 1.0.0.0
*/
class BidNeedBidComponent extends BaseComponent{
    
    /**
     * 函数用途描述
     * @date: 2016年4月14日  上午10:56:00
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public function getBidInfo($npr_id){
        $BidNeedBidModel = $this->loadModel('BidNeedBidModel');
        $r = $BidNeedBidModel->getNeedBidInfoByNbId($npr_id);
        return $r;
    }
    /**
     * 函数用途描述
     * @date: 2016年4月15日  下午4:12:42
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public function getUserInfoList(){
        $UserInfoModel = $this->loadModel('UserInfoModel');
        $r = $UserInfoModel->getUserInfoList();
        return $r;
    }
}