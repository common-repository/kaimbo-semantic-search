<?php 
	$tisearch_serverUrl='https://gopubmed.org/web/websitesearch/'; //the ending / is necessary.

	$kaimbo_special_url=get_option('kaimbo_special_url');
	if ($kaimbo_special_url) {
		$tisearch_serverUrl=$kaimbo_special_url;
	}
?>
