<?php 	
	$wpdir=getcwd()."/../../..";
	require_once($wpdir."/wp-load.php");
	
	$siteurl=get_site_url();
	
	$url=$_GET['url'];	
	$url=str_replace("?lang=en","",$url);
	$url=str_replace("?lang=de","",$url);
	$urlpath=substr($url,strlen($siteurl));
	if (!$urlpath[0]=="/") {
	  $urlpath="/".$urlpath;
	}

	//echo "$siteurl<br>";
 	//echo "$url<br>";
 	//echo "$urlpath<br>";
	
	if ($url==$siteurl || $url==$siteurl."/" || $url."/"==$siteurl) {
	    $id=get_option('page_on_front');	    
	    if ($id) {
	      echo $id;
	      return;
	    }
	}	
	
	$post=get_page_by_path($urlpath);	
	
	if ($post==null) {
		$id=url_to_postid($url);
		//echo $id;  		
	}
	else
		$id=$post->ID;  	
	
	if ($id==0) {
		$id="";
	}
	echo $id;
?>
