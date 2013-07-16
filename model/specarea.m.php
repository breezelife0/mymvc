<?php
/**
 * 特权区model层 dede_specarea 
 * @author breezelife@163.com
 *
 */
class specareaM extends BaseModel{
    public $tbname = 'dede_specarea';
    
    //通过主键id查询
    function getSpecareaById($id = ''){
        if(!$id) {
            return false;
        }
        $sql = "select * from {$this->tbname} where id='$id'";
        return $this->db->fetch($sql);
    }
    
    //通过tag查询，tag值唯一
    function getSpecareaByTag($tag = ''){
        if(!$tag) {
            return false;
        }
        $sql = "select * from {$this->tbname} where tag='$tag'";
        return $this->db->fetch($sql);
    }
    
    //通过name查询,name值唯一
    function getSpecareaByName($name = ''){
        if(!$name) {
            return false;
        }
        
        $sql = "select * from {$this->tbname} where name='$name'";
        return $this->db->fetch($sql);
        
    }
    
	/*
	*功能：自定义分类维护
	*说明：由于分类使用较少，目前只提供get和add两个操作
	*	   
	* $data: 数组查询时为id组成的一维数组；添加时为value组成的一维数组
	*/	 
	function specareaType($act, $new_type=''){	    
	    $file_path = _Include.'data/data_specarea_type.php';
	    if(!file_exists($file_path)){
	        require_once _Include.'common.func.php';	        
	        data_func_file($file_path, array(), 'getSpecareaType');
	    }	    
	    require_once $file_path; 
	    $type_list = getSpecareaType();
	    
		if($act == 'getAll'){
	        return $type_list;
		}			
		if($act == 'add'){
		   if(empty($new_type)) { //非空		       
		       return false;
		   }
		   if(in_array($new_type, $type_list)){ //不可重复		       
		       return false;
		   }
		   
		   require_once _Include.'common.func.php';		   
		   array_push($type_list, $new_type);		   
		   data_func_file($file_path, $type_list, 'getSpecareaType');
		   return $type_list;
		}
	}
	
	//获取生成shtml文件的路径 
	function getShtmlPath($tag='', $is_v=''){
	    if(empty($tag)) {
	        return false;
	    }
	    if(!$is_v){
	        return _Shtml.'specarea/'.$tag.'.shtml';
	    }else {
	        return V_shtml.'specarea/'.$tag.'.shtml';
	    }
	    
	}
	
	
	
}