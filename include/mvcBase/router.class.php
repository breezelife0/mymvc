<?php
/**
 * MVC路由分发器
 * @author Amp PHPMVC - AMP 1.5| http://www.amysql.com/AMP/doc.htm
 * @version modify by breezelife@163.com 2013-07-08
 */

class mvcFrame {
	public $MvcProcess;

	public function __construct() {	
		global $mvc_config;
		ini_set("magic_quotes_runtime", false);
		if($mvc_config['DebugPhp']){ 
		    error_reporting(E_ALL & ~E_NOTICE);
		}
		($mvc_config['SessionStart'] && session_start());	// SESSION
		(!empty($mvc_config['CharSet']) && header('Content-type: text/html;charset=' . $mvc_config['CharSet']));
        
	}
    //todo 
	static public function notice($notice)
	{
		var_dump($notice);
		exit();
	}

	static public function filter(&$array, $function) 
	{
		if (!is_array($array)) Return $array = $function($array);
		foreach ($array as $key => $value) 
			(is_array($value) && $array[$key] = mvcFrame::filter($value, $function)) || $array[$key] = $function($value);
		Return $array;
	}
}


/************************************************
 *
 * 总进程对象
 * @param Object $ControllerName	控制器对象
 * @param string $ControllerName	控制器名称
 * @param string $ActionName		方法名称
 * @param string $ControllerFile	控制器文件
 *
 */
class MvcProcess {

	public $controller;	
	public $ControllerName;
	public $ActionName;
	public $ControllerFile;

	function ProcessStart()
	{
		global $mvc_config;
		if ($mvc_config['HttpPath']) {
			$GETParam = (strlen($_SERVER['REQUEST_URI']) > strlen($_SERVER['SCRIPT_NAME'])) ? explode('/', trim(str_ireplace($_SERVER['SCRIPT_NAME'], '', $_SERVER['REQUEST_URI']), '/')) : '';
			$GETCount = count($GETParam);
			if($GETCount > 1)
				for ($i=2; $i<$GETCount; ++$i) $_GET[$GETParam[$i]] = isset($GETParam[++$i]) ?  $GETParam[$i] : '';
		}

		$magic_quotes = function_exists('get_magic_quotes_gpc') ? get_magic_quotes_gpc() : false;	// 环境是否有过滤
		if(!$magic_quotes && $mvc_config['Filter']) { // 开启过滤		
			mvcFrame::filter($_GET,   'addslashes');
			mvcFrame::filter($_POST,  'addslashes');
			mvcFrame::filter($_COOKIE,'addslashes');
			mvcFrame::filter($_FILES, 'addslashes');
				
		} else if($magic_quotes && !$mvc_config['Filter']) {	
		    mvcFrame::filter($_GET,   'stripslashes');
		    mvcFrame::filter($_POST,  'stripslashes');
		    mvcFrame::filter($_COOKIE, 'stripslashes');
		    mvcFrame::filter($_FILES,  'stripslashes');	
		}
		
		if($mvc_config['trim']) { // 开启过滤
		    mvcFrame::filter($_GET,   'trim');
		    mvcFrame::filter($_POST,  'trim');
		    mvcFrame::filter($_COOKIE,'trim');
		    mvcFrame::filter($_FILES, 'trim');
		
		}
		$this -> ControllerName = !empty($GETParam[0]) ? $GETParam[0] : ( (isset($_GET[$mvc_config['UrlControllerName']]) && !empty($_GET[$mvc_config['UrlControllerName']])) ? $_GET[$mvc_config['UrlControllerName']] : 'index');
		$this -> ControllerName = str_replace(_PathTag, DIRECTORY_SEPARATOR, $this -> ControllerName);
		$this -> ActionName = !empty($GETParam[1]) ? $GETParam[1] : ( (isset($_GET[$mvc_config['UrlActionName']]) && !empty($_GET[$mvc_config['UrlActionName']])) ? $_GET[$mvc_config['UrlActionName']] : 'index'); 
		$this -> ControllerFile = _Controller . $this -> ControllerName . '.c.php';
	}

	function ControllerStart()
	{   
		((is_file($this -> ControllerFile) && include_once($this -> ControllerFile)) || 
		(is_file(_Controller . 'index.c.php') && include_once(_Controller . 'index.c.php')) ||
		mvcFrame::notice($this -> ControllerFile . ' 控制器文件不存在'));

		(class_exists($this -> ControllerName) || (($this -> ControllerName = 'index') && class_exists('index')) || 
		mvcFrame::notice($this -> ControllerName . ' 控制器不存在'));

		$methods = get_class_methods($this -> ControllerName);			// 获取类中的方法名 

		(in_array($this -> ActionName, $methods, true) || 
		(($this -> ActionName = 'index') && in_array($this -> ActionName, $methods, true)) ||
		mvcFrame::notice($this -> ActionName . ' 方法不存在'));
		$this -> controller = new $this->ControllerName($_GET);	// 实例控制器
		$this -> controller -> {$this -> ActionName}();			// 执行方法
	}

}


