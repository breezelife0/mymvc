<?php
/**
 * 生成分页的按钮pagebar
 * @author breezelife@163.com
 * @version 2013-07-08
 */

class pageBar {
    public $total_record; //总记录数
    public $perpage; //每页显示的条数
    public $page; //当前页    
    public $pnum;  //每次显示的页数 页码导航 1 2 3 4 5
    public $total_page; //总共多少页
    public $page_style; //分页风格
    
	function __construct($totalRecord, $perpage=10, $page=1,$pnum=11){
	    if($totalRecord < 1) {
	        return false;
	    }
	    $this->total_record = $totalRecord;
	    $this->perpage = $perpage > 0 ? $perpage : 10;
	    $this->total_page = ceil($this->total_record/$this->perpage);
	    $this->page = $page <= 1 ? 1 :$page; //最小页 1
	    $this->page = $this->page >= $this->total_page ? $this->total_page :$this->page; // 最大页$totalpage
	    $this->pnum = $pnum;
	}
	
	public function setPageStyle($type){ //分页bar样式
	    $this->page_style = intval($type);
	}
	
	function makePageBarHtml($tpl, $smarty){    
        $method_name = 'makePageBarHtml_'.$this->type;
        if(method_exists($this, '$method_name')){
            return $this->$method_name($tpl, $smarty);
        } else {
            return $this->makePageBarHtml_1($tpl, $smarty); //默认样式为1
        }
	}
	
	/*	 
	 * 
	 [首页] [上页]（123456页码可隐藏） [下页] [尾页] 当前第1/453页
	*/
	function makePageBarHtml_1($tpl, $smarty){	    
	    $this->page = $this->page<1 ? 1 : $this->page; //最小为1
	    $this->page = $this->page>$this->total_page ? $this->total_page : $this->page; //最大为totalpage
	    
	    $smarty->assign('page', intval($this->page));	    
	    $smarty->assign('total_p', intval($this->total_page));
	    $smarty->assign('total_record', intval($this->total_record));
	    if($this->pnum > 0){
	        $smarty->assign('pnum_arr', $this-> getPnumArr());
	    }
	    
	    $pagebarHtml = $smarty->fetch($tpl);
        return $pagebarHtml;
	}
	/*
	 * 显示页码导航的数组 
	 */
	private function getPnumArr(){
	    $half_num = floor($this->pnum/2);
	    if($this->total_page <= $this->pnum){
	        for($i=1; $i <= $this->total_page; $i++){
	            $pnum_arr[] = $i;
	        }
	    }else if($this->page < $this->pnum){
	        for($i=1; $i<=$this->pnum; $i++){
	            $pnum_arr[] = $i;
	        }
	    } else { 
	        $first_num = $this->page - $half_num;
	        $last_num = ($this->page + $half_num)>$this->total_page ? $this->total_page : ($this->page + $half_num);
	        for($i=$first_num; $i<=$last_num; $i++){
	            $pnum_arr[] = $i;
	        }
	    }
	    return $pnum_arr; 
	} 
	
	function makePageBarHtml_2(){ //通过a链接简单查询的分页
	    $pageStr = null;
	     
	    $uri = $this->getUri();
	    $firstPageUrl = $uri."&page=1";
	    $lastPageUrl = $uri."&page=$this->total_page";
	    $prePageUrl = $uri."&page=".($this->page-1);
	    $nextPageUrl = $uri."&page=".($this->page+1);
	     
	    $pageStr = "<div id='page_bar'><a href='$firstPageUrl' >首页</a>&nbsp;";
	    if($this->page <= 1) {
	        $this->page = 1;
	        $pageStr .= "<a href='javascript:;'>已到达第一页</a>&nbsp;";
	        $pageStr .= "<a href='$nextPageUrl'>下一页</a>&nbsp;";
	    } else if($this->page >= $this->total_page) {
	        $this->page = $this->total_page;
	        $pageStr .= "<a href='$prePageUrl'>上一页</a>&nbsp;";
	        $pageStr .= "<a href='javascript:;'>已到达最后一页</a>&nbsp;";
	    } else {
	        $pageStr .= "<a href='$prePageUrl'>上一页</a>&nbsp;";
	        $pageStr .= "<a href='$nextPageUrl'>下一页</a>&nbsp;";
	    }
	    $pageStr .= "<a href='$lastPageUrl'>尾页</a>&nbsp;";
	    $pageStr .= "第[$this->page]页/共[$this->total_page]页" ;
	    $pageStr .= "共[$this->total_page]条记录</div>" ;
	     
	    return $pageStr;
	}
	
	//获取url 的querystr,如 name=aaa&page=3
	private function getUri(){
	    $uri = $_SERVER['REQUEST_URI'];
	    $uri = preg_replace('/&?page=\d*/', '', $uri); //去掉页码参数
	
	    return $uri;
	}
}
 
