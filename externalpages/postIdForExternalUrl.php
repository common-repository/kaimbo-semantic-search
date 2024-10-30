<?php 
function kaimbo_getIDfromURL($url, $title) {
	global $wpdb;
	global $tisearch_serverUrl;
	$query="select post_id from wp_postmeta where meta_key='EXT_URL' and meta_value = '$url'";
	$results = $wpdb->get_results( $query, OBJECT);
	if (sizeof($results)>0) {
		return $results[0]->post_id;
	}
	$query="select post_id from wp_posts where (post_type='post' or post_type='page') and post_title = '$title'";
	$results = $wpdb->get_results( $query, OBJECT);
	if (sizeof($results)>0) {
		return $results[0]->post_id;
	}

	$noncanonical=tisearch_getJson($tisearch_serverUrl."getParentUrl/?url=".rawurlencode($url),'');
	$noncanonical=html_entity_decode(stripslashes(($noncanonical)));
	$noncanonical=substr($noncanonical,1,strlen($noncanonical)-2);
	if (strpos($noncanonical,"http")===0) {
		$query="select post_id from wp_postmeta where meta_key='EXT_URL' and meta_value = '$noncanonical'";
		$results = $wpdb->get_results( $query, OBJECT);
		if (is_array($results) && sizeof($results)>0) {
			return $results[0]->post_id;
		}
	}

	return null;
}
?>