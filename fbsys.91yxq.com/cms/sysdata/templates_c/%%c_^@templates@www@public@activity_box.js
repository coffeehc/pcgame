;
(function(window){
	
	function loadHtml(){
		
		var $activity_box = 
			'<div class="m_box" id="activity_box">'+
				'<div class="hd_title"><h2>推荐活动</h2></div>'+
				'<div class="side_news">'+
					'<ul>'+
						<?php
 global $PageInfo,$params; 
 $params = array ( 
	'action' => "LIST",
	'return' => "list",
	'nodeid' => "27",
	'num' => "6",
 ); 
;
$this->_tpl_vars['list'] = CMS_LIST($params); 
    $this->_tpl_vars['PageInfo'] = &$PageInfo;  
?>
						<?php if(!empty($this->_tpl_vars['list'] )): 
   foreach ($this->_tpl_vars['list'] as  $this->_tpl_vars['key']=>$this->_tpl_vars['var']): ?>
						'<li>'+
							'<a href="<?php echo $this->_tpl_vars["var"]["URL"];?>"><i></i><?php echo $this->_tpl_vars["var"]["Title"];?></a>'+
							'<span class="time"><?php echo date("Y-m-d", $this->_tpl_vars["var"]["PublishDate"]);?></span>'+
						'</li>'+
						<?php endforeach; endif;?>
					'</ul>'+
				'</div>'+
			'</div>';
		$activity_box = $($activity_box);
		
		$('#activity_box').replaceWith($activity_box);
		
	}
	
	function loadJQ(){
		
		var jQ = document.createElement('script');
		if( typeof(jQ.onload) != 'undefined' ){
			jQ.onload = loadHtml;
		}
		else if( typeof(jQ.onreadystatechange) != 'undefined' ){
			jQ.onreadystatechange = function(){
				if( !this.readyState || this.readyState == 'loaded' || this.readyState == 'complete' ){
					loadHtml()
				}
			}
		}
		jQ.setAttribute('src','<?php echo $this->_tpl_vars["91yxq_image_url"];?>/js/jquery-1.10.2.min.js');
		document.getElementsByTagName("HEAD")[0].appendChild(jQ);
		
	}
	
	if(typeof(window.jQuery) == 'undefined'){
		loadJQ();
	}
	else{
		loadHtml();
	}
	
})(window);