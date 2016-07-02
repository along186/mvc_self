<?php
/**
* 基础控制器类
* ==============================================
* 版权所有 2015-2025 
* ----------------------------------------------
* 这不是一个自由软件，未经授权不许任何使用和传播。
* ==============================================
* @date: 2016年4月11日  上午10:08:23
* @author: liufeilong(alonglovehome@163.com)
* @version: 1.0.0.0
*/
abstract class BaseController extends Base{
    
    private $_params=array();
    public  $_assign_arr = array();
    private $_viewdriver=NULL;
    private static $_template_dir = array();
    /**
     * 动作开始之前的操作
     * @date: 2016年4月11日  下午2:06:59
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public function beforeAction(){
        return TRUE;
    }
    /**
     * 动作结束后的操作
     * @date: 2016年4月11日  下午2:07:26
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public function afterAction(){
        return TRUE;
    }
    /**
     * 函数用途描述
     * @date: 2016年4月11日  下午2:08:53
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public final function initAction($params){
        self::$controller= ucfirst($params[CONTROLLER]);
        unset($params[CONTROLLER]);
        self::$action = $params[ACTION];
        unset($params[ACTION]);
        self::$module = ucfirst($params[MODULE]);
        unset($params[MODULE]);
        unset($params['router']);
        $this->_params = $params + $_POST;
    }
    /**
     * 获得请求参数
     * @date: 2016年4月11日  下午2:22:26
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public final function getParams($key=NULL){
        if(isset($key)){
            if(array_key_exists($key, $this->_params)){
                return $this->_params[$key];
            }else{
                return false;
            }
        }else{
            return $this->_params;
        }
    }
    /**
     * display
     * @date: 2016年4月20日  上午10:12:35
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public final function display($template=NULL){
        //加载模板引擎
        if(!isset($this->_viewdriver)){
            $this->_viewdriver = $this->_getTemplateDriver();
        }
        //获得模板目录
        $template_dir = $this->_getTemplateDir();
        if(isset($template)){
            $this->_viewdriver->display(rtrim($template_dir,DS).DS.$template);
        }else{
            $template = Base::$action.TEMPLATE_FIX;
            $this->_viewdriver->display(rtrim($template_dir,DS).DS.$template);
        }
    }
    /**
     * render
     * @date: 2016年4月20日  下午1:58:05
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public final function render($template=NULL){
        //加载模板引擎
        if(!isset($this->_viewdriver)){
            $this->_viewdriver = $this->_getTemplateDriver();
        }
        //获得模板目录
        $template_dir = $this->_getTemplateDir();
        if(isset($template)){
            $render = $this->templatediver->render(rtrim($template_dir,DS).DS.$template);
        }else{
            $template = Base::$action.TEMPLATE_FIX;
            $render = $this->templatediver->render(rtrim($template_dir,DS).DS.$template);
        }
        return $render;
    }
    /**
     * assign
     * @date: 2016年4月20日  上午10:13:33
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    public function assign($key,$value){
        //加载模板引擎 
        if(!isset($this->_viewdriver)){
            $this->_viewdriver = $this->_getTemplateDriver();
        }
        $this->_viewdriver->assign($key,$value);   
    }
    /**
     * getTemplateDriver
     * @date: 2016年4月20日  上午10:20:00
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    private final function _getTemplateDriver($config_key=NULL){
        //加载视图引擎
        $viewdriver = BaseView::getViewDriver();
        return $viewdriver;
    }
    /**
     * getTemplateDir
     * @date: 2016年4月20日  下午1:26:39
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
    */
    private final function _getTemplateDir(){
        $appid = BaseService::getAppId();
        if(!isset(self::$_template_dir[$appid])){
            $apps_config = BaseConfig::getBaseConfig();
            if($apps_config['apps_path']['type'] != 'abs'){
                $apps_path = dirname(BASE_PATH).DS.ucfirst($apps_config['apps_path']['apps']);
            }else{
                $apps_path = ucfirst($apps_config['apps_path']['path']);
            }
            self::$_template_dir[$appid] = $apps_path.DS.Base::$appId.DS.Base::$module.DS.'Views'.DS;
        }
        return self::$_template_dir[$appid];   
    }
} 