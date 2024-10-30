<?php 
	if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit ();

	delete_option('kaimbo_wasFullyCrawled');
	delete_option('kaimbo_pagecount');
	
	require_once 'json.php';
	include('serverUrl.php');
	$ti_accesskey=get_option('kaimbo_accesskey');
	
	$toCall=$tisearch_serverUrl."unregister";
	$postStr="base=".urlencode(ti_getWebsiteUrl())."&accesskey=".$ti_accesskey;		
		
	tisearch_getJson($toCall.'?'.$postStr, '',10);
?>
