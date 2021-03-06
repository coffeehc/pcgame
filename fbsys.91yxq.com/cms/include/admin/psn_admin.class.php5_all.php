<?php
class psn_admin extends iData
{

				public $isLog = true;
				public $conn_id_stack = array( );

				public function add( )
				{
								global $table;
								if ( $this->dataInsert( $table->psn ) )
								{
												return true;
								}
								else
								{
												return false;
								}
				}

				public function del( $PSNID )
				{
								global $table;
								$which = "PSNID";
								if ( $this->dataDel( $table->psn, $which, $PSNID, $method = "=" ) )
								{
												return true;
								}
								else
								{
												return false;
								}
				}

				public static function isValid( )
				{
								global $table;
								global $db;
								global $PSN_INFO;
								$i = 0;
								require( SYS_PATH."/license.php" );
								$license_array = $License;
								unset( $License );
								if ( !empty( $PSN_INFO ) )
								{
												$allPsn = $PSN_INFO;
								}
								else
								{
												$allPsn = psn_admin::getallpsn( );
								}
								foreach ( $allPsn as $key => $var )
								{
												$psnInfo = psn_admin::parsepsn( $var['PSN'] );
												if ( $psnInfo[publish_type] == "ftp" )
												{
																++$i;
												}
								}
								if ( $license_array['RemotePSN-num'] < $i )
								{
												goback( "license_RemotePSN_num_overflow" );
								}
				}

				public function update( $PSNID )
				{
								global $table;
								$where = "where PSNID=".$PSNID;
								if ( $this->dataUpdate( $table->psn, $where ) )
								{
												return true;
								}
								else
								{
												return false;
								}
				}

				public function getPSNInfo( $PSNID )
				{
								global $table;
								global $db;
								global $PSN_INFO;
								$sql = "SELECT * FROM {$table->psn}  WHERE PSNID='{$PSNID}'";
								$result = $db->getRow( $sql );
								return $result;
				}

				public static function getAllPSN( )
				{
								global $table;
								global $db;
								global $PSN_INFO;
								$sql = "SELECT * FROM {$table->psn} order by PSNID asc";
								$result = $db->Execute( $sql );
								while ( !$result->EOF )
								{
												$data[] = $result->fields;
												$result->MoveNext( );
								}
								return $data;
				}

				public function getAllPSNByPermission( )
				{
								global $table;
								global $db;
								global $PSN_INFO;
								global $sys;
								$sql = "SELECT * FROM {$table->psn}";
								$result = $db->Execute( $sql );
								while ( !$result->EOF )
								{
												if ( $this->canPermissionReadG( $result->fields, $sys->session['sGId'] ) )
												{
																$data[] = $result->fields;
												}
												$result->MoveNext( );
								}
								return $data;
				}

				public function canPermissionReadG( $PSNInfo, $gId )
				{
								global $db;
								global $table;
								global $sys;
								if ( $sys->isAdmin( ) )
								{
												return true;
								}
								if ( !empty( $PSNInfo['PermissionReadG'] ) )
								{
												$posMG = strpos( ",".$PSNInfo['PermissionReadG'].",", ",".$gId."," );
												if ( $posMG === false )
												{
																$result = $db->getRow( "select ParentGID from {$table->group} where gId='{$gId}'" );
																if ( empty( $result['ParentGID'] ) )
																{
																				return false;
																}
																else
																{
																				return $this->canPermissionReadG( $PSNInfo, $result['ParentGID'] );
																}
												}
												else
												{
																return true;
												}
								}
								else
								{
												return false;
								}
				}

				public static function parsePSN( $psn )
				{
								$patt = "/file::(.*)/si";
								if ( preg_match( $patt, $psn, $matches ) )
								{
												$output[publish_path] = $matches[1];
												$output[publish_type] = "local";
												return $output;
								}
								$patt = "/relate::(.*)/si";
								if ( preg_match( $patt, $psn, $matches ) )
								{
												$output[publish_path] = $matches[1];
												$output[publish_type] = "local";
												return $output;
								}
								$patt = "/ftp::([^:]+):(.*)@([a-zA-Z0-9-_\\.]+):([0-9]+)(.*)/si";
								if ( preg_match( $patt, $psn, $matches ) )
								{
												$output[publish_path] = $matches[5];
												$output[publish_type] = "ftp";
												$output[publish_ftp_host] = $matches[3];
												$output[publish_ftp_port] = $matches[4];
												$output[publish_ftp_user] = $matches[1];
												$output[publish_ftp_pass] = $matches[2];
												return $output;
								}
				}

