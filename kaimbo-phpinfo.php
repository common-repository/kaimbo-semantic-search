<?php 
	$wpdir=dirname( __FILE__ )."/../../../";
	require_once($wpdir."wp-load.php");
	
	if (current_user_can( 'manage_options' )) {
		phpinfo();
	}
	else {
		header("Location: ".ti_getWebsiteUrl());
	}
?>
