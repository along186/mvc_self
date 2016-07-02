<?php
/**
* 
* ==============================================
* 版权所有 2015-2025 
* ----------------------------------------------
* 这不是一个自由软件，未经授权不许任何使用和传播。
* ==============================================
* @date: 2016年4月12日  下午1:50:53
* @author: liufeilong(alonglovehome@163.com)
* @version: 1.0.0.0
*/
class BidNeedBidModel extends BaseModel{
    
    /**
     * (non-PHPdoc)
     * @see BaseModel->_validate
     */
    protected $_validate = array('uname'=>array('required'=>TRUE,'min'=>1,'max'=>125,'preg'=>'/^[a-z]{3,}/'));
    /**
     * (non-PHPdoc)
     * @see BaseModel::tableName()
     */
    public function tableName(){
        return 'need_bid';
    }
    /**
     * 函数用途描述
     * @date: 2016年4月12日  下午1:52:07
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public function getNeedBidInfoByNbId($npr_id){
        $sql = "select * from need_bid where npr_id = :npr_id";
        $bind = array();
        $bind[':npr_id'] = $npr_id;
        return $this->select_row($sql,$bind,30);
    }
}