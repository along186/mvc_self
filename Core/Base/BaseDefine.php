<?php
/**
* 基础定义文件
* ==============================================
* 版权所有 2015-2025 
* ----------------------------------------------
* 这不是一个自由软件，未经授权不许任何使用和传播。
* ==============================================
* @date: 2016年4月8日  下午4:44:44
* @author: liufeilong(alonglovehome@163.com)
* @version: 1.0.0.0
*/
define('CONFIG_SUFFIX', 'ini');
define('CONTROLLER', 'c');
define('ACTION', 'a');
define('MODULE', 'm');
define('CONTROLLER_FIX', 'Controller');
define('ACTION_FIX', 'Action');
define('COMPONENT_FIX', 'Component');
define('CHARSET', 'utf-8');
define('DBTYPE', 'mysqlPdo');
define('DB_CONFIG_KEY', 'db_config');
define('LOG_PATH', BASE_PATH.DS.'Base/Log/');
define('PHP_LOG_NAME', 'php_errors.log');
define('MYSQL_LOG_NAME', 'mysql_errors.log');
define('DEFAULT_CACHE', 'default_cache');
define('DEFAULT_TEMPLATE','smarty');
define('TEMPLATE_FIX', '.html');
define('COM', 'ComController');
define('REDISSESSION', 'base:session:');