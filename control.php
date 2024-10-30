<?php 

//saving the post triggers to recrawl 
add_action( 'save_post', 'ti_triggerCrawl' );
function ti_triggerCrawl($post_id) {
	if ( !wp_is_post_revision( $post_id ) ) {
		$post_url = get_permalink( $post_id );

		$url=get_bloginfo('url');
		if ($url[strlen($url)-1]!='/')
			$url=$url.'/';
		global $tisearch_serverUrl;
		global $ti_accessKey;
		$toCall=$tisearch_serverUrl."recrawlurl";
		$postStr="base=".urlencode($url)."&accesskey=".$ti_accessKey."&currenturl=".urlencode($post_url);

		tisearch_getJson($toCall.'?'.$postStr, '');
	}
}

//the plugin is installed without an access key.
//when it is activated, registers itself to the search (and indexing) service.
function ti_checkSiteRegistration() {
	global $ti_accessKey; 
	if ($ti_accessKey!=false)
		return;
	
	if (kaimbo_checkIfLocal()) 
		return;	
	
	$url=ti_getWebsiteUrl();
	global $tisearch_serverUrl;
	$key=kaimbo_gen_uuid();
	
	tisearch_getJson($tisearch_serverUrl."errorLog?base=".ti_getWebsiteUrl()."&error=false","tries to save the accesskey option to: ".$key);
// 	$check=get_option('kaimbo_accesskey');
// 	if ($check) //the access key was already set
// 		return;	
	wp_cache_flush(); //make a real database check, don't use the cache, another session may have changed the value.
	if (!add_option('kaimbo_accesskey',$key)) 
		return; //the key was already set by another thread
	wp_cache_flush();
	$check=get_option('kaimbo_accesskey');
	if ($check!=$key)
		return; //another key was already saved, do not call a registration with a different key.
	
	$url=$tisearch_serverUrl."register?base=".$url."&accesskey=".$key;
	$result=tisearch_getJson($url,'',10);	
	
	if ( !is_wp_error($result) && $result=="OK" ) {
		tisearch_getJson($tisearch_serverUrl."errorLog?base=".ti_getWebsiteUrl()."&error=false","was registered: ".$key);			
	}
	else {
		ti_errorLog("Connection failed when registering the website at the search service.");
		$error=$result;
		if (is_wp_error($result))
			$error=$result->get_error_message();
		ti_errorLog($error);
		//send the error to us to know why was this not working.
		tisearch_getJson($tisearch_serverUrl."errorLog?base=".ti_getWebsiteUrl(),"Error at the website registration: ".$error." the key which was tried to register: ".$key);
		return;
	}
	
//  no need for this anymore.
// 	$stored=get_option('kaimbo_accesskey');
// 	if ($stored==false) {
// 		ti_errorLog("The accesskey could not be stored.");
// 		tisearch_getJson($tisearch_serverUrl."errorLog?base=".ti_getWebsiteUrl(),"Storing the accesskey in the options failed.");
// 	}	
// 	else {
// 		tisearch_getJson($tisearch_serverUrl."errorLog?base=".ti_getWebsiteUrl(),"The accesskey stored in the options is: ".$stored);
// 	} 
}

function kaimbo_setWpPageCount() {
	$saved=get_option("kaimbo_pagecount");
	if ($saved) 
		return;
	
	$count=kaimbo_getWpPageCount();
	global $tisearch_serverUrl;
	global $ti_accessKey;
	$result=tisearch_getJson($tisearch_serverUrl.'setTheirsPageCount/?base='.ti_getWebsiteUrl().'&accesskey='.$ti_accessKey.'&count='.$count,'');
	if ($result=="OK") {
		update_option("kaimbo_pagecount",$count);
	}
}

function kaimbo_getWpPageCount() {
	global $wpdb;
	$query = 'SELECT count(*) as c FROM '.$wpdb->posts.' WHERE post_status="publish"';	
	try {
		$countObj = reset($wpdb->get_results($query)); //gets first element of an array, or false.
		if ($countObj) $count=$countObj->c;
			else $count=0; 
	}
	catch (Exception $e) {
		$count=0;
	}
	if (!$count) $count=0;
	return $count;
}

function kaimbo_wasFullyCrawled() {
	$result=get_option('kaimbo_wasFullyCrawled');
	if ($result!="Finished") {
		global $tisearch_serverUrl;
		$result=tisearch_getJson($tisearch_serverUrl."wasCrawlFinished/?base=".ti_getWebsiteUrl(),'');
		if ($result=="Finished") {
			update_option('kaimbo_wasFullyCrawled','Finished');
			return true;
		}
		return false;
	}
	else
		return true;
}

function kaimbo_checkIfLocal() {
 	global $tisearch_serverUrl;	
	$testUrl=$tisearch_serverUrl."testiflocal?url=".$url.plugin_dir_url(__FILE__).'localtest.php';	
	$result=tisearch_getJson($testUrl,'',5);
	if ($result!="OK") {
		ti_errorLog("Your website address is detected as a local addres, not accessible from the internet. Kaimbo can not work.");
		return true;
	}
	return false;
}	

function kaimbo_gen_uuid() {
	return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
			// 32 bits for "time_low"
			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
			// 16 bits for "time_mid"
			mt_rand( 0, 0xffff ),
			// 16 bits for "time_hi_and_version",
			// four most significant bits holds version number 4
			mt_rand( 0, 0x0fff ) | 0x4000,
			// 16 bits, 8 bits for "clk_seq_hi_res",
			// 8 bits for "clk_seq_low",
			// two most significant bits holds zero and one for variant DCE1.1
			mt_rand( 0, 0x3fff ) | 0x8000,
			// 48 bits for "node"
			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
	);
}

function kaimbo_setVersion() {
	global $kaimbo_version;	
	global $ti_accessKey;
	global $tisearch_serverUrl;
	
	if ($kaimbo_version!=get_option('kaimbo_version')) {
		$result=tisearch_getJson($tisearch_serverUrl."installstatus/?status=on&base=".ti_getWebsiteUrl()."&accesskey=".$ti_accessKey.'&version='.$kaimbo_version,'');
		if ($result=='InstallStatus')
			update_option('kaimbo_version',$kaimbo_version);
	}
}

?>
