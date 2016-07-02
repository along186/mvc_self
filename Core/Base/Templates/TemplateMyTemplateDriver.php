<?php
/**
* my模板引擎适配器
* ==============================================
* 版权所有 2015-2025 
* ----------------------------------------------
* 这不是一个自由软件，未经授权不许任何使用和传播。
* ==============================================
* @date: 2016年4月20日  下午3:11:50
* @author: liufeilong(alonglovehome@163.com)
* @version: 1.0.0.0
*/
class TemplateMyTemplateDriver extends BaseView{
    
    /**
     * getTemplateHandle
     * @date: 2016年4月20日  上午11:12:12
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
     */
    public function getTemplateHandle($config_key){
        $config = BaseService::getAppConfig($config_key);
        if(empty($config['default']['template'])){
            $template = DEFAULT_TEMPLATE;
        }else{
            $template = $config['default']['template'];
        }
        $files = BaseService::getAppId();
        $template_config = BaseService::getAppConfigData($template,$files);
        if(!isset($template_config)){
            throw new BaseException($template.' config is not found in '.$files);
        }
        if($template_config['type'] !='abs'){
            $apps_config = BaseConfig::getBaseConfig();
            if($apps_config['apps_path']['type'] != 'abs'){
                $apps_path = dirname(BASE_PATH).DS.ucfirst($apps_config['apps_path']['apps']);
            }else{
                $apps_path = ucfirst($apps_config['apps_path']['path']);
            }
            $template = $apps_path.DS.Base::$appId.DS.Base::$module.DS.'Views'.DS;
            //$cache = $template.'caches';
        }else{
            $template = $template_config['templates'];
        }
        BaseService::includeClassFile(BASE_PATH.DS.'Base'.DS.'Templates/mytemplate/MyTemplate.class.php');
        $mytemplate = new MyTemplate();
        $mytemplate->_template_dir = $template;
        return $mytemplate;
    }
    /**
     * assign
     * @date: 2016年4月20日  上午11:35:43
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
     */
    public function assign($key,$value){
        $driver = BaseView::getTemplateDriver();
        return $driver->assign($key,$value);
    }
    /**
     * display
     * @date: 2016年4月20日  下午12:13:42
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
     */
    public function display($template){
        $driver = BaseView::getTemplateDriver();
        //todo缓存操作（自定义模板不带缓存，哈哈，爽歪歪）
        return $driver->display($template);
    }
    /**
     * render
     * @date: 2016年4月20日  下午12:14:35
     * @author: liufeilong(alonglovehome@163.com)
     * @version: 1.0.0.0
     */
    public function render($template){
        $driver = BaseView::getTemplateDriver();
        //todo缓存操作（自定义模板不带缓存，哈哈，爽歪歪）
        return $driver->fetch($template);
    }
}