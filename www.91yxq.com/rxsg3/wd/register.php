<?phprequire('../../include/mysqli_config.inc.php');require('../../include/common.inc.php');//if (isset($_COOKIE['login_name'])) {//    $url = 'http://www.' . DOMAIN . '.com/rxsg3/wd/select.php';//    header("Location:".$url);//    exit;//}$sql = "SELECT Title, URL, PublishDate FROM  91yxq_publish.91yxq_publish_1 WHERE NodeID = 227 ORDER BY PublishDate DESC LIMIT 5";$res = $mysqli->query($sql);$news_list = [];if ($res) {    while ($row = $res->fetch_assoc()) {        $news_list[] = $row;    }}$sql = "SELECT Photo, URL, Title FROM  91yxq_publish.91yxq_publish_1 WHERE NodeID = 232";$res = $mysqli->query($sql);$slide_list = $res->fetch_assoc();?><!DOCTYPE html><html lang="zh"><head>    <meta charset="UTF-8" />    <title>热血三国3微端登录</title>    <link rel="stylesheet" href="css/style.css" />    <script type="text/javascript" src="js/jquery-1.7.2.min.js"></script></head><body scroll="no"><div class="log_box">    <!--nav-->    <div class="nav">        <a href="http://www.91yxq.com/rxsg3/" target="_blank" class="fl a1"></a>        <a href="http://pay.91yxq.com/?game_id=8" target="_blank" class="fl a2"></a>        <a href="http://www.91yxq.com/help/index.html" target="_blank" class="fl a3"></a>        <a href="http://bbs.91yxq.com/" target="_blank" class="fl a4"></a>        <a href="http://www.91yxq.com/rxsg3/xsk/" target="_blank" class="fl a5"></a>    </div>    <!-- banner -->    <div class="banner">        <a href="<?php echo $slide_list['URL'] ?>" target="_blank">            <img src="<?php echo $slide_list['Photo'] ?>" height="165" width="263"/>        </a>    </div>    <!-- 新闻 -->    <div class="news">        <div class="news_til" class="clearfix">            <img src="./images/news.png" class="fl"/>            <a href="http://www.91yxq.com/rxsg3/xwzx/index.html" target="_blank" class="fr more">更多»</a>        </div>        <ul class="news_con">            <?php if (!empty($news_list)): ?>                <?php foreach($news_list as $val): ?>                    <li><a href="<?php echo $val['URL'] ?>"><?php echo $val['Title'] ?></a></li>                <?php endforeach; ?>            <?php endif; ?>        </ul>    </div>    <!-- 注册 -->    <form class="login_box" action="../../main.php?act=other_reg" name="iform" method="post" id="_iform">        <ul class="log_in">            <li> <span class="user"></span> <input id="_username" name="login_name" type="text" tabindex="1" placeholder="请输入4-18个字符" />                <div class="clear"></div> </li>            <p class="login_msg"></p>            <li> <span class="psw"></span> <input id="_password" name="login_pwd" type="password" tabindex="2" value="" placeholder="请输入6-20个字符" />                <div class="clear"></div> </li>            <li> <span class="psw02"></span> <input id="_repassword" name="repeat_pwd" type="password" tabindex="2" value="" placeholder="请再次输入您的密码" />                <div class="clear"></div> </li>        </ul>        <div class="login_btns">            <button type="submit" class="reg_btn02" id="loginin" style="cursor: pointer;"></button>            <a href="login.php" class="log_btn"></a>        </div>    </form></div></body></html><!-- <script>var sid = getCookie_comment("sid");if(sid&&sid.length>32){window.location.href="http://games.ifeng.com/webgame/rxsgs/client-serverlist.shtml";}////////////////////////////////////function getCookie_comment(name){var arr = document.cookie.match(new RegExp('(^| )'+name+'=([^;]*)(;|$)'));if(arr!=null) return arr[2];return null;}$(function(){$('#_iform').submit(function(){$.ajax({url: "http://play.ifeng.com/?_c=games&_a=dologin",type: "POST",dataType: "jsonp",jsonp: "jsoncallback",async: false,data: {username:$('#_username').val(),password:$('#_password').val()},jsonpCallback:"success_jsonpCallback",success: function(json) {if (json == '1') {window.location.reload();} else {$('.login_msg').html(json);}}});return false;});$('#_username').blur(function(){if ($('#_username').val()==""){$('#_username').val('请输入您的账号/邮箱');}});$('#_username').focus(function(){if ($('#_username').val()=='请输入您的账号/邮箱'){$('#_username').val('');}});});</script> --><script>	$(function(){		$('.reg_btn02').on('click',function(){			var username = $.trim($('#_username').val());			var password = $.trim($('#_password').val());			var password2 = $.trim($('#_repassword').val());			var acpatten =  new RegExp(/^[a-zA-Z0-9_-]{4,18}$/);			var pspatten =  new RegExp(/^[a-zA-Z0-9_-]{6,20}$/);			if(username == '' || password ==''){				alert('账号和密码不能为空！');				return;			}			if(!acpatten.test(username)){				alert('账号只能是4-18个字符!');				return;			}			if(!pspatten.test(password)){				alert('密码只能是6-20个字符!');				return;			}			if(password != password2){				alert('两次输入的密码不一致!');				return;			}		})	})</script></body></html>