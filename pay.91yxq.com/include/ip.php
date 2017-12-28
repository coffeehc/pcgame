<?php
/**
* IP 地理位置查询类
*
* @author weedong
* @version 1.5
* @copyright 2012 www.91wan.com
*/
class IpLocation {
    /**
     * QQWry.Dat文件指针
     *
     * @var resource
     */
    var $fp;
    
    /**
     * 第一条IP记录的偏移地址
     *
     * @var int
     */
    var $firstip;
    
    /**
     * 最后一条IP记录的偏移地址
     *
     * @var int
     */
    var $lastip;
    
    /**
     * IP记录的总条数（不包含版本信息记录）
     *
     * @var int
     */
    var $totalip;
    
    /**
     * 返回读取的长整型数
     *
     * @access private
     * @return int
     */
    function getlong() {
        //将读取的little-endian编码的4个字节转化为长整型数
        $result = unpack('Vlong', fread($this->fp, 4));
        return $result['long'];
    }
    
    /**
     * 返回读取的3个字节的长整型数
     *
     * @access private
     * @return int
     */
    function getlong3() {
        //将读取的little-endian编码的3个字节转化为长整型数
        $result = unpack('Vlong', fread($this->fp, 3).chr(0));
        return $result['long'];
    }
    
    /**
     * 返回压缩后可进行比较的IP地址
     *
     * @access private
     * @param string $ip
     * @return string
     */
    function packip($ip) {
        // 将IP地址转化为长整型数，如果在PHP5中，IP地址错误，则返回False，
        // 这时intval将Flase转化为整数-1，之后压缩成big-endian编码的字符串
        return pack('N', intval(ip2long($ip)));
       }
    
    /**
     * 返回读取的字符串
     *
     * @access private
     * @param string $data
     * @return string
     */
    function getstring($data = "") {
        $char = fread($this->fp, 1);
        while (ord($char) > 0) {        // 字符串按照C格式保存，以\0结束
            $data .= $char;                // 将读取的字符连接到给定字符串之后
            $char = fread($this->fp, 1);
        }
        return $data;
    }
    
    /**
     * 返回地区信息
     *
     * @access private
     * @return string
     */
    function getarea() {
        $byte = fread($this->fp, 1);    // 标志字节
        switch (ord($byte)) {
            case 0:                        // 没有区域信息
                $area = "";
                break;
            case 1:
            case 2:                        // 标志字节为1或2，表示区域信息被重定向
                fseek($this->fp, $this->getlong3());
                $area = $this->getstring();
                break;
            default:                    // 否则，表示区域信息没有被重定向
                $area = $this->getstring($byte);
                break;
        }
        return $area;
    }
    
    /**
     * 根据所给 IP 地址或域名返回所在地区信息
     *
     * @access public
     * @param string $ip
     * @return array
     */
    function getlocation($ip) {
        if (!$this->fp) return null;            // 如果数据文件没有被正确打开，则直接返回空
        $location['ip'] = gethostbyname($ip);     // 将输入的域名转化为IP地址
        $ip = $this->packip($location['ip']);    // 将输入的IP地址转化为可比较的IP地址
                                                // 不合法的IP地址会被转化为255.255.255.255
        // 对分搜索
        $l = 0;                            // 搜索的下边界
        $u = $this->totalip;            // 搜索的上边界
        $findip = $this->lastip;        // 如果没有找到就返回最后一条IP记录（QQWry.Dat的版本信息）
        while ($l <= $u) {                // 当上边界小于下边界时，查找失败
            $i = floor(($l + $u) / 2);    // 计算近似中间记录
            fseek($this->fp, $this->firstip + $i * 7);
            $beginip = strrev(fread($this->fp, 4));        // 获取中间记录的开始IP地址
            // strrev函数在这里的作用是将little-endian的压缩IP地址转化为big-endian的格式
            // 以便用于比较，后面相同。
            if ($ip < $beginip) {        // 用户的IP小于中间记录的开始IP地址时
                $u = $i - 1;            // 将搜索的上边界修改为中间记录减一
            }
            else {
                fseek($this->fp, $this->getlong3());
                $endip = strrev(fread($this->fp, 4));    // 获取中间记录的结束IP地址
                if ($ip > $endip) {        // 用户的IP大于中间记录的结束IP地址时
                    $l = $i + 1;        // 将搜索的下边界修改为中间记录加一
                }
                else {                    // 用户的IP在中间记录的IP范围内时
                    $findip = $this->firstip + $i * 7;
                    break;                // 则表示找到结果，退出循环
                }
            }
        }
    
        //获取查找到的IP地理位置信息
        fseek($this->fp, $findip);
        $location['beginip'] = long2ip($this->getlong());    // 用户IP所在范围的开始地址
        $offset = $this->getlong3();
        fseek($this->fp, $offset);
        $location['endip'] = long2ip($this->getlong());        // 用户IP所在范围的结束地址
        $byte = fread($this->fp, 1);    // 标志字节
        switch (ord($byte)) {
            case 1:                        // 标志字节为1，表示国家和区域信息都被同时重定向
                $countryOffset = $this->getlong3();            // 重定向地址
                fseek($this->fp, $countryOffset);
                $byte = fread($this->fp, 1);    // 标志字节
                switch (ord($byte)) {
                    case 2:                // 标志字节为2，表示国家信息又被重定向
                        fseek($this->fp, $this->getlong3());
                        $location['country'] = $this->getstring();
                        fseek($this->fp, $countryOffset + 4);
                        $location['area'] = $this->getarea();
                        break;
                    default:            // 否则，表示国家信息没有被重定向
                        $location['country'] = $this->getstring($byte);
                        $location['area'] = $this->getarea();
                        break;
                }
                break;
            case 2:                        // 标志字节为2，表示国家信息被重定向
                fseek($this->fp, $this->getlong3());
                $location['country'] = $this->getstring();
                fseek($this->fp, $offset + 8);
                $location['area'] = $this->getarea();
                break;
            default:                    // 否则，表示国家信息没有被重定向
                $location['country'] = $this->getstring($byte);
                $location['area'] = $this->getarea();
                break;
        }
        if ($location['country'] == " CZ88.NET") {    // CZ88.NET表示没有有效信息
            $location['country'] = "未知";
        }
        if ($location['area'] == " CZ88.NET") {
            $location['area'] = "";
        }
        return $location;
    }
    
