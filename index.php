<?php
header("Content-type: text/html; charset=utf-8");
ini_set('display_errors',1);
ini_set('date.timezone','Asia/Shanghai');
include 'Core/Core.php';
BaseService::run('Api');