				public function haveSon( $NodeID )
				{
								global $table;
								global $db;
								$sql = "SELECT count(*) as nr FROM {$table->site}  WHERE cFId='{$NodeID}'  AND disabled=0";
								$result = $db->getRow( $sql );
								if ( 0 < $result[nr] )
								{
												return true;
								}
								else
								{
												return false;
								}
				}

				public function getAll4Tree( $cFId = 0 )
				{
								global $table;
								global $db;
								$sql = "SELECT NodeID,cName,cFId FROM {$table->site} where cFId={$cFId}  AND disabled=0";
								$result = $db->Execute( $sql );
								while ( !$result->EOF )
								{
												if ( $this->haveSon( $result->fields[NodeID] ) )
												{
																$haveSon = 1;
												}
												else
												{
																$haveSon = 0;
												}
												$data[] = array(
																"NodeID" => $result->fields[NodeID],
																"cName" => $result->fields[cName],
																"cFId" => $result->fields[cFId],
																"haveSon" => $haveSon
												);
												$result->MoveNext( );
								}
								return $data;
				}

				public function connect( $PSN )
				{
								$psnInfo = $this->parsePSN( $PSN );
								$this->PSN = $PSN;
								$this->psnInfo = $psnInfo;
								switch ( $psnInfo[publish_type] )
								{
								case "local" :
												if ( is_dir( $psnInfo[publish_path] ) )
												{
																return true;
												}
												else
												{
																return false;
												}
								case "ftp" :
												$this->conn_id_key = md5( $PSN );
												if ( is_resource( $this->conn_id[$this->conn_id_key] ) )
												{
																return true;
												}
												else
												{
																return $this->_ftp_connect( );
												}
								}
				}

				public function close( )
				{
								switch ( $this->psnInfo[publish_type] )
								{
								case "local" :
												return true;
								case "ftp" :
												return $this->_ftp_close( );
								}
				}

				public function _ftp_close( )
				{
								return true;
				}

				public function _ftp_connect( )
				{
								if ( function_exists( "ftp_connect" ) )
								{
												$mode = decoct( $mode );
												if ( strlen( $mode ) == 4 )
												{
																$mode = substr( $mode, 1 );
												}
												$this->conn_id[$this->conn_id_key] = ftp_connect( $this->psnInfo['publish_ftp_host'], $this->psnInfo['publish_ftp_port'], 100 );
												$login_result = @ftp_login( $this->conn_id[$this->conn_id_key], $this->psnInfo['publish_ftp_user'], $this->psnInfo['publish_ftp_pass'] );
												if ( !$this->conn_id[$this->conn_id_key] || !$login_result )
												{
																echo "<font color=red>FTP connection has failed!</font><br>Attempted to connect to {$this->psnInfo['publish_ftp_host']}:{$this->psnInfo['publish_ftp_port']} for user {$this->psnInfo['publish_ftp_user']}.<br>";
																echo "Please reset you FTP accounts correctly in your iWPC system setting.";
																exit( );
												}
												else
												{
																return true;
												}
								}
								else
								{
												echo "The FTP module can not found,Please contact to you web administrator to install it";
												return false;
								}
				}

				public function listFile( $path = NULL )
				{
								switch ( $this->psnInfo[publish_type] )
								{
								case "local" :
												return $this->_list( $path );
								case "ftp" :
												return $this->_ftp_list( $path );
								}
				}

				public function mkDir( $dirname, $mode = 511 )
				{
								global $SYS_CONFIG;
								if ( !empty( $SYS_CONFIG['dir_mode'] ) )
								{
												$mode = $SYS_CONFIG['dir_mode'];
								}
								switch ( $this->psnInfo[publish_type] )
								{
								case "local" :
												return $this->_mkdir( $dirname, $mode );
								case "ftp" :
												return $this->_ftp_mkdir( $dirname, $mode );
								}
				}

				public function _mkdir( $dirname, $mode )
				{
								if ( File::automkdir( $this->psnInfo[publish_path]."/".$dirname, $mode ) )
								{
												return true;
								}
								else
								{
												return false;
								}
				}

