<?php
/**
 * http://www.91yxq.com/rxhw/juxiangyou/index.php?recordID=1397806802&tokenID=13106
 */
    if (empty($_GET['tokenID']) || empty($_GET['recordID'])) {
        exit('所有参数均不能为空!');
    }
    $pcid = $_GET['recordID'];
    $adid = $_GET['tokenID'];

    $url = "http://api.91yxq.com/api/get_max_server.php?gid=3";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $result = curl_exec($ch);
    curl_close($ch);
    unset($ch);

    $serverId = $result;

    switch ($adid) {
        case 12360:
            $serverId = $serverId > 35 ? 0 : $serverId;
            break;
        case 12503:
            $serverId = $serverId > 66 ? 0 : $serverId;
            break;
        case 13106:
            $serverId = $serverId > 77 ? 0 : $serverId;
            break;
        case 13364:
            $serverId = $serverId > 91 ? 0 : $serverId;
            break;
    }
	
	if ($serverId == 0) {
		exit('活动已过期!');
	}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>热血虎卫</title>
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
<div class="register">
    <div class="container">
        <div class="hd">
            <img src="images/logo.png" alt="">
        </div>
        <div class="bd">
            <div class="inner">
                <div class="btn01">
                    <a href="javascript:void(0)"><img src="images/btn-01.png" alt=""></a>
                </div>
                <form action="http://www.91yxq.com/main.php?act=reg_juxiangyou" method="post">
                    <input type="hidden" id="other_act" name="action" value="1" />
                    <input type="hidden" name="game_id" value="3" />
                    <input type="hidden" name="server_id" value="<?php echo $serverId;?>" />
                    <input type="hidden" name="agent_id" value="10016" />
                    <input type="hidden" name="placeid" value="10076" />
                    <input type="hidden" name="pcid" value="<?php echo $pcid;?>" />
                    <input type="hidden" name="adid" value="<?php echo $adid;?>" />
                    <div class="form">
                        <ul>
                            <li>
                                <label>通行证：</label>
                                <input type="text" name="login_name">
                                <p>*4-20个字符</p>
                            </li>
                            <li>
                                <label>密　码：</label>
                                <input type="password" name="login_pwd">
                                <p>*6-18个字符</p>
                            </li>
                            <li>
                                <label>确认密码：</label>
                                <input type="password" name="repeat_pwd">
                                <p>*6-18个字符</p>
                            </li>
                        </ul>
                    </div>
                    <div class="btn02">
                        <input type="submit" style="width: 100px; background-image: url("./images/btn-02.png")" />
                        <!--						<a href="###"><img src="images/btn-02.png" alt=""></a>-->
                    </div>
                </form>
            </div>
            <div class="qr">
                <img src="images/qr.png" alt="">
            </div>
        </div>
        <div class="ft">
            <img src="images/reg-word.png" alt="">
        </div>
    </div>
</div>
</body>
</html>