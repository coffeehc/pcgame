<?php   
if(!defined('IN_IWPC')) {
 	exit('Access Denied');
}
if(!class_exists("CKEditor")){
	include_once(INCLUDE_PATH."ckeditor/ckeditor.php");
}
// Create a class instance.
$CKEditor = new CKEditor();
// Path to the CKEditor directory.
$CKEditor->basePath = INCLUDE_PATH.'/ckeditor/';
$CKEditor->config['width'] = "100%";
$CKEditor->config['height'] = 500;
$CKEditor->config['skin'] = "v2";
$CKEditor->config['image_previewText'] = "蓝慕科技的技术主导方向是企业电子商务、企业信息门户、企业网站、企业业务自动化。公司主要业务是企业信息化应用咨询服务、企业及政府信息门户建设、企业业务流程自动化应用解决、网站内容管理系统等企业级服务，我们提供成熟的企业信息门户解决方案、政府公众与内部集成信息门户等信息化应用方案；我们开发了在国内业界首屈一指的CMSware内容管理系统，为企业级网站与知识门户的内容管理提供优秀灵活的解决方案。";
$CKEditor->config['filebrowserImageUploadUrl'] = "./upload_cke.php?sId={$IN['sId']}&type=img&o=upload&mode=one&NodeID={$IN['NodeID']}";
$CKEditor->config['filebrowserBrowseUrl'] = "./admin_select.php?sId={$IN['sId']}&o=psn_picker&psn=";
$CKEditor->config['filebrowserWindowWidth'] = "600";
$CKEditor->config['filebrowserWindowHeight'] = "266";
$CKEditor->config['filebrowserImageBrowseLinkUrl'] = false;
$CKEditor->config['filebrowserFlashUploadUrl'] = "./upload_cke.php?sId={$IN['sId']}&type=flash&o=upload&mode=picker&changeName=1&NodeID={$IN['NodeID']}";
$CKEditor->config['filebrowserAttachfileUploadUrl'] = "./upload_cke.php?sId={$IN['sId']}&type=attach&o=upload&mode=picker&changeName=1&NodeID={$IN['NodeID']}";
$CKEditor->config['lang_fix'] = array(
	"localize" => "图片本地化",
	"pagebreak"  => "插入分页符",
	"editpagebreak" => "编辑分页符",
	"pagebreaktitle" => "插入/编辑分页符",
	"pagetitle" => "分页子标题",
	"insertimage" => "插入图片",
	"browseServer" => "发布点PSN选择",
	"attachtitle" => "插入文件",
	"attachfile" => "插入附件下载",
	"attachfiletitle" => "插入附件",
	"attachfileurl" => "链接地址",
	"attachfiletxt" => "显示文件名",
	"attachfileurlempty" => "链接地址不能为空",
	"attachmedia" => "插入/修改多媒体",
	"editattachmedia" => "修改多媒体",
	"attachmediavars" => "多媒体变量",
	"media" => array(
		"video" => "视频高度方案",
		"audio" => "音频高度方案",
		"autostart" => "自动开始",
		"enablecontextmenu" => "允许右键菜单",
		"clicktoplay" => "允许鼠标点击播放/暂停",
		"showcontrols" => "显示控制栏",
		"showstatusbar" => "显示状态",
		"showdisplay" => "显示多媒体信息",
		"loop" => "循环播放",
	)
);
$CKEditor->config['Localize'] = false;
$CKEditor->config['removePlugins'] = "pagebreak";
$CKEditor->config['videopreset'] = array(300,250);
$CKEditor->config['audiopreset'] = array(300,68);
$CKEditor->config['downicon'] = TplVarsAdmin::getValue('PUBLISH_URL') . "/images/icon/%s.gif";
$CKEditor->config['extraPlugins'] = "checkbox,cmswareforms,cmswarelocal,addhtml,cmswarecss,cmswarepagebreak,cmswareattach";
$IsIE = isset($IsIE) ? $IsIE : preg_match("/MSIE ([0-9\.]+)/is",$_SERVER["HTTP_USER_AGENT"]);
$CKEditor->config['toolbar'] = array(
 array( 'Maximize','Cut','Copy','Paste','PasteText','PasteFromWord','Find','-','Undo','Redo','-','RemoveFormat','-','Bold','Italic','Underline','Strike','-','NumberedList','BulletedList','Outdent','Indent','-','Subscript','Superscript','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','PageBreak',($IsIE ? NULL : '-'),'CMSWareLocalize' ),
 '/',
 array( 'Font','FontSize','Format','Link','Unlink','Anchor','-','TextColor','BGColor','-','Table','CMSWareForms','-','Image','Flash','CMSWareAttach','HorizontalRule','SpecialChar','-','Source'),
 array( 'CMSWareCss' )
);
$CKEditor->config['enterMode'] = "CKEDITOR.ENTER_BR";
$CKEditor->config['shiftEnterMode'] = "CKEDITOR.ENTER_P";
$CKEditor->config['contentsCss'] = "<CKEditor folder>/cmsware.css";
$CKEditor->config['image_plus'] = array(
	"changeName" => array(
		'type'    => 'checkbox',
		'id'      => 'changeName',
		'default' => true,
		'label'   => '上传后重命名'
	),
	"makeMinibox" => array(
		'type'    => 'vbox',
		'padding' => 0,
		'width'   => '200px',
		'style' 	=> 'width:200px',
		'children'=> array(
			array(
				'type'    => 'checkbox',
				'id'      => 'makeMini',
				'default' => false,
				'label'   => '生成缩略图'
			),
			array(
				'type'    => 'hbox',
				'widths'  => array( '100px', '100px' ),
				'align'   => 'left',
				'children'=> array(
					array(
						'type'     => 'text',
						'id'       => 'width',
						'label'    => '长',
						'default'  => '160'
					),
					array(
						'type'     => 'text',
						'id'       => 'height',
						'validate' => false,
						'label'    => '宽',
						'default'  => '120',
					)
				),
			)
		)
	),
	"mkHTML" => array(
		'type'    => 'vbox',
		'padding' => 1,
		'width'   => '200px',
		'style' 	=> 'width:200px',
		'children'=> array(
			array(
				'type'    => 'checkbox',
				'id'      => 'mkHTML',
				'default' => false,
				'label'   => '大图生成网页',
			),
			array(
				'type'    => 'text',
				'id'      => 'img_title',
				'label'   => '图片标题'
			),
			array(
				'type'    => 'textarea',
				'id'      => 'img_intro',
				'label'   => '图片简介'
			),
		)
	)
);
$CKEditor->addGlobalEventHandler('dialogDefinition', "function( ev )
	{
		var dialogName = ev.data.name;
		var dialogDefinition = ev.data.definition;
		var editor=ev.editor,plus=editor.config.image_plus,i,elements=false;
		if ( dialogName == 'image' )
		{
			dialogDefinition.contents[0].elements[0].children[0].children[1].label=editor.config.lang_fix.browseServer;
			for(i=0;i<dialogDefinition.contents.length;i+=1){
				if(dialogDefinition.contents[i].id=='Upload'){
					elements=dialogDefinition.contents[i].elements;
					break;
				}
			}
			if(!elements){return false;}
			elements[0].id = 'uploadFile';
			i=elements.length-1;
			elements[i]['for'][1] = 'uploadFile';
			elements[i].onClick = function( evt ){
				var d = this.getDialog(), f = d.getContentElement( 'Upload', 'uploadButton' )['for'],
				action = d.definition.getContents(f[0]).get(f[1]).action, i;
				var params={
					'changeName' : 0,
					'makeMini'   : 0,
					'width'      : 0,
					'height'     : 0,
					'mkHTML'     : 0
				};
				if( d.getContentElement( 'Upload', 'changeName' ).getValue() == true ){
					params.changeName = 1;
				}
				if( d.getContentElement( 'Upload', 'makeMini' ).getValue() == true ){
					params.makeMini = 1;
					params.width = d.getContentElement( 'Upload', 'width' ).getValue();
					params.height = d.getContentElement( 'Upload', 'height' ).getValue();
				}
				if( d.getContentElement( 'Upload', 'mkHTML' ).getValue() == true ){
					params.mkHTML = 1;
				}
				for(i in params){
					action += params[i] ==null ? '' : '&' + i + '=' + params[i];
				}
				d.getContentElement( 'Upload', 'uploadFile' ).getInputElement().getParent().$.action = action;
				return true;
			}
			elements[i] = {
				type : 'vbox',
				width : '240px',
				style : 'width:240px;',
				padding : 0,
				children :
				[
					{
						type : 'hbox',
						widths : [ '140px', '100px' ],
						align : 'left',
						children :
						[
							elements[i],
							plus.changeName
						]
					}
				]
			};
			plus.makeMinibox.children[0].onChange = function(){
				var d = this.getDialog(),v = this.getValue();
				var cmd = v==true ? 'enable' : 'disable';
				d.getContentElement( 'Upload', 'width' )[cmd]();
				d.getContentElement( 'Upload', 'width' ).getInputElement().$.disabled=!v;
				d.getContentElement( 'Upload', 'height' )[cmd]();
				d.getContentElement( 'Upload', 'height' ).getInputElement().$.disabled=!v;
				d.getContentElement( 'Upload', 'mkHTML' )[cmd]();
				d.getContentElement( 'Upload', 'mkHTML' ).getInputElement().$.disabled=!v;
				v = v && d.getContentElement( 'Upload', 'mkHTML' ).getValue();
				cmd = v==true ? 'enable' : 'disable';
				d.getContentElement( 'Upload', 'img_title' )[cmd]();
				d.getContentElement( 'Upload', 'img_title' ).getInputElement().$.disabled=!v;
				d.getContentElement( 'Upload', 'img_intro' )[cmd]();
				d.getContentElement( 'Upload', 'img_intro' ).getInputElement().$.disabled=!v;
				return;
			}
			elements.push(plus.makeMinibox);
			plus.mkHTML.children[0].onChange = function(){
				var d = this.getDialog(),v = this.getValue();
				var cmd = v==true ? 'enable' : 'disable';
				d.getContentElement( 'Upload', 'img_title' )[cmd]();
				d.getContentElement( 'Upload', 'img_title' ).getInputElement().$.disabled=!v;
				d.getContentElement( 'Upload', 'img_intro' )[cmd]();
				d.getContentElement( 'Upload', 'img_intro' ).getInputElement().$.disabled=!v;
				return;
			}
			elements.push(plus.mkHTML);
			dialogDefinition.onFocus = function()
			{
				this.getContentElement( 'Upload', 'width' ).disable();
				this.getContentElement( 'Upload', 'width' ).getInputElement().$.disabled=true;
				this.getContentElement( 'Upload', 'height' ).disable();
				this.getContentElement( 'Upload', 'height' ).getInputElement().$.disabled=true;
				this.getContentElement( 'Upload', 'mkHTML' ).disable();
				this.getContentElement( 'Upload', 'mkHTML' ).getInputElement().$.disabled=true;
				this.getContentElement( 'Upload', 'img_title' ).disable();
				this.getContentElement( 'Upload', 'img_title' ).getInputElement().$.disabled=true;
				this.getContentElement( 'Upload', 'img_intro' ).disable();
				this.getContentElement( 'Upload', 'img_intro' ).getInputElement().$.disabled=true;
				return;
			};
		}
		else if ( dialogName == 'flash' )
		{
			dialogDefinition.contents[0].elements[0].children[0].children[1].label=editor.config.lang_fix.browseServer;
		}
	},null,null,1");
$CKEditor->editor("data_".$var["FieldName"], $pInfo[$var["FieldName"]]);
?>