				public function _ftp_mkdir( $dirname, $mode )
				{
								if ( ftp_mkdir( $this->conn_id[$this->conn_id_key], $this->psnInfo[publish_path]."/".$dirname ) )
								{
												ftp_site( $this->conn_id[$this->conn_id_key], "CHMOD {$mode} ".$this->psnInfo[publish_path]."/".$dirname );
												return true;
								}
								else
								{
												return false;
								}
				}

				public function rmDir( $dirname )
				{
								switch ( $this->psnInfo[publish_type] )
								{
								case "local" :
												return $this->_rmdir( $dirname );
								case "ftp" :
												return $this->_ftp_rmdir( $dirname );
								}
				}

				public function _rmdir( $dirname )
				{
								return rmdir( $this->psnInfo[publish_path]."/".$dirname );
				}

				public function _ftp_rmdir( $dirname )
				{
								return ftp_rmdir( $this->conn_id[$this->conn_id_key], $this->psnInfo[publish_path]."/".$dirname );
				}

				public function delFile( $path, $filename )
				{
								switch ( $this->psnInfo[publish_type] )
								{
								case "local" :
												return $this->_delFile( $path, $filename );
								case "ftp" :
												return $this->_ftp_delFile( $path, $filename );
								}
				}

				public function _delFile( $path, $filename )
				{
								$deler = $this->psnInfo[publish_path].$path."/".$filename;
								if ( file_exists( $deler ) )
								{
												if ( unlink( $deler ) )
												{
																return true;
												}
												else
												{
																return false;
												}
								}
								else
								{
												return true;
								}
				}

				public function _ftp_delFile( $path, $filename )
				{
								if ( ftp_delete( $this->conn_id[$this->conn_id_key], $this->psnInfo[publish_path].$path."/".$filename ) )
								{
												return true;
								}
								else
								{
												return false;
								}
				}

				public function move( $filename, $destination )
				{
								switch ( $this->psnInfo[publish_type] )
								{
								case "local" :
												return $this->_move( $filename, $destination );
								case "ftp" :
												return $this->_ftp_move( $filename, $destination );
								}
				}

				public function _move( $filename, $destination )
				{
								if ( File::autocopy( $this->psnInfo[publish_path].$filename, $this->psnInfo[publish_path].$destination ) )
								{
												unlink( $this->psnInfo[publish_path].$filename );
												return true;
								}
								else
								{
												return false;
								}
				}

				public function _ftp_move( $filename, $destination )
				{
								if ( ftp_exec( $this->conn_id[$this->conn_id_key], "RNFR ".$this->psnInfo[publish_path].$filename ) && ftp_exec( $this->conn_id[$this->conn_id_key], "RNTO ".$this->psnInfo[publish_path].$destination ) )
								{
												return true;
								}
								else
								{
												return false;
								}
				}

				public function cp( $filename, $destination )
				{
								switch ( $this->psnInfo[publish_type] )
								{
								case "local" :
												return $this->_cp( $filename, $destination );
								case "ftp" :
												return $this->_ftp_cp( $filename, $destination );
								}
				}

				public function _cp( $filename, $destination )
				{
								if ( File::autocopy( $this->psnInfo[publish_path].$filename, $this->psnInfo[publish_path].$destination ) )
								{
												return true;
								}
								else
								{
												return false;
								}
				}

				public function _ftp_cp( $filename, $destination )
				{
								return false;
				}

				public function renameFile( $filename, $destination )
				{
								switch ( $this->psnInfo[publish_type] )
								{
								case "local" :
												return $this->_renameFile( $filename, $destination );
								case "ftp" :
												return $this->_ftp_renameFile( $filename, $destination );
								}
				}

				public function _renameFile( $filename, $destination )
				{
								return rename( $this->psnInfo[publish_path].$filename, $this->psnInfo[publish_path].$destination );
				}

				public function _ftp_renameFile( $filename, $destination )
				{
								return ftp_rename( $this->conn_id[$this->conn_id_key], $this->psnInfo[publish_path].$filename, $this->psnInfo[publish_path].$destination );
				}

				public function upload( $filename, $destination, $URL = "" )
				{
								switch ( $this->psnInfo[publish_type] )
								{
								case "local" :
												return $this->_upload( $filename, $destination, $URL );
								case "ftp" :
												return $this->_ftp_upload( $filename, $destination, $URL );
								}
				}

