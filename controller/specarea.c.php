<?php
/**
 * 特权区管理 dede_specarea
 * @author breezelife@163.com
 * 
 */
class specarea extends BaseController
{
    public $base_url ;
    public $user_name;
    public function __construct(){        
        parent::__construct();        
        $this->base_url = V_INDEX.'?c=specarea';       
        $this->model = $this->_model();  //根据需要加载model $this->model为同名模型对象 
        //获取当前用户
        $cuserLogin = new userLogin();
        $this->user_name = $cuserLogin->getUserName();        
    }
    
	// 默认首页
	public function index(){ 
	    $_GET['page'] = $_GET['page'] > 1 ? $_GET['page'] : 1;
	    //分类
	    $type_list = $this->model->specareaType('getAll');
	    //modpage	    
	    $modpage_list = $this->getModPageArr();
	    //数据
		$page_list_html = $this->getPageList();	
		
		$this->smt->assign('type_list', $type_list);
		$this->smt->assign('modpage_list', $modpage_list);
		$this->smt->assign('page_list_html', $page_list_html);
		
		$this->smt->display('specarea/specarea_list.html');
	}
	
	public function ajaxGetPageList(){
	    $page_list_html = $this->getPageList();
	    echo $page_list_html;
	}
	
	public function getPageList(){	    
	    $page = intval($_GET['page']);	   
	    //查询条件 
	    $where_arr = array();
	    if($_GET['id']) {
	        $where_arr[] = "id='".$_GET['id']."'";
	    } else {
	        if($_GET['tag']){
	            $where_arr[] .= "tag like '%".$_GET['tag']."%'";
	        }  
	        if($_GET['name']){
	            $where_arr[] .= "name like '%".$_GET['name']."%'";
	        }
	        if($_GET['modpage']){
	            $where_arr[] .= "modpage like '%".$_GET['modpage']."%'";
	        }
	        if($_GET['type']){
	            $where_arr[] .= "type='".$_GET['type']."'";
	        }
	    }
	    
	    require_once(_Include.'datasrc.class.php');
	    require_once(_Include.'datalist.class.php');
	    $base_sql = "select id,tag,name,description,modpage,type from dede_specarea";
	    $ds = new SqlDataSrc($base_sql, $where_arr,'id asc');    
	    $ds->setPage($page); //设置当前页	    
	    $ds->setHandler(array($this, 'dataHandler')); //进一步处理数据
	    
	    $dl = new DataList($ds); //将object型的datasource作为参数，传递给datalist
	    $dl->setTitle(array( //列表显示及标题设置
	            'id'=>'ID',
	            'tag' => '标签',
	            'name'=>'名称',
	            'description'=>'描述',
	            'modpage_str'=>'所属模板',
	            'type_str'=>'分类',
	    ));
	    $d_info_id = '{$d_info.id}'; 
	    $upd_url = "{$this->base_url}&a=upd&id={$d_info_id}";
	    $del_url = "{$this->base_url}&a=del&id={$d_info_id}";
	    $dl->setOperate(array( //设置操作参数 ，要求模板遍历数据时提供名为d_info的数据变量
            "<a href='$upd_url'>修改</a>",
	        '<a href="#" onclick="del_confim(\''.$del_url.'\');">删除</a>'             
	    ));
	    
	    $page_list_html = $dl->getPageHtml(); //显示分页列表
	    
	    return $page_list_html;
	}

	//显示添加页面
	public function add(){	
	    $this->checkAdmin();    
		$type_list = $this->model->specareaType('getAll');
		$modpage_list = $this->getModPageArr();	
		
		$this->smt->assign('modpage_list', $modpage_list);
		$this->smt->assign('type_list', $type_list);
		$this->smt->display('specarea/specarea_add.html');
	}
	
	//添加do
	public function add_do(){	   
	    $this->checkAdmin();
	    $body = StripSlashes($_POST['body']);	 
	    $modpage_ser = serialize($_POST['modpage_selected']);
	    
	    $spa_info = $this->model->getSpecareaByTag($_POST['tag']); 
	    if(!empty($spa_info)) {
	        $this->show_msg("该tag已存在", $this->base_url.'&a=add');	        
	    }
	    
	    $spa_info = $this->model->getSpecareaByName($_POST['name']);
	    if(!empty($spa_info)) {
	        $this->show_msg("该name已存在", $this->base_url.'&a=add');
	    }
	    //==============
	    //生成shtml文件，用于html页面调用
	    //可以通过$this->model->getShtmlPath($_POST['tag'], ture)获取查看路径
	    //==============
        if($_POST['make_shtml']){
            require_once _Include.'common.func.php';
            $shtml_path = $this->model->getShtmlPath($_POST['tag']);
            write_file($shtml_path, $body);
        }        
        
        $date = date("Y-m-d H:i:s");
		$body=addslashes($body); //转义		
        $add_data = array(
            'tag' =>  $_POST['tag'],      
            'name' =>  $_POST['name'],
            'description' => $_POST['description'],
            'modpage' => $modpage_ser,
            'body' => $body,
            'body_bak' => $body,
            'type' => intval($_POST['type']),
            'shtml_path' => $_POST['tag'].'.shtml',
            'add_date' => $date,
            'upd_date' => $date,
            'upd_user' => $this->user_name 
        );
        $rs = $this->model->db->insert('dede_specarea', $add_data);
        if($rs){
            $rs= "添加成功";
        } else {
            $rs= "添加失败!!!";
        }
        if($rs) {
            $this->show_msg($rs, $this->base_url);
        }
	}
	
