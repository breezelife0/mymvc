<?php
/**
 * MVC控制器逻辑层 (C)基层 basecontroller, controller都需要继承该基类
 *   controller文件命名：名称+'.c.php';
 *   类文件的命名格式:名称
 *   如：specarea.c.php 的类名为class specarea(){} 
 */

class BaseController {
	public $model;				// 与控制器同名的据模型对象
	public $smt;		        // smarty模板对象
	public $class_require;			// 控制器关联的类对象集	
	public $EchoData = array();		// 调试数据
	
	public function __construct(){ //子类无法自动调用父类构造函数
	    $this->initSmarty();	    
	}		
		
    /**
	 * 创建smarty对象
	 */
	public function initSmarty(){
	    global $mvc_config;
	    $this->smt = new Smarty;	    
	
	    $this->smt->setTemplateDir($mvc_config['smarty']['templateDir']);
	    $this->smt->setCompileDir($mvc_config['smarty']['compileDir']);
	    $this->smt->setConfigDir($mvc_config['smarty']['configDir']);
	    $this->smt->setCacheDir($mvc_config['smarty']['cacheDir']); 
	    
	    $this->smt->assign('V_WUYOU',V_WUYOU);	    
	    $this->smt->assign('V_TPL',V_TPL);
	    $this->smt->assign('V_INDEX',V_INDEX);	    
	}
	
	/**
	 * 加载模型文件
	 * @param	string $modelName 模型名(可选,默认为当前control的前缀，如test.c.php则实例化test.m.php)
	 * @param	
	 * @return	Object $this->model[$modelName]		模型对象
	 */
	public function _model($modelName = NULL) 
	{
	    //$file = str_replace(_PathTag, DIRECTORY_SEPARATOR, $file);
	    if($modelName == NULL) {	        
	        $modelName = get_class($this);
	        $is_self = true;
	    }	    
	    $modelfile = _Model . $modelName . '.m.php';     
	    if(!is_file($modelfile)) {
	        mvcFrame::notice($modelfile . ' 模型文件不存在');
	    }
	    
        require_once($modelfile); 
        $className = $modelName.'M'; //类名xxxM，用于区分c层的xxx
        
        if(!class_exists($modelName)){
            mvcFrame::notice($modelName . ' 模型对象不存在');
        }
        if($is_self) {           
            $this->model = new $className();
        }       
                
        Return new $className();    
	}
	
	/**
	 * 加载自定义类文件
	 * @param	string $file			文件名
	 * @param	string $modelName		类名(可选,默认为文件名)
	 * @return	Object $modelName()		类对象
	 */
	public function _class($file, $modelName = NULL)
	{
	    $file = str_replace(_PathTag, DIRECTORY_SEPARATOR, $file);
	    $modelName = ($modelName == NULL) ? $file : $modelName;
	    $file = _Class . $file . '.php';
	    if(is_file($file))
	    {
	        include_once($file);
	        if(!class_exists($modelName))
	            mvcFrame::notice($modelName . ' 类对象不存在');
	
	        if(!isset($this -> class_require[$modelName])){ // 不存在类对象
				$this -> class_require[$modelName] = new $modelName();
			}							            
	        Return $this -> class_require[$modelName];
	    }
	
	    mvcFrame::notice($file . ' 类文件不存在');
	}
	
	/**
	 * 通用错误页面提示信息
	 * @param string $msg 弹出的提示的语句
	 * @param string $goto_url 提示后跳转的路径
	 */	 
    //js提示并跳转
    public function show_msg($msg='', $goto_url='./'){	
    	$msg_js = '<script type="text/javascript">';
    	if($msg != ''){
    		$msg_js .= 'alert("'.$msg.'");';
    	}	
    	$msg_js .= 'window.location.href="'.$goto_url.'";  </script>';
    		
    	echo $msg_js;	 		
    	exit;
    }
} 
 
