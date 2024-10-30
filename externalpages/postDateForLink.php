<?php 
	include('postIdForExternalUrl.php');

	$wpdir=getcwd()."/../../../..";
	require_once($wpdir."/wp-load.php");
	
	$id=kaimbo_getIDfromURL($_GET['url'],$_GET['title']);
	if ($id) {
		$wpost=get_post($id);
		echo $wpost->post_date;	
	}
?>
