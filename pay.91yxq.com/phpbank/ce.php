<?php

	$userid = $_POST["UserId"];//�û�ID��www.yzch.net��ȡ��
	$orderid =  $_POST["OrderId"];//�û������ţ�����Ψһ��
	$money =  $_POST["Money"];//�������
	$bankid =  $_POST["BankId"];//����ID�����ĵ���
	$keyvalue =  $_POST["Key"];//�û�key��www.yzch.net��ȡ��
	$reutrn_url =  $_POST["Url"];//�û����շ���URL����
	$ext =  $_POST["Ext"];
	$submiturl = $_POST["JumpUrl"];
	$sign = "userid=".$userid."&orderid=".$orderid."&bankid=".$bankid."&keyvalue=".$keyvalue;
	$sign2 = "money=".$money."&userid=".$userid."&orderid=".$orderid."&bankid=".$bankid."&keyvalue=".$keyvalue;
	$sign = md5($sign);//ǩ������ 32λСд����ϼ�����֤��
	$sign2 = md5($sign2);//ǩ������2 32λСд����ϼ�����֤��
	$url=$submiturl."?userid=".$userid."&orderid=".$orderid."&money=".$money."&url=".$reutrn_url."&bankid=".$bankid."&sign=".$sign."&ext=".$ext."&sign2=".$sign2;

	header('Location:'.$url);

?>