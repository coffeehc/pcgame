<?php
class AccessUser extends SqlMap
{

				public function AccessUser( )
				{
								$this->_basefile = __FILE__;
				}

				public function getRecordNum( )
				{
								$this->startTransaction( );
								$result = $this->queryForObject( "getRecordNum" );
								return $result['nr'];
				}

				public function getRecordLimit( $start = 0, $offset = 10, &$oas )
				{
								$this->startTransaction( );
								$this->addData( "start", $start );
								$this->addData( "offset", $offset );
								$userList = $this->queryForList( "getRecordLimit" );
								$userIds = "";
								foreach ( $userList as $key => $var )
								{
												if ( empty( $key ) )
												{
																$userIds = $var['OwnerID'];
												}
												else
												{
																$userIds .= ",".$var['OwnerID'];
												}
								}
								$oas->setTransactionID( time( ) );
								$oas->setDataEncode( false );
								$return = $oas->call( "GetUserListByUserIDs", array(
												"UserIDs" => $userIds
								) );
								if ( $return === false )
								{
												$oas->error( );
								}
								else
								{
												$returnUserList = $oas->unserialize( $return['List'] );
								}
								foreach ( $userList as $key => $var )
								{
												$userList[$key] = array_merge( $var, $returnUserList[$var['OwnerID']] );
								}
								return $userList;
				}

				public function getInfo( $aId, &$oas )
				{
								if ( empty( $aId ) )
								{
												return false;
								}
								$this->startTransaction( );
								$this->addData( "AccessID", $aId );
								$userInfo = $this->queryForObject( "getAccessInfoByAccessID" );
								$oas->setTransactionID( time( ) );
								$oas->setDataEncode( false );
								$params['UserID'] = $userInfo['OwnerID'];
								$return = $oas->call( "GetUserInfo", $params );
								if ( $return === false )
								{
												$oas->error( );
								}
								else
								{
												$userInfo = array_merge( $userInfo, $oas->unserialize( $return['Info'] ) );
								}
								$this->startTransaction( );
								$this->addData( "AccessID", $aId );
								$accessMap = $this->queryForList( "getAccessMapByAccessID" );
								foreach ( $accessMap as $var )
								{
												$userInfo[$var['PermissionKey']] = $var['AccessNodeIDs'];
								}
								return $userInfo;
				}

				public function userExists( $UserName, &$oas )
				{
								$oas->setTransactionID( time( ) );
								$return = $oas->call( "IsUserExists", array(
												"UserName" => $UserName
								) );
								if ( $return === false )
								{
												$oas->error( );
								}
								else if ( empty( $return['ok'] ) )
								{
												return false;
								}
								else
								{
												return $return['ok'];
								}
				}

				public function accessDefined( $UserName, &$oas )
				{
								$oas->setTransactionID( time( ) );
								$return = $oas->call( "IsUserExists", array(
												"UserName" => $UserName
								) );
								if ( $return === false )
								{
												$oas->error( );
								}
								else
								{
												$UserID = $return['ok'];
								}
								$this->startTransaction( );
								$this->addData( "UserID", $UserID );
								$accessInfo = $this->queryForObject( "getAccessInfoByUserID" );
								if ( empty( $accessInfo['AccessID'] ) )
								{
												return false;
								}
								else
								{
												return true;
								}
				}

}

?>