				public function _upload( $filename, $destination, $URL = "" )
				{
								global $SYS_CONFIG;
								$mode = empty( $SYS_CONFIG['file_mode'] ) ? 511 : $SYS_CONFIG['file_mode'];
								$path = $this->psnInfo[publish_path].$destination;
								$pathInfo = pathinfo( $path );
								cmsware_mkdir( $pathInfo['dirname'] );
								if ( File::autocopy( $filename, $path ) )
								{
												if ( $this->isLog )
												{
																$this->logIt( $destination, "binary", $URL );
												}
												return true;
								}
								else
								{
												return false;
								}
				}

				public function _ftp_upload( $filename, $destination, $URL = "" )
				{
								$path = $this->psnInfo[publish_path].$destination;
								$pathInfo = pathinfo( $path );
								$this->ftpMakeDir( $pathInfo['dirname'] );
								/*
								 * 同步 cdn
								 */
								/*
								if(strstr($path,'image.91wan.com') || strstr($path,'image.2918.com')')){
									$cdn_path = "http:/".$path;
									exec("/usr/local/php/bin/php /www/server/cms/sync.php  {$cdn_path} > /dev/null &");

								}
								*/
								// 当发布点psn包含这些91wan.com或2918.com时
								if(strstr($path,'91wan.com') || strstr($path,'.2918.com')){
									//文件路径
									$cdn_path = "http:/".$path;
									//91wan二级域名的官网放在根目录下，故作替换
									require_once '/www/server/cms/game_web.php';
									$cdn_path = preg_replace($game_web,$game_web2,$cdn_path);
									exec("/usr/local/php/bin/php /www/server/cms/sync.php  {$cdn_path} > /dev/null &");
									
									//同时推送index.html目录 当作文件推送
									preg_match('/(http:\/\/(www|[a-z0-9]+).(2918|91wan).com\/[a-z0-9\/]{0,50})index.html/',$cdn_path,$m);
									if($m[1]){exec("/usr/local/php/bin/php /www/server/cms/sync.php  {$m[1]} > /dev/null &");}
								}else{ 
								    //由于www.2918.com的ftp路径没有设置的，故在这里匹配发布URL再对www.2918.com目录下再判断
									global $PSN_INFO;
									$p = $this->psnInfo;
									
									$PSNURL = $p['publish_type'].'::'.$p['publish_ftp_user'].':'.$p['publish_ftp_pass'].'@'.$p['publish_ftp_host'].':'.$p['publish_ftp_port'].$p['publish_path'];
									foreach($PSN_INFO as $key=>$val){
										if($val['PSN']==$PSNURL){
											$url = $val['URL'];
											break;
										}
									}
									$cdn_path = $url.$path;
									if(strstr($cdn_path,'www.2918.com')){
										exec("/usr/local/php/bin/php /www/server/cms/sync.php  {$cdn_path} > /dev/null &");
										
										//同时推送index.html目录 当作文件推送
										preg_match('/(http:\/\/(www|[a-z0-9]+).(2918|91wan).com\/[a-z0-9\/]{0,50})index.html/',$cdn_path,$m);
										if($m[1]){exec("/usr/local/php/bin/php /www/server/cms/sync.php  {$m[1]} > /dev/null &");}
									}
								}

								if ( ftp_put( $this->conn_id[$this->conn_id_key], $path, $filename, FTP_BINARY ) )
								{
												if ( $this->isLog )
												{
																$this->logIt( $destination, "binary", $URL );
												}

												return true;
								}
								else
								{
												return false;
								}
				}

				public function fileExists( $filename )
				{
								switch ( $this->psnInfo[publish_type] )
								{
								case "local" :
												return $this->_file_exists( $filename );
								case "ftp" :
												return $this->_ftp_file_exists( $filename );
								}
				}

				public function _file_exists( $filename )
				{
								return file_exists( $this->psnInfo[publish_path]."/".$filename );
				}

				public function _ftp_file_exists( $filename )
				{
								return ftp_rename( $this->conn_id[$this->conn_id_key], $this->psnInfo[publish_path]."/".$filename, $this->psnInfo[publish_path]."/".$filename );
				}

				public function read( $path, $filename )
				{
								switch ( $this->psnInfo[publish_type] )
								{
								case "local" :
												return $this->_read( $path, $filename );
								case "ftp" :
												return $this->_ftp_read( $path, $filename );
								}
				}

