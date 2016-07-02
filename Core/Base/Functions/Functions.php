<?php
register_shutdown_function(array('BaseLog','getLastErrorMsg'));//加载日志记录
