<?php
function JsOutputFormat( $str )
{
				$str = trim( $str );
				$str = str_replace( "\\s\\s", "\\s", $str );
				$str = str_replace( "\r", "", $str );
				$str = str_replace( "\n", "", $str );
				$str = str_replace( "\t", "", $str );
				$str = str_replace( "\\", "\\\\", $str );
				$str = str_replace( "\"", "\\\"", $str );
				$str = str_replace( "\\'", "\\\\'", $str );
				return $str;
}

require_once( "init.php" );
require_once( "js.config.php" );
require_once( KTPL_DIR."kTemplate.class.php" );
$TPL = new kTemplate( );
$TPL->template_dir = SYS_PATH."templates/";
$TPL->compile_dir = CACHE_DIR."templates_c/";
$TPL->cache_dir = CACHE_DIR."cache/";
$id = empty( $_GET['id'] ) ? "new" : $_GET['id'];
if ( !array_key_exists( $id, $templateKeys ) )
{
				exit( "document.write(\"Error! Invalid Template Key.\");" );
}
$cacheId = $id.intval( $_GET['IndexID'] ).intval( $_GET['ContentID'] ).intval( $_GET['NodeID'] ).intval( $_GET['TableID'] );
$tplname =& $templateKeys[$id];
if ( $TPL->is_cached( $tplname, $cacheId ) )
{
				echo "document.write(\"";
				$TPL->display_cache( $tplname, $cacheId );
				echo "\");";
}
else
{
				if ( isset( $_GET['IndexID'] ) )
				{
								$TPL->assign( "IndexID", intval( $_GET['IndexID'] ) );
				}
				if ( isset( $_GET['ContentID'] ) )
				{
								$TPL->assign( "ContentID", intval( $_GET['ContentID'] ) );
				}
				if ( isset( $_GET['NodeID'] ) )
				{
								$TPL->assign( "NodeID", intval( $_GET['NodeID'] ) );
				}
				if ( isset( $_GET['TableID'] ) )
				{
								$TPL->assign( "TableID", intval( $_GET['TableID'] ) );
				}
				require_once( INCLUDE_PATH."data.class.php" );
				require_once( INCLUDE_PATH."data.remote.class.php" );
				require_once( INCLUDE_PATH."functions.php" );
				require_once( INCLUDE_PATH."image.class.php" );
				require_once( INCLUDE_PATH."file.class.php" );
				if ( !extension_loaded( "ftp" ) )
				{
								require_once( INCLUDE_PATH."ftp.class.php" );
				}
				require_once( INCLUDE_PATH."Error.php" );
				require_once( INCLUDE_PATH."exception.class.php" );
				require_once( INCLUDE_PATH."admin/psn_admin.class.php" );
				require_once( INCLUDE_PATH."admin/dsn_admin.class.php" );
				include_once( SETTING_DIR."cms.ini.php" );
				include_once( CACHE_DIR."Cache_SYS_ENV.php" );
				include_once( CACHE_DIR."Cache_PSN.php" );
				include_once( CACHE_DIR."Cache_CateList.php" );
				require( SYS_PATH."license.php" );
				$SYS_ENV['CMSware_Mark'] = "";
				require_once( INCLUDE_PATH."admin/publishAdmin.class.php" );
				require_once( INCLUDE_PATH."admin/content_table_admin.class.php" );
				require_once( INCLUDE_PATH."admin/tplAdmin.class.php" );
				require_once( INCLUDE_PATH."admin/psn_admin.class.php" );
				require_once( INCLUDE_PATH."admin/site_admin.class.php" );
				require_once( INCLUDE_PATH."admin/dsn_admin.class.php" );
				require_once( INCLUDE_PATH."cms.class.php" );
				require_once( INCLUDE_PATH."cms.func.php" );
				require_once( KDB_DIR."kDB.php" );
				if ( !class_exists( "TplVarsAdmin" ) )
				{
								require_once( INCLUDE_PATH."admin/TplVarsAdmin.class.php" );
				}
				require_once( LIB_PATH."Spring.php" );
				$BeanFactory = new Spring( "spring.appcontext.php" );
				include_once( SETTING_DIR."global.php" );
				$bFactory =& Spring::getinstance( "spring.appcontext.php" );
				$settingCache =& $bFactory->getBean( "SettingCache" );
				$OAS_SETTING = array_merge( $OAS_SETTING, $settingCache->load( "plugin_oas_setting" ) );
				$OAS_SETTING['CWPS_RootURL'] = substr( $OAS_SETTING['CWPS_RootURL'], -1 ) != "/" ? $OAS_SETTING['CWPS_RootURL']."/" : $OAS_SETTING['CWPS_RootURL'];
				$OAS_SETTING['OAS_RootURL'] = substr( $OAS_SETTING['OAS_RootURL'], -1 ) != "/" ? $OAS_SETTING['OAS_RootURL']."/" : $OAS_SETTING['OAS_RootURL'];
				$TPL->assign( "CWPS_URL", $OAS_SETTING['CWPS_RootURL'] );
				$TPL->assign( "OAS_URL", $OAS_SETTING['OAS_RootURL'] );
				$TPL->assign_by_ref( "OAS_SETTING", $OAS_SETTING );
				require_once( SYS_PATH."config.php" );
				$db = new kDB( $db_config['db_driver'] );
				$db->connect( $db_config );
				$db->setFetchMode( "assoc" );
				$db->setDebug( $db_config['debug'] );
				$db->setCacheDir( SYS_PATH."sysdata/cache/" );
				$iWPC = new iWPC( );
				$tpl_vars = TplVarsAdmin::getall( );
				foreach ( $tpl_vars as $key => $var )
				{
								if ( $var['IsGlobal'] )
								{
												$TPL->assign( $var['VarName'], $var['VarValue'] );
								}
				}
				$TPL->registerPreFilter( "CMS_Parser" );
				$TPL->registerCacheFun( "JsOutputFormat" );
				echo "document.write(\"";
				$TPL->display_cache( $tplname, $cacheId );
				echo "\");";
				$db->close( );
}
?>