				public function _read( $path, $filename )
				{
								$Path2Get = $this->psnInfo[publish_path].$path."/".$filename;
								return getfile( $Path2Get );
				}

				public function _ftp_read( $path, $filename )
				{
								$tmpfilename = CACHE_DIR.Auth::makesessionkey( );
								$remote_file = $this->psnInfo[publish_path].$path."/".$filename;
								if ( ftp_get( $this->conn_id[$this->conn_id_key], $tmpfilename, $remote_file, FTP_ASCII ) )
								{
												if ( $content = getfile( $tmpfilename ) )
												{
																unlink( $tmpfilename );
																return $content;
												}
												else
												{
																return false;
												}
								}
								else
								{
												return false;
								}
				}

				public function put( $filename, &$content )
				{
								$wap_mark = "<wap>";
								if ( preg_match( "/<wap>(.*)<\\/wap>/isU", $content, $match ) )
								{
												$function = $match[1];
												$content = $function( $content );
												$content = str_replace( $match[0], "", $content );
												$content = str_replace( "<%xml", "<?xml", $content );
												$content = str_replace( "%>", "?>", $content );
								}
								switch ( $this->psnInfo[publish_type] )
								{
								case "local" :
												return $this->_put( $filename, $content );
								case "ftp" :
												return $this->_ftp_put( $filename, $content );
								}
				}

				public function _put( $filename, &$content )
				{
								global $SYS_CONFIG;
								$mode = empty( $SYS_CONFIG['file_mode'] ) ? 511 : $SYS_CONFIG['file_mode'];
								$path = $this->psnInfo[publish_path].$filename;
								$pathInfo = pathinfo( $path );
								cmsware_mkdir( $pathInfo['dirname'] );
								if ( File::autowrite( $path, $content ) )
								{
												if ( $this->isLog )
												{
																$this->logIt( $filename, "text" );
												}
												return true;
								}
								else
								{
												return false;
								}
				}

				public function _ftp_put( $filename, &$content )
				{
								$path = $this->psnInfo[publish_path].$filename;
								/**
								 * 所有文件必经这里上传，故在这对文件更新
								 * 同步 cdn
								 */
								/*
								if(strstr($path,'image.91wan.com')){ //91wan

									$cdn_path = "http:/".$path;
									exec("/usr/local/php/bin/php /www/server/cms/sync.php  {$cdn_path} > /dev/null &");

								}elseif(strstr($path,'image.2918.com')){// 2918

									$cdn_path = "http:/".$path;
									exec("/usr/local/php/bin/php /www/server/cms/sync_2918.php  {$cdn_path} > /dev/null &");

								}
								*/	
								// 当发布点psn包含这些91wan.com或2918.com时
								if(strstr($path,'91wan.com') || strstr($path,'.2918.com')){
									//文件路径
									$cdn_path = "http:/".$path;
									//91wan二级域名的官网放在根目录下，故作替换
									require_once '/www/server/cms/game_web.php';
									$cdn_path = preg_replace($game_web,$game_web2,$cdn_path);
									exec("/usr/local/php/bin/php /www/server/cms/sync.php  {$cdn_path} > /dev/null &");
									
									//同时推送index.html目录 当作文件推送
									preg_match('/(http:\/\/(www|[a-z0-9]+).(2918|91wan).com\/[a-z0-9\/]{0,50})index.html/',$cdn_path,$m);
									if($m[1]){exec("/usr/local/php/bin/php /www/server/cms/sync.php  {$m[1]} > /dev/null &");}
								}else{ 
								    //由于www.2918.com的ftp路径没有设置的，故在这里匹配发布URL再对www.2918.com目录下再判断
									global $PSN_INFO;
									$p = $this->psnInfo;
									
									$PSNURL = $p['publish_type'].'::'.$p['publish_ftp_user'].':'.$p['publish_ftp_pass'].'@'.$p['publish_ftp_host'].':'.$p['publish_ftp_port'].$p['publish_path'];
									foreach($PSN_INFO as $key=>$val){
										if($val['PSN']==$PSNURL){
											$url = $val['URL'];
											break;
										}
									}
									$cdn_path = $url.$path;
									if(strstr($cdn_path,'www.2918.com')){
										exec("/usr/local/php/bin/php /www/server/cms/sync.php  {$cdn_path} > /dev/null &");
										
										//同时推送index.html目录 当作文件推送
										preg_match('/(http:\/\/(www|[a-z0-9]+).(2918|91wan).com\/[a-z0-9\/]{0,50})index.html/',$cdn_path,$m);
										if($m[1]){exec("/usr/local/php/bin/php /www/server/cms/sync.php  {$m[1]} > /dev/null &");}
									}
								}
								$pathInfo = pathinfo( $path );
								$this->ftpMakeDir( $pathInfo['dirname'] );
								$tmpFile = $this->makeTmpFile( $content );
								//$tmpFile = "http:/".$path;
								if ( ftp_put( $this->conn_id[$this->conn_id_key], $path, $tmpFile, FTP_ASCII ) )
								{
												if ( $this->isLog )
												{
																$this->logIt( $filename, "text" );
												}
												@unlink( $tmpFile );
												return true;
								}
								else
								{
												@unlink( $tmpFile );
												return false;
								}
				}

