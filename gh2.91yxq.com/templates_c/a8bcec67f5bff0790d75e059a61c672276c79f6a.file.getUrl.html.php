<?php /* Smarty version Smarty-3.1.7, created on 2016-12-15 15:47:46
         compiled from "./templates/grant/getUrl.html" */ ?>
<?php /*%%SmartyHeaderCode:171359894156cff7e86b82b6-03588121%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a8bcec67f5bff0790d75e059a61c672276c79f6a' => 
    array (
      0 => './templates/grant/getUrl.html',
      1 => 1456973078,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '171359894156cff7e86b82b6-03588121',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_56cff7e8752e0',
  'variables' => 
  array (
    'gamelist' => 0,
    'v' => 0,
    'serverlist' => 0,
    'regurl' => 0,
    'dregurl' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56cff7e8752e0')) {function content_56cff7e8752e0($_smarty_tpl) {?><div class="pageContent">
    <form method="post" onsubmit="return navTabSearch(this)" action="?action=<?php echo $_GET['action'];?>
&opt=<?php echo $_GET['opt'];?>
&api=geturl" class="pageForm required-validate">
    <input name="sub" type="hidden" value="1" />
    <input name="id" type="hidden" value="<?php echo $_GET['id'];?>
" />
    <div class="pageFormContent" layoutH="68">  
        <div class="unit">
                <label>游戏：</label>
            <select class="combox" name="game_id" id="game_id" ref="server_91yxq_23333324" refUrl="?action=sysmanage&opt=getServers&game_id={value}">
                <option value="0">请选择</option>
                <?php  $_smarty_tpl->tpl_vars['v'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['v']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['gamelist']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['v']->key => $_smarty_tpl->tpl_vars['v']->value){
$_smarty_tpl->tpl_vars['v']->_loop = true;
?>
                     <option value="<?php echo $_smarty_tpl->tpl_vars['v']->value['id'];?>
" <?php if ($_POST['game_id']==$_smarty_tpl->tpl_vars['v']->value['id']){?> selected <?php }?> ><?php echo $_smarty_tpl->tpl_vars['v']->value['name'];?>
</option>
                     <?php } ?>
                 </select>    
        </div>
        <div class="unit">
            <label>服区：</label>
                   <select class="combox" name="server_id" id="server_91yxq_23333324">
                <option value="0">请选择</option>
                <?php  $_smarty_tpl->tpl_vars['v'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['v']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['serverlist']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['v']->key => $_smarty_tpl->tpl_vars['v']->value){
$_smarty_tpl->tpl_vars['v']->_loop = true;
?>
                     <option value="<?php echo $_smarty_tpl->tpl_vars['v']->value['server_id'];?>
" <?php if ($_POST['server_id']==$_smarty_tpl->tpl_vars['v']->value['server_id']){?> selected <?php }?> ><?php echo $_smarty_tpl->tpl_vars['v']->value['name'];?>
</option>
                     <?php } ?>
                 </select>                       
        </div>
        <div class="unit">
                <label>注册链接地址：</label>
                <textarea rows="3" cols="60" readonly="true" name=""  class="textInput readonly"><?php echo $_smarty_tpl->tpl_vars['regurl']->value;?>
</textarea>
        </div>
		<div class="unit">
                <label>短链接地址：</label>
                <textarea rows="3" cols="60" readonly="true" name=""  class="textInput readonly"><?php echo $_smarty_tpl->tpl_vars['dregurl']->value;?>
</textarea>
        </div>
    </div>
    <div class="formBar">
        <ul>
            <li><div class="buttonActive"><div class="buttonContent"><button type="submit">生成链接</button></div></div></li>
            <li><div class="button"><div class="buttonContent"><button type="button" class="close">取消</button></div></div></li>
        </ul>
    </div>
    </form>
</div><?php }} ?>