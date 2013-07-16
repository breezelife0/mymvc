<?php
/**
 * PDO类库
 * @author tudou 41871879@qq.com
 * @version v1.0 2013-07-08 modify by breezelife@163.com 
 * 
 */

class pdo_class{
	public $pdo = null;
	public $sql = null;
	public $statement = null;
	private $is_addsla = false;
	public $options = array(
		PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES ",
	);
	public function __construct($host,$user="root",$pass="",$dbname="",$charset="utf8", $persistent=false){
		$this->options[PDO::MYSQL_ATTR_INIT_COMMAND] .= $charset;
		if($persistent){
			$this->options[PDO::ATTR_PERSISTENT] = true;
		}
		$dsn = "mysql:host={$host};dbname={$dbname}"; 
		$this->pdo = new PDO($dsn,$user,$pass,$this->options);	
	}
	
	/**
	 * 全局属性设置，包括：列名格式和错误提示类型    可以使用数字也能直接使用参数 
	 */
	public function setAttr($param,$val=''){
		if(is_array($param)){
			foreach($param as $key=>$val){
				$this->pdo->setAttribute($key,$val);
			}
		}else{
			if($val!=''){
				$this->pdo->setAttribute($param,$val);
			}else{
				return false;
			}
			
		}
	}
	
	//查询一条
	public function fetch($sql, $fetch_style=PDO::FETCH_ASSOC){	    
	    return $this->query($sql)->fetch($fetch_style);  
	}
	
	//查询多条
	public function fetchAll($sql, $fetch_style=PDO::FETCH_ASSOC) {
	    $this->query($sql);
	    if($this->statement){
	        return $this->statement->fetchAll($fetch_style);
	    }else {
	        return $this->query($sql);
	    }	
	    return $this->query($sql)->fetchAll($fetch_style);	    
	}
	
	/**
	 * 插入一条数据
	 * @param string $table
	 * @param array $data
	 * @return 
	 */
	public function insert($table,$data){
		if(!is_array($data)){
			return false;
		}
		$cols = array();
		$vals = array();
		foreach($data as $key=>$val){
			$cols[]=$key;
			$vals[]="'".$this->addsla($val)."'";
		}
		$sql  = "INSERT INTO {$table} (";
		$sql .= implode(",",$cols).") VALUES (";		
		$sql .= implode(",",$vals).")";
		
		return $this->exec($sql);
	}
	
	/**
	 * 更新数据
	 * @param string $table
	 * @param array $data
	 * @param string $where
	 * @return 
	 */
	public function update($table,$data,$where=""){
		if(!is_array($data)){
			return false;
		}
		$set = array();
		foreach($data as $key=>$val){
			$set[] = $key."='".trim($this->addsla($val))."'";
		}
		$sql = "UPDATE {$table} SET ";
		$sql .= implode(",",$set);
		$sql .= " WHERE ".$where;
		
		return $this->exec($sql);
	}
	
	/**
	 * 
	 * @param string $table
	 * @param string $where
	 * @return 
	 */
	public function delete($table,$where=""){
		$sql = "DELETE FROM {$table} WHERE ".$where;
		return $this->exec($sql);
	}
	
    /**
     * 执行Sql语句，一般用于 增、删、更新或者设置  返回影响的行数
     * @param string $sql  
     */
	public function exec($sql){
		if($sql==""){
			return false;
		}
		try{
		    $this->sql = $sql;
			return $this->pdo->exec($sql);
		}catch(Exception $e){
			return $e->getMessage();
		}
	}
	
	/**
	 *
	 * 执行有返回值的查询，可以通过链式操作，可以通过这个类封装的操作获取数据
	 * @param string $sql
	 * @return PDOstatement
	 */
	public function query($sql){
	    $this->sql = $sql;
	    if($sql ==""){
	        return false;
	    }	    
	    try {
	        if($this->statement) { //使用fetch未全部取出数据时，需要在下一次查询之前关闭游标,否则会出现错误
	            $this->statement->closeCursor();
	        }
	        $this->statement = $this->pdo->query($this->sql); 
	        return $this->statement; 
	    }catch(Exception $e){
	        return $e->getMessage();
	    }
	    
	}
	
	//输出sql语句
	public function echoSql(){
	    echo $this->sql;
	    die;
	}
	
	private function addsla($data){
		if($this->is_addsla){
			return trim(addslashes($data));
		}
		return $data;
	}
}
 