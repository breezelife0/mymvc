<?php
/*
 * 功能：生成文件，调用指定函数返回数据
 * 参数：$file_path:文件绝对路径
 *     $content_arr : 要写入的数组
 *     $func_name : 文件中的函数名 
 * */
function data_func_file($file_path, $content_arr, $func_name){
    $content = "<?php\r\n";
    $content .= "function $func_name (){\r\n";
    $content .= "\x20\x20\x20\x20 \$data_arr = " . var_export($content_arr, true) . ";\r\n";
    $content .= "\x20\x20\x20\x20 return \$data_arr; \r\n";
    $content .= "}\r\n";
    $content .= "?>";
	
    write_file($file_path, $content);   
}

function write_file($file_path, $content){
	if (!file_put_contents($file_path, $content, LOCK_EX)){
        $fp = @fopen($file_path, 'wb+');
        if (!$fp)
        {
            exit('写入文件“'.$file_path.'”失败');
        }
        if (!@fwrite($fp, trim($content)))
        {
            exit('写入文件“'.$file_path.'”失败');
        }
        @fclose($fp);
    }      
}


//强制下载
function download($file_dir, $file_name,$rename=''){
	if(!$file_dir || !$file_name) {
		return false;
	} 
	$file_path = $file_dir.'/'.$file_name;
	$rename = empty($rename) ? $file_name : $rename;
	
	header('Content-Type: application/force-download');
	header("Content-Type: application/octet-stream");					
	header('Content-Length: ' . filesize($file_path)); 			
	  
	$ua = $_SERVER["HTTP_USER_AGENT"]; 				
	if (preg_match("/MSIE/", $ua)) {
		//$rename = iconv("gb2312","UTF-8",$rename);	?????			
		$encoded_filename = urlencode($rename);    
		$encoded_filename = str_replace("+", " ", $encoded_filename);    				
		
		header('Content-Disposition: attachment; filename="' . $encoded_filename . '"');    
	} else if (preg_match("/Firefox/", $ua)) {    
		header('Content-Disposition: attachment; filename*="utf8\'\'' . $rename . '"');    
	} else {    
		header('Content-Disposition: attachment; filename="' . $rename . '"');    
	} 		 
	readfile($file_path);
	exit;
} 

//输出Excel表格，$data_arr为二维数组
function export_Excel($data_arr, $filename='export'){
	if(empty($data_arr)) {
		echo "数据不能为空";		
		return false;
	}
	//html head
	$html_head = '<html xmlns:o="urn:schemas-microsoft-com:office:office"  xmlns:x="urn:schemas-microsoft-com:office:excel"  xmlns="http://www.w3.org/TR/REC-html40">  
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">  
		<html>  
			<head>  
				<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />  
				<style id="Classeur1_16681_Styles"></style>  
			</head>  
			<body>  
				<div id="Classeur1_16681" align=center x:publishsource="Excel"> 
					<table>';   
	//html tail
	$html_tail = '  </table>
				</div>  
			</body>  
		</html> ';

 	//html body
	$html_body = '';
	foreach($data_arr as $key0=>$info) {
		if(!is_array($info)) {			
			continue;
		}
		$html_body .= "<tr><td>$key0</td>";		
		foreach($info as $col_name => $val) { //一行的元素						
			$html_body .= "<td>$val</td>";  			
		}	
		$html_body .= "</tr>";
	}	
	//添加列名
	$one_info = array_pop($data_arr);
	$col_names = array_keys($one_info);
	$col_name_str = "<td>ID</td>";
	foreach($col_names as $key => $val) {
		$col_name_str .= "<td>$val</td>";
	}	 
	$html_body = "<tr>".$col_name_str."</tr>". $html_body;
	 
	//输出
	define("FILETYPE","xls"); 
	ob_start();		    
    
	header("Content-type:application/vnd.ms-excel"); 
	if(FILETYPE=="xls"){ 
		$filename .= '.xls';		
	}else{ 
		$filename .= '.csv';		
	} 	
	
	$ua = $_SERVER["HTTP_USER_AGENT"]; 				
	if (preg_match("/MSIE/", $ua)) {		
		$encoded_filename = urlencode($filename);    
		$encoded_filename = str_replace("+", " ", $encoded_filename);    				
		
		header('Content-Disposition: attachment; filename="' . $encoded_filename . '"');    
	} else if (preg_match("/Firefox/", $ua)) {    
		header('Content-Disposition: attachment; filename*="utf8\'\'' . $filename . '"');    
	} else {    
		header('Content-Disposition: attachment; filename="' . $filename . '"');    
	} 		 
	 
 	$excel_html = $html_head . $html_body . $html_tail;
	echo $excel_html;
	 
	die;
}

//js提示并跳转
function show_msg($msg='', $goto_url='./'){	
	$msg_js = '<script type="text/javascript">';
	if($msg != ''){
		$msg_js .= 'alert("'.$msg.'");';
	}	
	$msg_js .= 'window.location.href="'.$goto_url.'";  </script>';
		
	echo $msg_js;	 		
	exit;
}