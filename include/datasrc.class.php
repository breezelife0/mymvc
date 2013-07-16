<?php
/**
 *  分页数据源 定义接口 IDataSrc
 *  @author breezelife@163.com
 *  @version 2013-07-08
 */
interface IDataSrc{
    public function setPage($page);
    public function setPerPage($perpage);
    
    public function getPage();
    public function getPerPage();
    public function getTotalRecord();
    public function getTotalPage();
    public function getData();    
}

/**
 * 类SqlDataSrc 实现接口 IDataSrc 
 * @author breezelife@163.com
 *
 */
class SqlDataSrc implements IDataSrc{
    public $db;      
    public $sql;      //sql(不含limit)    
    public $page;     // 当前第几页
    public $perpage = 15;  //每页的记录数
    public $totalPage; //总页数    
    public $handlers; //对查询到的数据进一步处理
    
    public function __construct($base_sql, $where_arr=array(), $order_str=''){
        if(!$base_sql) {
            return false;
        }    
        $this->sql = $base_sql;        
        //对于sql中用户输入的部分，需要过滤
        if(!empty($where_arr)){
            $this->sql .= ' where '.implode(' and ', $where_arr);
        }        
        if($order_str){
            $this->sql .= ' order by '.$order_str;
        }
        $this->connectDB(); //连接数据库 
    }
    
    public function connectDB(){
        global $mvc_config;
        require_once _Include.'mvcBase/db.class.php';
        $this->db = new pdo_class($mvc_config['db']['dbhost'], $mvc_config['db']['dbuser'], $mvc_config['db']['dbpwd'], $mvc_config['db']['dbname'], $mvc_config['db']['dbcharset']);
        
    }
    public function setPage($page){ 
        $totalPage = $this->getTotalPage(); 
        $this->page = $page >= $totalPage ? $totalPage : $page; // 最大页$totalpage
        $this->page = $this->page <= 1 ? 1 :$this->page; //最小页 1
    }
    public function setPerPage($perpage){
        $this->perpage = $perpage > 0 ? $perpage : 15;        
    }
    /**
     * 
     * @param array $func_arr
     * array(funcname_str)
     * or array(object, funcname_str)
     * or array(classname_str, funcname_str)
     * 
     */
    public function setHandler($func_arr){
        $this->handlers[] = $func_arr;
    }
    
    public function getPage(){
        return $this->page;   
    }
    public function getPerPage(){
        return $this->perpage;
    }
    
    public function getTotalRecord(){   
        $count_sql = preg_replace("#SELECT[ \r\n\t](.*)[ \r\n\t]FROM#is", 'select count(*) as count from', $this->sql);
        $count_sql = preg_replace("#ORDER[ \r\n\t]{1,}BY(.*)#is", '', $count_sql); 
        $rs = $this->db->fetch($count_sql);  
        
        return $rs['count'];
    }
    
    public function getTotalPage(){
        $total = $this->getTotalRecord(); 
        $this->totalPage = ceil($total/$this->perpage);
        return $this->totalPage;
    }
    
    public function getData(){ 
        $offset = ($this->page-1) * $this->perpage;
        $this->querysql = $this->sql." limit $offset,$this->perpage";        
        $data_list = $this->db->fetchAll($this->querysql); 
        //处理数据
        if(!empty($this->handlers)){
            $data_list = $this->runHander($data_list);
        }
        return $data_list;
    } 
    
    public function runHander($data_list){
        // loop over handlers
        if (!empty($this->handlers)) {
            foreach ($this->handlers as $key => $function) {//funtion
                if (!is_array($function)) {
                    $data_list = "{$function}({$data_list})";
                } else if (is_object($function[0])) { //object
                    //$output = "\$_smarty_tpl->smarty->registered_filters[Smarty::FILTER_VARIABLE][{$key}][0]->{$function[1]}({$output},\$_smarty_tpl)";
                    $data_list = $function[0]->$function[1]($data_list);                    
                } else { //class
                    $data_list = "{$function[0]}::{$function[1]}({$data_list})";
                }              
            }
        }
        return $data_list;
    }
    
    
    
}  
 