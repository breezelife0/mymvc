/**
 * 
 * @author breezelife@163.com
 * @version 2013-07-08
 * 为类为'query'的元素绑定点击事件，用于异步提交查询表单里的条件 
 */

(function($){
    $.fn.simplePage = function(os){
        var options = {            
            container: '.tableData',//放置分页表格数据的容器
            page_id:'page',//当前页码隐藏域ID
            gpage_id:'goto_page',//要跳转的页码隐藏域ID
            form_id: 'q_form',//放置查询条件的表单,为null时页面没有查询表单
            q_button: '.query',            
            q_url: '',//发送请求的地址
            success: null,//成功后执行的回调函数            
            callbacks: null,            
            param: {},//附加参数
            type: null,//可选：action,            
        };        
        var o = $.extend(options, os);
        
        $(o.q_button).unbind("click").bind("click",function ajax_query(){ 
        	var goto_page = $("#"+o.gpage_id).val(); 
            if(!$("body").find("form[id="+o.form_id+"]")){ //无查询表单
            	var url = o.q_url+"&page="+o.page_val;
            } else { //有查询表单
            	var q_str = $("#"+(o.form_id)).serialize();		 
            	var url =  o.q_url+"&"+q_str+"&page="+goto_page;    
            }
        	$.get(
				url,				
				function (data){			
					$(o.container).html(data); //显示数据
					$(o.q_button).unbind("click").bind("click",ajax_query); //为异步加载进来的元素绑定点击事件
				}, 
				'html'
			);		
		});        
    }
 
})(jQuery)
