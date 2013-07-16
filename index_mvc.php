<?php
/**
 * MVC入口文件
 * @author breezelife@163.com
 * @version v1.0 2013-07-08 
 */

require_once './config_mvc.php';  //引入mvc配置文件
require_once (_Include . 'mvcBase/index.php'); //引入mvc类库

$mvcFrame = new mvcFrame();   // 总线程

// URL分析开启网站进程
$mvcFrame -> MvcProcess = new MvcProcess();  
$mvcFrame -> MvcProcess -> ProcessStart(); 
$mvcFrame -> MvcProcess -> ControllerStart();