    /**
     * 构造函数，打开 QQWry.Dat 文件并初始化类中的信息
     *
     * @param string $filename
     * @return IpLocation
     */
    function IpLocation($filename = "/www/pay2.91wan.com/QQWry.Dat") {
        if (($this->fp = @fopen($filename, 'rb')) !== false) {
            $this->firstip = $this->getlong();
            $this->lastip = $this->getlong();
            $this->totalip = ($this->lastip - $this->firstip) / 7;
            //注册析构函数，使其在程序执行结束时执行
            register_shutdown_function(array(&$this, '_IpLocation'));
        }
    }
    
    /**
     * 析构函数，用于在页面执行结束后自动关闭打开的文件。
     *
     */
    function _IpLocation() {
        fclose($this->fp);
    }
}

//echo _QQWRY;
class QQWry{
    var $StartIP=0;
    var $EndIP=0;
    var $Country='';
    var $Local='';
	var $datfile="/www/pay2.91wan.com/config/QQWry.Dat";
    var $CountryFlag=0; // 标识 Country位置
             // 0x01,随后3字节为Country偏移,没有Local
             // 0x02,随后3字节为Country偏移，接着是Local
             // 其他,Country,Local,Local有类似的压缩。可能多重引用。
    var $fp;

    var $FirstStartIp=0;
    var $LastStartIp=0;
    var $EndIpOff=0 ;

    function getStartIp($RecNo){
		 $offset=$this->FirstStartIp+$RecNo * 7 ;
		 @fseek($this->fp,$offset,SEEK_SET) ;
		 $buf=fread($this->fp ,7) ;
		 $this->EndIpOff=ord($buf[4]) + (ord($buf[5])*256) + (ord($buf[6])* 256*256);
		 $this->StartIp=ord($buf[0]) + (ord($buf[1])*256) + (ord($buf[2])*256*256) + (ord($buf[3])*256*256*256);
		 return $this->StartIp;
    }

    function getEndIp(){
		 @fseek ( $this->fp , $this->EndIpOff , SEEK_SET ) ;
		 $buf=fread ( $this->fp , 5 ) ;
		 $this->EndIp=ord($buf[0]) + (ord($buf[1])*256) + (ord($buf[2])*256*256) + (ord($buf[3])*256*256*256);
		 $this->CountryFlag=ord ( $buf[4] ) ;
		 return $this->EndIp ;
		}
	
		function getCountry(){
		 switch ( $this->CountryFlag ) {
			case 1:
			case 2:
			 $this->Country=$this->getFlagStr ( $this->EndIpOff+4) ;
			 //echo sprintf('EndIpOffset=(%x)',$this->EndIpOff );
			 $this->Local=( 1 == $this->CountryFlag )? '' : $this->getFlagStr ( $this->EndIpOff+8);
			 break ;
			default :
			 $this->Country=$this->getFlagStr ($this->EndIpOff+4) ;
			 $this->Local=$this->getFlagStr ( ftell ( $this->fp )) ;
		 }
    }

