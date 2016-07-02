<?php
/**
* MyTemplate异常处理类
* ==============================================
* 版权所有 2015-2025 
* ----------------------------------------------
* 这不是一个自由软件，未经授权不许任何使用和传播。
* ==============================================
* @date: 2016年4月25日  下午1:49:01
* @author: liufeilong(alonglovehome@163.com)
* @version: 1.0.0.0
*/
class MyTemplate_Exception extends Exception{

    /**
     * (non-PHPdoc)
     * @see Exception::__toString()
     */
    public function __toString(){
        $r='<pre>'."\r\n";
        $r.='Exception: '."\r\n".'Message: '. $this->getMessage () . ''."\r\n".'File: ' . $this->getFile () . ''."\r\n".'Line: '. $this->getLine () . ''."\r\n".'Trace: ' ;
        $r.="\r\n";
        $r.=$this->getTraceAsString();
        $r.="\r\n".'</pre>';
        return $r;
    }
}