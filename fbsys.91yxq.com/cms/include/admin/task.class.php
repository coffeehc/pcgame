<?php
class Task extends iData
{

				public $Tasks = NULL;

				public function refreshIndex( $NodeID, $window = "top.CrawlerTaskFrame.addThread" )
				{
								$params = array(
												"NodeID" => $NodeID
								);
								Task::addtask( "refreshIndex", $params, "refreshIndex".$NodeID, $window );
				}

				public function refreshExtra( $PublishID, $NodeID, $window = "top.CrawlerTaskFrame.addThread" )
				{
								$params = array(
												"NodeID" => $NodeID,
												"PublishID" => $PublishID
								);
								Task::addtask( "refreshExtra", $params, "refreshExtra".$PublishID, $window );
				}

				public function refreshIndexTree( $NodeID, $window = "top.CrawlerTaskFrame.addThread" )
				{
								global $iWPC;
								$NodeInfo = $iWPC->loadNodeInfo( $NodeID );
								$NodeIDs = explode( "%", $NodeInfo[ParentNodeID] );
								//debug($NodeInfo);
								foreach ( $NodeIDs as $key => $var )
								{
												if($var==20 || $var==659) continue;
												Task::refreshindex( $var, $window );
								}
				}

				public function refreshContent( $NodeID, $num, $window = "top.CrawlerTaskFrame.addThread" )
				{
								$params = array(
												"NodeID" => $NodeID,
												"offset" => $num
								);
								Task::addtask( "refreshContent", $params, "refreshContent".$NodeID, $window );
				}

				public function publishContent( $NodeID, $num, $window = "top.CrawlerTaskFrame.addThread" )
				{
								$params = array(
												"NodeID" => $NodeID,
												"offset" => $num
								);
								Task::addtask( "publishContent", $params, "publishContent".$NodeID, $window );
				}

				public function unpublishContent( $NodeID, $num, $window = "top.CrawlerTaskFrame.addThread" )
				{
								$params = array(
												"NodeID" => $NodeID,
												"offset" => $num
								);
								Task::addtask( "unpublishContent", $params, "unpublishContent".$NodeID, $window );
				}

				public function refreshSiteInit( $params )
				{
								global $iWPC;
								global $db;
								global $table;
								extract( $params, EXTR_PREFIX_SAME, "params_" );
								if ( $NodeID == 0 )
								{
												$result = $db->Execute( "select NodeID from {$table->site} where ParentID=0" );
												$NodeIDs = array( );
												while ( !$result->EOF )
												{
																$NodeInfo = $iWPC->loadNodeInfo( $result->fields[NodeID] );
																if ( $include_sub == 1 )
																{
																				$tmp = explode( "%", $NodeInfo[SubNodeID] );
																				$NodeIDs = array_merge( $NodeIDs, $tmp );
																}
																else
																{
																				$NodeIDs[] = $NodeID;
																}
																$result->MoveNext( );
												}
								}
								else
								{
												$NodeInfo = $iWPC->loadNodeInfo( $NodeID );
												if ( $include_sub == 1 )
												{
																$NodeIDs = explode( "%", $NodeInfo[SubNodeID] );
												}
												else
												{
																$NodeIDs[] = $NodeID;
												}
								}
								if ( $refresh_index == 1 )
								{
												foreach ( $NodeIDs as $var )
												{
																$Tasks[] = array(
																				"type" => "index",
																				"targetId" => $var
																);
												}
								}
								if ( $refresh_content == 1 )
								{
												foreach ( $NodeIDs as $var )
												{
																$Tasks[] = array(
																				"type" => "content",
																				"targetId" => $var
																);
												}
								}
								if ( $refresh_extra == 1 )
								{
												$extrapublish = new extra_publish_admin( );
												foreach ( $NodeIDs as $var )
												{
																$extraPublishList = $extrapublish->getAll( $var );
																if ( !empty( $extraPublishList ) )
																{
																				foreach ( $extraPublishList as $varIn )
																				{
																								$Tasks[] = array(
																												"type" => "extra",
																												"targetId" => $var,
																												"publishId" => $varIn['PublishID']
																								);
																				}
																}
												}
								}
								return $Tasks;
				}

				public function publishSiteInit( $params )
				{
								global $iWPC;
								global $db;
								global $table;
								extract( $params, EXTR_PREFIX_SAME, "params_" );
								if ( $NodeID == 0 )
								{
												$result = $db->Execute( "select NodeID from {$table->site} where ParentID=0" );
												$NodeIDs = array( );
												while ( !$result->EOF )
												{
																$NodeInfo = $iWPC->loadNodeInfo( $result->fields[NodeID] );
																if ( $include_sub == 1 )
																{
																				$tmp = explode( "%", $NodeInfo[SubNodeID] );
																				$NodeIDs = array_merge( $NodeIDs, $tmp );
																}
																else
																{
																				$NodeIDs[] = $NodeID;
																}
																$result->MoveNext( );
												}
								}
								else
								{
												$NodeInfo = $iWPC->loadNodeInfo( $NodeID );
												if ( $include_sub == 1 )
												{
																$NodeIDs = explode( "%", $NodeInfo[SubNodeID] );
												}
												else
												{
																$NodeIDs[] = $NodeID;
												}
								}
								foreach ( $NodeIDs as $var )
								{
												$Tasks[] = array(
																"type" => "content",
																"targetId" => $var
												);
								}
								return $Tasks;
				}

				public function addTask( $action, $params, $taskID, $window = "top.CrawlerTaskFrame.addThread" )
				{
								global $sys;
								foreach ( $params as $key => $var )
								{
												$query .= "&{$key}={$var}";
								}
								echo "<script language='JavaScript'>\n";
								echo $window."(\"admin_task.php?sId=".$sys->session['sId']."&o=".$action."&mode=running&TaskID=".$taskID.$query."\" , \"".$taskID."\");\n";
								echo "</script>";
				}

				public function addTaskInfo( $info, $taskID )
				{
								echo "<script language='JavaScript'>\n";
								echo "top.TaskInfoFrame.addInfo(\"".addslashes( $info )."\", '".$taskID."')\n";
								echo "</script>";
				}

				public function endTask( $taskID )
				{
								exit( "<script language='JavaScript'>parent.endThread('".$taskID."');</script>" );
				}

				public function taskSessionAdd( )
				{
								global $db;
								global $table;
								if ( $this->dataInsert( $table->tasks ) )
								{
												return true;
								}
								else
								{
												return false;
								}
				}

				public function taskSessionGet( $TaskID )
				{
								global $db;
								global $table;
								$result = $db->getRow( "SELECT TaskData FROM {$table->tasks} WHERE TaskID='".$TaskID."'" );
								return unserialize( stripslashes( $result['TaskData'] ) );
				}

				public function taskSessionUpdate( $TaskID )
				{
								global $db;
								global $table;
								$where = "where TaskID='".$TaskID."'";
								if ( $this->dataUpdate( $table->tasks, $where ) )
								{
												return true;
								}
								else
								{
												return false;
								}
				}

				public function taskSessionEnd( $TaskID )
				{
								global $db;
								global $table;
								return $db->query( "DELETE FROM {$table->tasks} WHERE TaskID='".$TaskID."'" );
				}

}

?>
