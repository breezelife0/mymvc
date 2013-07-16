<?php
/**
 * MVC的模型数据层 (M)， model都需要继承该基类
 *   
 *   类文件的命名格式为：名称+'.c.php'
 *   类命名：名称+'M' ,以便与controller类名区分开
 *   如：specarea.m.php 的类名为 class specareaM(){} 
 * 
 */
 
class BaseModel {
	public $db; //model的$db属性对应数据的操作
	public function __construct(){
	    global $mvc_config;	   
	    $this->db = new pdo_class($mvc_config['db']['dbhost'], $mvc_config['db']['dbuser'], $mvc_config['db']['dbpwd'], $mvc_config['db']['dbname'], $mvc_config['db']['dbcharset']);	                                                
	}
	    
	
}