	public function upd(){
	    $id = $_GET['id']; 
	    $spa_info = $this->model->getSpecareaById($id);
	    $is_admin = true;
	    
	    //modpage
	    //$modpage_list = $this->getModPageArr();    
	    
		$spa_info['body'] = stripslashes($spa_info['body']);
	    //body_bak_html
		$spa_info['body_bak_html'] = stripslashes($spa_info['body_bak']);
	    //$spa_info['body_bak_html'] = htmlspecialchars($spa_info['body_bak']);
	    
	    //type_str
	    $type_list = $this->model->specareaType('getAll');	    
	    $spa_info['type_str'] = $type_list[$spa_info['type']];
	     
	    //modpage_str
	    $modpage_arr = unserialize($spa_info['modpage']);	        
	    $spa_info['modpage_str'] = @implode(', ', $modpage_arr);
	    
	    //shtml_str
	    $spa_info['shtml_str'] = $this->model->getShtmlPath($spa_info['tag'] ,true);
	    //$this->smt->assign('type_list', $type_list);
	    //$this->smt->assign('modpage_list', $modpage_list); 不提供修改
	    $this->smt->assign('spa_info', $spa_info);	    
	    $this->smt->assign('is_admin', $is_admin);
	    	    
	    $this->smt->display('specarea/specarea_upd.html');
	}
	
	public function upd_do(){
	     $id = intval($_POST['id']);
	     $name = $_POST['name'];
	     $description = $_POST['description'];
	     $body = $_POST['body'];
	     if(!$id) {
	         $this->show_msg('参数错误', $this->base_url.'&a=index');
	     }
	     
	     if(!($name && $body)) {
	         $this->show_msg('名称及内容不能为空', $this->base_url.'&a=upd&id='.$id);
	     }
	     $modpage_ser = serialize($_POST['modpage_selected']);
		 $body=addslashes($body); //转义		 
	     $upd_data = array(
	                 'name' => $name,
	                 'description' => $description,
	                 'body' => $body,
	                 //'body_bak' => $body, 技术首次将数据整理完后，body备份不再被编辑更新
	                 'modpage' => $modpage_ser,
	                 'type' => intval($_POST['type']),
	                 'upd_date'=> date("Y-m-d H:i:s"),
	                 'upd_user' => $this->user_name
	             );	
	     $rs = $this->model->db->update('dede_specarea', $upd_data, "id={$id}");
	     //更新shtml，如果存在的话
	     $spec_info = $this->model->getSpecareaById($id);
	     if($spec_info['shtml_path']){
	         require_once _Include.'common.func.php';
	         $shtml_path = $this->model->getShtmlPath($spec_info['tag']);
	         write_file($shtml_path, $body);
	     }
	     if($rs){ 
	         $rs= "修改成功";
	     } else {
	         $rs= "修改失败!!";
	     }
	     $this->show_msg($rs, $this->base_url.'&a=index');
	}
	
	
	//添加自定义分类
	public function add_type(){	   
	    $this->checkAdmin();
	    $new_type = $_GET['new_type'];
	    $type_list = $this->model->specareaType('add', $new_type);	    
	    
	    //data_format: rs = array('status'=>xxx, 'msg'=>xxx, 'rs_data'=>data_arr);
	    if(empty($type_list)) {	        
	        $rs_data = array('status'=>-1, 'msg'=>'添加分类失败，请检查分类是否已经存在');
	    } else {	        
	        $key = count($type_list);
	        $rs_data = array('status'=>1, 'msg'=>'新分类已添加','data'=>array($key=>$new_type));
	    }	    
	    echo json_encode($rs_data);	    
	}
		
	
	public function del(){ //删除
	    $this->checkAdmin();
	    
		//检测是否存在shtml并删除		
	     $spec_info = $this->model->getSpecareaById($id);
	     if($spec_info['shtml_path']){	        
	         $shtml_path = $this->model->getShtmlPath($spec_info['tag']);
	         unlink($shtml_path);
	    }		 
		 
	    $id = intval($_GET['id']);	    
	    $rs = $this->model->db->delete('dede_specarea', "id=$id");
	    if($rs) {
	        $this->show_msg('删除成功', $this->base_url);
	    }
	    
	} 
	
	//进一步处理数据
	public function dataHandler($data){	    
	    if(!empty($data)){
	        $type_list = $this->model->specareaType('getAll');
	        
	        foreach($data as $key=>$info){
	            //type_str
                $data[$key]['type_str'] = $type_list["{$info['type']}"];
                //modpage_str
                $mod_arr = @unserialize($info['modpage']);
                if(is_array($mod_arr)){
                    $data[$key]['modpage_str'] = implode('|', $mod_arr);
                }
                $modpage_ser = serialize($_POST['modpage_selected']);
                //sub_description
                if(strlen($info['description'])>30){
                    $data[$key]['description'] = substr($info['description'], 0, 30); 
                }
	        }    
	    }
	    return $data;
	}
	function getModPageArr(){
	    require_once("inc_modpage.php");	   
	    $modpage = new modPage();
	    $modpage_list = $modpage->GetModArray();
        //将因一些规则排除的模板追加到数组中显示
	    array_push($modpage_list, 'index_page'); //网站首页
	    array_push($modpage_list, 'TongYong'); //全站通用
	    //site_foot
	    return $modpage_list;
	}
	function checkAdmin(){ //检测是否是管理员 简单的检测	
	    if($this->user_name != 'admin'){
	        $this->show_msg('非管理员没有权限操作', $this->base_url);        
	    }
	}
	
}