				public function logIt( $FileName, $Type, $URL = "" )
				{
								global $table;
								$this->flushData( );
								$this->addData( "ContentID", $this->sendVar[ContentID] );
								$this->addData( "NodeID", $this->sendVar[NodeID] );
								$this->addData( "PSN", $this->PSN );
								$this->addData( "FileName", $FileName );
								$this->addData( "Type", $Type );
								$this->addData( "URL", $URL );
								if ( $this->logExits( $this->sendVar[ContentID], $this->PSN, $FileName ) )
								{
												return true;
								}
								else if ( $this->dataInsert( $table->publish_log ) )
								{
												return true;
								}
								else
								{
												return false;
								}
				}

				public function logExits( $ContentID, $PSN, $FileName )
				{
								global $db;
								global $table;
								global $CMS_CACHE_logExits;
								$CacheKey = md5( $ContentID.$PSN.$FileName );
								if ( isset( $CMS_CACHE_logExits[$CacheKey] ) )
								{
												return $CMS_CACHE_logExits[$CacheKey];
								}
								else
								{
												$sql = "SELECT logID  FROM {$table->publish_log}  WHERE ContentID={$ContentID} AND PSN='{$PSN}' AND FileName='{$FileName}'";
												$result = $db->getRow( $sql );
												if ( !empty( $result[logID] ) )
												{
																$CMS_CACHE_logExits[$CacheKey] = true;
																return true;
												}
												else
												{
																$CMS_CACHE_logExits[$CacheKey] = false;
																return false;
												}
								}
				}

				public function makeTmpFile( &$content )
				{
								$tmpfilename = CACHE_DIR.Auth::makesessionkey( );
								if ( writefile( $tmpfilename, $content ) )
								{
												return $tmpfilename;
								}
								else
								{
												return false;
								}
				}

				public function ftpMakeDir( $directory )
				{
								$pwd = ftp_pwd( $this->conn_id[$this->conn_id_key] );
								if ( @ftp_chdir( $this->conn_id[$this->conn_id_key], $directory ) )
								{
												@ftp_chdir( $this->conn_id[$this->conn_id_key], $pwd );
												return true;
								}
								$dirinfo = pathinfo( $directory );
								if ( !ftp_chdir( $this->conn_id[$this->conn_id_key], $dirinfo['dirname'] ) )
								{
												$pathInfo = explode( "/", $dirinfo['dirname'] );
												$basedir = "";
												foreach ( $pathInfo as $var )
												{
																if ( $var == "" )
																{
																}
																else if ( $this->ftpMakeDir( $dirinfo['dirname'] ) )
																{
																				echo "Repair {$basedir}{$var} OK<br>";
																}
																else
																{
																				echo "Repair {$basedir}{$var} Fail<br>";
																}
												}
								}
								if ( ftp_mkdir( $this->conn_id[$this->conn_id_key], $dirinfo['basename'] ) )
								{
												ftp_site( $this->conn_id[$this->conn_id_key], "CHMOD 777 ".$dirinfo['basename'] );
												return true;
								}
								else
								{
												return false;
								}
				}

