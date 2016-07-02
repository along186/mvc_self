<?php
/**
* 
* ==============================================
* 版权所有 2015-2025 
* ----------------------------------------------
* 这不是一个自由软件，未经授权不许任何使用和传播。
* ==============================================
* @date: 2016年5月5日  上午11:11:28
* @author: liufeilong(alonglovehome@163.com)
* @version: 1.0.0.0
*/
include_once('MyStream_Compiler.class.php');
class MyStream{
    
    //当前开始位置
    protected $_pos = 0;
    
    //编译后文件内容
    protected $_data;
    
    protected $_stat;
     
    /**
     * 文件开始读取时方法
     * @date: 2016年5月5日  上午11:13:15
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public function stream_open($path, $mode, $options, &$opened_path) {
        $now_path = str_replace ( MYSTREAM . '://', '', $path ); 
        $data = MyStream_Compiler::getViewData($now_path);
        $this->_data = $data;
        $this->_stat = stat($now_path);
        return true;
    }
    
    /**
     * Included so that __FILE__ returns the appropriate info
     *
     * @return array
     */
    public function url_stat() {
        return $this->_stat;
    }
    
    /**
     * Reads from the stream.
     */
    public function stream_read($count) {
        $ret = substr ( $this->_data, $this->_pos, $count );
        $this->_pos += strlen ( $ret );
        return $ret;
    }
    
    /**
     * Tells the current position in the stream.
     */
    public function stream_tell() {
        return $this->_pos;
    }
    
    /**
     * Tells if we are at the end of the stream.
     */
    public function stream_eof() {
        return $this->_pos >= strlen ( $this->_data );
    }
    
    /**
     * Stream statistics.
     */
    public function stream_stat() {
        return $this->_stat;
    }
    
    /**
     * Seek to a specific point in the stream.
     */
    public function stream_seek($offset, $whence) {
        switch ($whence) {
            case SEEK_SET :
                if ($offset < strlen ( $this->_data ) && $offset >= 0) {
                    $this->_pos = $offset;
                    return true;
                } else {
                    return false;
                }
                break;
                 
            case SEEK_CUR :
                if ($offset >= 0) {
                    $this->_pos += $offset;
                    return true;
                } else {
                    return false;
                }
                break;
                 
            case SEEK_END :
                if (strlen ( $this->_data ) + $offset >= 0) {
                    $this->_pos = strlen ( $this->_data ) + $offset;
                    return true;
                } else {
                    return false;
                }
                break;
                 
            default :
                return false;
        }
   }
}