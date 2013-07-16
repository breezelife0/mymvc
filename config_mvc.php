<?php

// MVC框架配置 *****************************************

define('_Wuyou', str_replace("\\", '/', dirname(__FILE__) ).'/' );
define('_Webroot', str_replace("\\", '/', substr(_Wuyou, 0,-7) ).'/');

// 兼容原系统配置文件 *****************************************
require_once(_Wuyou.'config.php');


define ('_Controller', _Wuyou . 'controller/');		// 控制器目录
define ('_Model', _Wuyou . 'model/');				// 模型目录
define ('_View', _Wuyou . 'templates/');				// 视图模板目录
define ('_Include', _Wuyou . 'include/');			// 库目录
//define ('_Class', _Wuyou . 'class/');				// 对象类目录
define ('_Class', _Wuyou . '/');				    // 对象类目录
define ('_PathTag', '/');				 	    	// 载入下级目录文件标识 例如: 需载入 Model/user/vip.php 即使用 _mode('user/vip');

define ('_Host', (empty($_SERVER["HTTPS"]) || $_SERVER['HTTPS'] == 'off' ? 'http://' : 'https://') . $_SERVER['HTTP_HOST']);// 主机网址
define ('_Http', _Host . str_ireplace('/index.php', '', $_SERVER['SCRIPT_NAME']) . '/');	// 网站根目录网址

define('V_WUYOU', _Host.'/wuyou/'); //v means visit or view~ 
define('V_INDEX', V_WUYOU.'index_mvc.php'); 
define('V_TPL', V_WUYOU.'templates/');

//一些其他配置 *****************************************
define('_Shtml',_Webroot.'shtml/' );
define('V_shtml',_Host.'/shtml/' );




//数据库db配置 *****************************************

global $mvc_config;

$mvc_config['db']['dbhost'] = $dbhost;
$mvc_config['db']['dbuser'] = $dbusername;
$mvc_config['db']['dbpwd'] = $dbuserpwd;
/*
测试机
$mvc_config['db']['dbhost'] = '192.168.2.115';
$mvc_config['db']['dbuser'] = 'root';
$mvc_config['db']['dbpwd'] = '123456';
*/
$mvc_config['db']['dbname'] = $dbname;
$mvc_config['db']['dbcharset'] = 'utf8';
$mvc_config['db']['dbprefix'] = $dbprefix;


// smarty配置 *****************************************
$mvc_config['smarty']['templateDir'] = _View;
$mvc_config['smarty']['compileDir'] = _Wuyou .'templates_c/';
$mvc_config['smarty']['configDir'] = _Wuyou .'configs/';
$mvc_config['smarty']['cacheDir'] = _Wuyou .'cache/';


// MVC系统基本配置 **********************************************
$mvc_config['HttpPath'] = false;				// 是否开启 index.php/Controller/Action/name/value 模式（不支持）
$mvc_config['Filter'] = false;					// 是否过滤 $_GET、$_POST、$_COOKIE、$_FILES
$mvc_config['XSS'] = true;						// 是否开启 XSS防范
$mvc_config['trim'] = true;
$mvc_config['SessionStart'] = false;			// 是否开启 SESSION（已被cms其他文件开启）
$mvc_config['DebugPhp'] = false;				    // 是否开启PHP运行报错信息
$mvc_config['DebugSql'] = false;				// 是否开启源码调试Sql语句
$mvc_config['CharSet'] = 'utf-8';				// 设置网页编码
$mvc_config['UrlControllerName'] = 'c';			// 自定义控制器名称 例如: index.php?c=index
$mvc_config['UrlActionName'] = 'a';				// 自定义方法名称 例如: index.php?c=index&a=IndexAction		
		
 
?>