				public function _list( $path )
				{
								$dirlist = array( );
								$filelist = array( );
								$dir = dir( $this->psnInfo[publish_path]."/".$path );
								$dir->rewind( );
								while ( $file = $dir->read( ) )
								{
												if ( $file == "." || $file == ".." )
												{
												}
												else if ( is_dir( $this->psnInfo[publish_path]."/".$path."/".$file ) )
												{
																$dirlist[] = array(
																				"type" => "dir",
																				"name" => $file,
																				"user" => $matches[4],
																				"group" => $matches[5],
																				"size" => ceil( filesize( $this->psnInfo[publish_path]."/".$path."/".$file ) / 1080 ),
																				"modifiedDate" => date( "Y-m-d H:i:s", filemtime( $this->psnInfo[publish_path]."/".$path."/".$file ) ),
																				"mode" => fileperms( $this->psnInfo[publish_path]."/".$path."/".$file )
																);
												}
												else
												{
																$arr = explode( ".", $file );
																$filelist[] = array(
																				"type" => array_pop( $arr ),
																				"name" => $file,
																				"user" => $matches[4],
																				"group" => $matches[5],
																				"size" => ceil( filesize( $this->psnInfo[publish_path]."/".$path."/".$file ) / 1080 ),
																				"modifiedDate" => date( "Y-m-d H:i:s", filemtime( $this->psnInfo[publish_path]."/".$path."/".$file ) ),
																				"mode" => fileperms( $this->psnInfo[publish_path]."/".$path."/".$file )
																);
												}
								}
								$filelist = array_merge( $dirlist, $filelist );
								$dir->close( );
								return $filelist;
				}

				public function _ftp_list( $path )
				{
								$type = ftp_systype( $this->conn_id[$this->conn_id_key] );
								$list = ftp_rawlist( $this->conn_id[$this->conn_id_key], $this->psnInfo[publish_path].$path );
								if ( $type == "UNIX" )
								{
												foreach ( $list as $key => $var )
												{
																$patt = "/^([d-])([rwx-]+)[\\s]+([\\S]+)[\\s]+([\\S]+)[\\s]+([\\S]+)[\\s]+([0-9]+)[\\s]+([A-Za-z]+[\\s]+[0-9]+[\\s]+[0-9]+:[0-9]+)[\\s]+([\\S]+)/is";
																preg_match( $patt, $var, $matches );
																if ( $matches[8] == "." || $matches[8] == ".." )
																{
																}
																else if ( $matches[1] == "d" )
																{
																				$dirlist[] = array(
																								"type" => "dir",
																								"name" => $matches[8],
																								"user" => $matches[4],
																								"group" => $matches[5],
																								"size" => ceil( $matches[6] / 1080 ),
																								"modifiedDate" => $matches[7],
																								"mode" => $matches[1].$matches[2]
																				);
																}
																else
																{
																				$arr = explode( ".", $matches[8] );
																				$filelist[] = array(
																								"type" => array_pop( $arr ),
																								"name" => $matches[8],
																								"size" => ceil( $matches[6] / 1080 ),
																								"user" => $matches[4],
																								"group" => $matches[5],
																								"modifiedDate" => $matches[7],
																								"mode" => $matches[1].$matches[2]
																				);
																}
												}
								}
								else
								{
												foreach ( $list as $key => $var )
												{
																$patt = "/^([d-])([rw-]+)[\\s]+([0-9]+)[\\s]+([\\S]+)[\\s]+([\\S]+)[\\s]+([0-9]+)[\\s]+([A-Za-z]+[\\s]+[0-9]+[\\s]+[0-9]+:[0-9]+)[\\s]+([\\S]+)/is";
																preg_match( $patt, $var, $matches );
																if ( $matches[8] == "." || $matches[8] == ".." )
																{
																}
																else if ( $matches[1] == "d" )
																{
																				$dirlist[] = array(
																								"type" => "dir",
																								"name" => $matches[8],
																								"user" => $matches[4],
																								"group" => $matches[5],
																								"size" => ceil( $matches[6] / 1024 ),
																								"modifiedDate" => $matches[7],
																								"mode" => $matches[1].$matches[2]
																				);
																}
																else
																{
																				$arr = explode( ".", $matches[8] );
																				$filelist[] = array(
																								"type" => array_pop( $arr ),
																								"name" => $matches[8],
																								"size" => ceil( $matches[6] / 1024 ),
																								"user" => $matches[4],
																								"group" => $matches[5],
																								"modifiedDate" => $matches[7],
																								"mode" => $matches[1].$matches[2]
																				);
																}
												}
								}
								$filelist = array_merge( $dirlist, $filelist );
								return $filelist;
				}

}

?>