    function getFlagStr ($offset){
		 $flag=0 ;
		 while(1){
			@fseek($this->fp ,$offset,SEEK_SET) ;
			$flag=ord(fgetc($this->fp ) ) ;
			if ( $flag == 1 || $flag == 2 ) {
			 $buf=fread ($this->fp , 3 ) ;
			 if ($flag==2){
				$this->CountryFlag=2;
				$this->EndIpOff=$offset - 4 ;
			 }
			 $offset=ord($buf[0]) + (ord($buf[1])*256) + (ord($buf[2])* 256*256);
			}
			else{
			 break ;
			}
	
		 }
		 if($offset<12)
			return '';
		 @fseek($this->fp , $offset , SEEK_SET ) ;
	
		 return $this->getStr();
    }

    function getStr ( )
    {
		 $str='' ;
		 while ( 1 ) {
			$c=fgetc ( $this->fp ) ;
			//echo "$c\n" ;
	
			if(ord($c[0])== 0 )
			 break ;
			$str.= $c ;
		 }
		 //echo "$str \n";
		 return $str ;
    }
	
    function qqwry ($dotip='') {
        if(!$dotip)return;
            if(ereg("^(127)",$dotip)){$this->Country='本地网络';return;}
            elseif(ereg("^(192)",$dotip)) {$this->Country='局域网';return;}
		 $nRet="";
		 $ip=$this->IpToInt ( $dotip );
		 $this->fp= fopen($this->datfile, "rb");
		 if ($this->fp == NULL) {
			 $szLocal= "OpenFileError";
			return 1;
	
		 }
		 @fseek ( $this->fp , 0 , SEEK_SET ) ;
		 $buf=fread ( $this->fp , 8 ) ;
		 $this->FirstStartIp=ord($buf[0]) + (ord($buf[1])*256) + (ord($buf[2])*256*256) + (ord($buf[3])*256*256*256);
		 $this->LastStartIp=ord($buf[4]) + (ord($buf[5])*256) + (ord($buf[6])*256*256) + (ord($buf[7])*256*256*256);
	
		 $RecordCount= floor( ( $this->LastStartIp - $this->FirstStartIp ) / 7);
		 if ($RecordCount <= 1){
			$this->Country="FileDataError";
			fclose($this->fp) ;
			return 2 ;
		 }
	
		 $RangB= 0;
		 $RangE= $RecordCount;
		 // Match ...
		 while ($RangB < $RangE-1)
		 {
		 $RecNo= floor(($RangB + $RangE) / 2);
		 $this->getStartIp ( $RecNo ) ;
	
			if ( $ip == $this->StartIp )
			{
			 $RangB=$RecNo ;
			 break ;
			}
		 if ($ip>$this->StartIp)
			$RangB= $RecNo;
		 else
			$RangE= $RecNo;
		 }
		 $this->getStartIp ( $RangB ) ;
		 $this->getEndIp ( ) ;
	
		 if ( ( $this->StartIp <= $ip ) && ( $this->EndIp >= $ip ) ){
			$nRet=0 ;
			$this->getCountry ( ) ;
			//这样不太好..............所以..........
			$this->Local=str_replace("（我们一定要解放台湾！！！）", "", $this->Local);
		 }
		 else{
			$nRet=3 ;
			$this->Country='未知' ;
			$this->Local='' ;
		 }
		 fclose ( $this->fp );
		$this->Country=preg_replace("/(CZ88.NET)|(纯真网络)/","",$this->Country);
		$this->Local=preg_replace("/(CZ88.NET)|(纯真网络)|(对方和您在同一内部网)/","",$this->Local);
	
		 //////////////看看 $nRet在上面的值是什么0和3，于是将下面的行注释掉
			return $nRet ;
	
		 //return "$this->Country $this->Local";#如此直接返回位置和国家便可以了
    }

    function IpToInt($Ip) {
		 $array=explode('.',$Ip);
		 for($k=0;$k<4;$k++) if(!isset($array[$k])) $array[$k]=0;
		 $Int=($array[0] * 256*256*256) + ($array[1]*256*256) + ($array[2]*256) + $array[3];
		 return $Int;
    }
	function IntToIp($Int) {
		$b1=($Int & 0xff000000)>>24;
		if ($b1<0) $b1+=0x100;
		$b2=($Int & 0x00ff0000)>>16;
		if ($b2<0) $b2+=0x100;
		$b3=($Int & 0x0000ff00)>>8;
		if ($b3<0) $b3+=0x100;
		$b4= $Int & 0x000000ff;
		if ($b4<0) $b4+=0x100;
		$Ip=$b1.'.'.$b2.'.'.$b3.'.'.$b4;
		return $Ip;
    }
}
?>