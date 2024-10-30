<?php
// this proxy is necessary only for the internet explorer, because it is not handling
// our cross-platform javascript requests to the search service.
// this way a local php request is sent, and the php asks the service on the other domain.

	$post='';
	if (array_key_exists('json',$_POST))
		$post=$_POST['json'];
	if (get_magic_quotes_gpc()) {
		$post=stripslashes($post);
	}	

	//it is very important, that this include comes after the megic_quotes_check !
	$wpdir=getcwd()."/../../..";
	//$wpdir=dirname( __FILE__ )."/../../..";	
	require_once($wpdir.'/wp-config.php');	
    require_once( $wpdir. '/wp-includes/wp-db.php' );

    $post=str_replace('-ACCESSKEY-',get_option('kaimbo_accesskey'),$post);		
	
	$urlpart='';
// 	$i=strpos($_SERVER['REQUEST_URI'],'ieproxy.php?url=');
// 	if ($i!==false) {
// 		$urlpart=substr($_SERVER['REQUEST_URI'],$i+16);
// 	}
// 	$urlpart=urldecode($urlpart);
	$urlpart=$_REQUEST['url'];
	$obj=tisearch_getJson($tisearch_serverUrl.$urlpart, $post,5);
	if (!is_wp_error($obj))
		echo $obj;
	  else echo $obj->get_error_message();
?>
