<?php 
//this calls the remote server and returns the response.
function tisearch_getJson($json_url, $json_string, $timeout=5) {
	//$start = microtime(true);
	
	if (strlen($json_string)>0) {
		$json_string='json='.urlencode($json_string);
	}
	
	$isTimeouted=false;
	$result=false;
	if (ti_iscurlinstalled()) {		
		$result=tisearch_getUsingCurl($json_url, $json_string, $timeout);		
		if (strpos("ERROR:",$result)===0) {
			if (strpos("ERROR:28",$result)===0) {
				$isTimeouted=true;
			}
			$result=false;
		}		
	}
	
	if (!$isTimeouted && ini_get('allow_url_fopen')) {
		if (!ti_iscurlinstalled() || $result==false || strlen($result)==0) {
			$result=tisearch_file_post_contents($json_url, $json_string, $timeout);
		}
	}
	
	if (!$isTimeouted && ($result==false || strlen($result)==0)) {
		$result=wp_remote_post($json_url, array( 'body' => $json_string, 'timeout' => $timeout));
		if( is_wp_error( $result ) ) {
			$error_message = $result->get_error_message();
			ti_errorLog($error_message);
		}
		else {
			$result=$result['body'];
		}
	}	
	
	//$time_taken = microtime(true) - $start;
	//ti_errorlog("Json time $time_taken for: '".substr($json_string,0,30)."'");
	return $result;
}

function tisearch_getUsingCurl($json_url, $json_string, $timeout=5) {		
	$ip=$_SERVER['REMOTE_ADDR'];
	$ch = curl_init( $json_url );
	$options = array(
			CURLOPT_RETURNTRANSFER => true,			
			CURLOPT_HTTPHEADER => array('Content-Type: application/x-www-form-urlencoded') ,
			CURLOPT_POSTFIELDS => $json_string,
			CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],			
			CURLOPT_VERBOSE => true,
			CURLOPT_CAINFO => dirname( __FILE__ ).'/mozilla.pem',
			CURLOPT_HTTPHEADER => array("REMOTE_ADDR: $ip", "HTTP_X_FORWARDED_FOR: $ip"),
			CURLOPT_NOSIGNAL => true,
			CURLOPT_TIMEOUT => $timeout, //running time
			CURLOPT_CONNECTTIMEOUT => 2  //connection time
	);

	// if authentication needed
	global $tisearch_userName, $tisearch_password;
	if (isset($tisearch_userName)) {	
		$options[CURLOPT_USERPWD] = $tisearch_userName . ":" . $tisearch_password;
	}   	

	curl_setopt_array( $ch, $options );			 		
	$result=curl_exec($ch);
		
	if (curl_error($ch) || $result==false || strlen($result)==0) {
		ti_errorLog("Error connecting to the server.");
		ti_errorLog(curl_error($ch));		
		ti_errorLog("Request url: ".$json_url);
		//$result=false;
		$result="ERROR:".curl_errno($ch).'-'.curl_error($ch);
	}	
	
	curl_close($ch);	
	
	return $result;
}

function ti_errorLog($string) {	
	$rotate=200;
	
	$string=hideAccesskey($string);
	
	$existing=get_option('kaimbo_errorlog');
	if (!$existing)
		$existing='';
	$parts=explode('~',$existing);
	
	if (count($parts)>=$rotate) {
		for ($i=1; $i<$rotate; $i++) {
			$parts[$i-1]=$parts[$i];
			$last=$rotate;
		}
	}
	else {
		$last=count($parts)+1;
	}
	
	$new='';
	for ($i=0; $i<$last-1; $i++) {
		$new=$new.$parts[$i].'~';
	}
	$new=$new. date(DateTime::RSS). ": ".$string. " ";
	
	update_option('kaimbo_errorlog',$new);
}

function ti_getWebsiteUrl() {
	$url= site_url();
	if ($url[strlen($url)-1]!='/')
		$url=$url.'/';
	return $url;
}

function hideAccesskey($string) {
	$i=0; $end=false;
	while (($k=strpos($string,"accesskey=",$i))>0) {
		$k=$k+10;
		do {
			$k++;
			if ($k>=strlen($string) || $string[$k]=="&") {
				$end=true;
			}
			else {
				$string[$k]="*";
			}
		} while (!$end);
		$i=$k;
	}
	return $string;
}

function tisearch_file_post_contents($url, $data, $timeout=5, $username = null, $password = null)
{
	$opts = array('http' =>
			array(
					'method'  => 'POST',
					'header'  => 'Content-type: application/x-www-form-urlencoded',
					'content' => $data,
					'timeout' => $timeout
			)
	);

	global $tisearch_userName, $tisearch_password;
	if (isset($tisearch_userName)) {	
		$opts['http']['header'] = ("Authorization: Basic " . base64_encode("$tisearch_userName:$tisearch_password"));
	}

	$context = stream_context_create($opts);
		
	$result=@file_get_contents($url, false, $context); //@ supresses the warning.	
	return $result;
}

function ti_iscurlinstalled() {
	if  (in_array('curl', get_loaded_extensions())) {
		return true;
	}
	else{
		return false;
	}
}

?>
