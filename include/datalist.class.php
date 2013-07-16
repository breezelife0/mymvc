<?php
/**
 * 生成分页的显示的数据表格html及分页按钮  
 *
 * @package  
 * @author  breezelife@163.com
 * 
 * @version datalist.class.php 2013-07-08 
 */

/**
 * 初始化的参数$ds,需要实现接口IDataSrc
 *  
 * [example]
 * 使用默认模板生成数据表格和分页按钮html
 * $dl = new DataList($ds);  
 * $page_list_html = $dl->getPageHtml(); 
 * [example/]
 * 
 * [example]
 * 单独获取数据及分页按钮html
 * $dl = new DataList($ds);
 * $page_data = $dl->getData();
 * $page_bar_html = $dl->getPageBarHtml();   
 * [example/]
 * 
 * 更多个性化设置请参考类中提供的函数
 *
 */

class DataList{
    public $ds;
    public $data_arr;
    public $title_arr;
    public $operate_arr;
    public $page_style;
    public $pnum=11;
    public $pagebar_tpl = 'page/tpl_page_bar.html';
    public $page_tpl = 'page/tpl_page_list.html'; 
    public $smarty;
    
    
	public function __construct(IDataSrc $ds){
	    $this->ds = $ds;
	    $this->initSmarty();
	}	
	/**
	 * 设置数据
	 * @param array $data_arr
	 */
	public function setData($data_arr){
	    $this->data_arr = $data_arr;
	}
	
	/**
	 * 设置表格每一列数据对应的标题 
	 * @param array $title_arr{datakey=>titlename}
	 * @return null
	 * 
	 * [example]
	 * $dl->setTitle(array( //列表显示及标题设置
	 *           'id'=>'ID',
	 *           'tag' => '标签',
	 *           'name'=>'名称',
	 *           'type_str'=>'分类',
	 *   ));
	 */
	public function setTitle($title_arr){
	    $this->title_arr = $title_arr;
	}
	/**
	 * 设置表格中操作信息及url参数
	 * 
	 * @param array $operate_arr
	 * @return null
	 *
	 * [example]
	 *  $dl->setOperate(array( //设置操作
	 *           '修改' => array(   
	 *                   'uri' => '/wuyou/index_mvc.php?c=specarea&a=upd',
	 *                   'params' => array('id', 'name'),
	 *           ),
	 *           '删除' => array(   
	 *                   'uri' => '/wuyou/index_mvc.php?c=specarea&a=del',
	 *                   'params' => array('id'),
	 *           )
	 *   ));	
	 * [example/]
	 */
	public function setOperate($operate_arr){ //要求模板 d_info	
	    $this->operate_arr = $operate_arr;	    
	}
	
	/**
	 * 设置分页按钮的样式，可供选择的样式请查看 page.class.php
	 * @param int $type
	 */
	public function setPageStyle($page_style){ //分页bar样式
	    $this->page_style = $page_style;
	}
	
    /**
     * 页码导航显示多少页码
     * @param int $pnum 
     * 默认11    
     */
	public function setPnum($pnum){ 
	    $this->pnum = $pnum;
	}
	/**
	 * 设置分页按钮的模板
	 * @param string $tpl
	 * 默认'page/tpl_page_bar.html'
	 */
	public function setPagebarTpl($tpl){ 
	    $this->pagebar_tpl = $tpl;
	}
	
	/**
	 * 设置分页数据（含分页按钮）的模板
	 * @param string $tpl
	 * 默认 'page/tpl_page_list.html'
	 */
	public function setPageTpl($tpl){ //设置数据的模板
	    $this->page_tpl = $tpl;
	}
	
	/**
	 * 获取数据，可以通过setData设置，也可以从$ds中获取
	 * @return array 
	 */
	public function getData(){ 
	    //查询数据
	    if(empty($this->data_arr)){ //从数据库查询数据
	        return $this->ds->getData();
	    } else{
	        return $this->data_arr;
	    }
	}
	/**
	 * 生成page_list 及page_bar 的html，直接放到页面显示 
	 * @return string (html)
	 */

	public function getPageHtml(){
	    $this->data_arr = $this->getData();
	    if(empty($this->title_arr)){//title
	        foreach($this->data_arr[0] as $filed=>$val){
	            $this->title_arr[$filed] = $filed;
	        }
	    }
	    $with_operate = true;
	    if(empty($this->operate_arr)){
	        $with_operate = false;
	    }
	     
	    $pagebarHtml = $this->getPageBarHtml();
	    //获取编译后的html
	    $this->smarty->assign('title_arr', $this->title_arr);
	    $this->smarty->assign('with_operate', $with_operate);
	    $this->smarty->assign('operate_arr', $this->operate_arr);
	    $this->smarty->assign('data_arr', $this->data_arr);
	    $this->smarty->assign('pagebarHtml', $pagebarHtml);
	     
	    return $this->smarty->fetch($this->page_tpl); //fetch html
	}
	
    /**
     * 获取分页的age_bar html
     * @return string (html)
     */
	public function getPageBarHtml(){
	    require_once(_Include.'pageBar.class.php');
	    $pager = new pageBar($this->ds->getTotalRecord(), $this->ds->getPerPage(), $this->ds->getPage(),$this->pnum);
	    if($this->page_style){
	        $pager->setPageStyle($this->page_style);
	    }
	     
	    $pagebarHtml = $pager->makePageBarHtml($this->pagebar_tpl, $this->smarty);
	    return $pagebarHtml; 
	}
	
	/**
	 * init $this->smarty
	 * 配置smarty
	 */
	public function initSmarty(){
	    global $mvc_config;
	    $this->smarty = new Smarty;
	
	    $this->smarty->setTemplateDir($mvc_config['smarty']['templateDir']);
	    $this->smarty->setCompileDir($mvc_config['smarty']['compileDir']);
	    $this->smarty->setConfigDir($mvc_config['smarty']['configDir']);
	    $this->smarty->setCacheDir($mvc_config['smarty']['cacheDir']);
	}
	
}


  