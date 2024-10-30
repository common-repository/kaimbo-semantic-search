<?php
/*
Plugin Name: KAIMBO - Semantic Search
Plugin URI: http://wordpress.org/extend/plugins/kaimbo-semantic-search/
Description: KAIMBO semantic search represents a whole new level in website search. Readers will find related concepts, can search in pdfs, similar words.
Version: 2.7.3
Author: Transinsight
Author URI: http://kaimbo.com
License:
*/

//test change.

$kaimbo_version='2.7.3';  // !!!!!! don't forget to set this on version change !!!!!!!! 

include 'serverUrl.php';
$tisearch_serverUrlIE=plugin_dir_url(__FILE__).'ieproxy.php?url=';
include 'json.php';

register_activation_hook( __FILE__, "kaimbo_activation" );
register_deactivation_hook( __FILE__, "kaimbo_deactivation" );

function kaimbo_activation() {
	include 'serverUrl.php';
	ti_checkSiteRegistration();
	
	$ti_accessKey=get_option('kaimbo_accesskey');	
	tisearch_getJson($tisearch_serverUrl."installstatus/?status=on&base=".ti_getWebsiteUrl()."&accesskey=".$ti_accessKey,'');
}

function kaimbo_deactivation() {
	global $tisearch_serverUrl;
	global $ti_accessKey;

	tisearch_getJson($tisearch_serverUrl."installstatus/?status=off&base=".ti_getWebsiteUrl()."&accesskey=".$ti_accessKey,'');
}

$ti_accessKey=get_option('kaimbo_accesskey');

include('control.php');
include('design.php');

//if we did not crawled once fully the site, we return the original wordpress results, and show a warning popup.
if (kaimbo_wasFullyCrawled()) {
	add_filter('the_posts', 'tisearch_search');
}
add_action('init', 'tisearch_init');
add_action('init', 'tisearch_load_locale' );

//just show the user query, not the extended one.
//add_filter('get_search_query', 'ti_fullSearchQuery');

add_action( 'admin_menu', 'ti_search_admin_menu' );
function ti_search_admin_menu() {
	include(plugin_dir_path(__FILE__).'options.php');
}

function tisearch_isIE() {		
    $u_agent = $_SERVER['HTTP_USER_AGENT'];
    if(preg_match('/MSIE/i',$u_agent)) {
		return true;
    }
        
//     //if authentication needed, we take the IE proxy way. 
//     //Otherwise we get authentication Origin is not allowed by Access-Control-Allow-Origin.
//     global $tisearch_userName;
//     if (isset($tisearch_userName)) {
//     	return true;
//     }
    
// 	return false;
}

function kaimbo_isHighlightNeeded() {
	$options = get_option('ti_search_options');
	return !is_admin() && ($options[highlightForUnlogged]=='on' || current_user_can("edit-ontology")) && ($options[highlightTermOnOff]!='off');
}

function tisearch_init() {
	ti_checkSiteRegistration();
	kaimbo_setVersion();	
	kaimbo_setWpPageCount();
	
	wp_enqueue_script('jquery');		
	wp_enqueue_script('jquery-ui-core');	
	wp_enqueue_script('jquery-ui-widget');
	wp_enqueue_script('jquery-ui-position');
	wp_enqueue_script('jquery-ui-autocomplete');
	wp_enqueue_script('jquery-ui-autocomplete.html', plugin_dir_url(__FILE__).'jquery.ui.autocomplete.html.js', array('jquery','jquery-ui-core','jquery-ui-widget','jquery-ui-position','jquery-ui-autocomplete'), false, true);		
	
	//the highlight attachment to the ready event must run first, before other plugins
	//which use the dom tree like the lightbox gallery.
	//so if the lightbox.js runs first, ours disables it on the highlighting.	
	if (kaimbo_isHighlightNeeded()) {
		wp_enqueue_script('ti_highlight', plugin_dir_url(__FILE__).'highlightOnReady.js');
	}	
	
	wp_enqueue_script('contentElementFinder', plugin_dir_url(__FILE__).'contentElementFinder.js');
	wp_enqueue_style('ti_font-awesome',plugin_dir_url(__FILE__)."css/font-awesome.css");
			
	wp_enqueue_style('ti-popups',plugin_dir_url(__FILE__)."css/ti-popups.css"); //we use a part in admin also.
	wp_enqueue_script('term-popup-base', plugin_dir_url(__FILE__).'term-popup-base.js');
	
	//wp_enqueue_script('prototype-no$', plugin_dir_url(__FILE__).'prototype.js','jquery',false,true);
	
	global $tisearch_serverUrl;
	$test=tisearch_getJson($tisearch_serverUrl.'api/json-activity?base='.ti_getWebsiteUrl(),'',5);
	if ($test && strlen($test)>200)	{
		wp_enqueue_script('ti-jsonapi', $tisearch_serverUrl.'api/json-activity?base='.ti_getWebsiteUrl());
	}
	else {
		ti_errorLog("api/json not available ".$test);
	}
	//wp_enqueue_script('ti-autocomplete', plugin_dir_url(__FILE__).'autocomplete.js',array('jquery','prototype-no$'), false, true);
	wp_enqueue_script('ti-suggest', plugin_dir_url(__FILE__).'ti-suggest.js', array('jquery'), false, true);
	
	if (is_admin()) {
		wp_enqueue_style('tisearch_admin',plugin_dir_url(__FILE__)."css/admin.css");
		return;   	//these are not needed on the admin page
	}

	wp_enqueue_style('ti-autocomplete+highlight',plugin_dir_url(__FILE__)."css/autocomplete-highlight.css");	
	if (file_exists(plugin_dir_path(__FILE__)."css/custom-highlight.css"))
		wp_enqueue_style('ti-custom-highlight',plugin_dir_url(__FILE__)."css/custom-highlight.css");
	
	global $exitnexttime;
	$exitnexttime=false;

	//global $tisearch_serverUrl;				
	
	if (current_user_can("edit-ontology")) {
		global $tisearch_serverUrlIE;
			
		wp_enqueue_script('ti-ontologyeditapi', $tisearch_serverUrlIE.'api/ontologyedit');	
				
		wp_enqueue_style('ti-logged-in',plugin_dir_url(__FILE__)."css/logged-in.css");
		if (file_exists(plugin_dir_path(__FILE__)."css/custom-logged-in.css"))
			wp_enqueue_style('ti-custom-logged-in',plugin_dir_url(__FILE__)."css/custom-logged-in.css");
		wp_enqueue_script('ti-mouse-selection', plugin_dir_url(__FILE__).'mouse-selection.js');
		wp_enqueue_script('term-popup-allow', plugin_dir_url(__FILE__).'term-popup-allow.js');						
		
		$options = get_option('ti_search_options');
		if ($options[enableOnlineEditor]!='off')	{
			wp_enqueue_style('ti-chkedit-css',plugin_dir_url(__FILE__)."css/chkedit.css");
			wp_enqueue_script('ti-ckeditor', plugin_dir_url(__FILE__).'ckeditor/ckeditor.js');
			wp_enqueue_script('ti-ckeditor-custom', plugin_dir_url(__FILE__).'ckeditor-customize.js');
		}
	}
	else 
		wp_enqueue_script('term-popup-disabled', plugin_dir_url(__FILE__).'term-popup-disabled.js');
}

add_action( 'wp_footer', 'ti_search_inline_scripts');
function ti_search_inline_scripts() {
	
	global $tisearch_serverUrl;
	global $tisearch_serverUrlIE;
	global $wp_query;
	global $ti_accessKey;
	
	include('login.php');
	
	include('indexwarning.php');
	
	//for the javascript calls from the user in the ontology editing
	echo '<script type="text/javascript"> ti_serverUrl="'.$tisearch_serverUrl.'"; </script>';
	echo '<script type="text/javascript"> ti_serverUrlIE="'.$tisearch_serverUrlIE.'"; </script>';	
	
	$editAllowed="false";
	$options = get_option('ti_search_options');
	if ($options[enableOnlineEditor]!='off')
		$editAllowed="true";
	
	echo '<script type="text/javascript"> ti_plugin_dir="'.plugin_dir_url(__FILE__).'"</script>';	
	echo '<script type="text/javascript"> ti_post_id="'.$wp_query->post->ID.'";</script>';				
	echo '<script type="text/javascript"> ti_is_page="'.is_page().'"; TISearchPopups.editAllowed="'.$editAllowed.'";  </script>';
	echo '<script type="text/javascript"> ti_wordpress_url="'.ti_getWebsiteUrl().'";</script>';
	echo '<script type="text/javascript"> ti_accesskey="-ACCESSKEY-";</script>';

	if (kaimbo_isHighlightNeeded) {
 		$akt_post=get_post($wp_query->post->ID);
 		echo '<script type="text/javascript"> ti_beforeHighlight=decodeURIComponent("'.rawurlencode($akt_post->post_content).'");</script>';  		
		?>
		<script type='text/javascript'>	
		function ti_highlightTerms() {			
			try {	  
				var jsonConf = new com.transinsight.JsonConf('<?php echo $tisearch_serverUrlIE; ?>'+"highlightterms",null);
			}
			catch (e) {
				console.log('new JsonConf failed. The Server is not available ?'); 
				return;
			}

			var annotatorCallback= function(obj) {
				if (obj && !obj.exception && obj.result) {
					var element=kaimbo_getContentBaseElement();
			   		element.innerHTML=obj.result;
			   	}
			};			
			
			var annotator = new com.transinsight.yggdrasil.jsonapi.JsonAnnotatorComponent(jsonConf);
			try {			
				var element=kaimbo_getContentBaseElement();											
			   	var obj = annotator.annotate(<?php echo json_encode(ti_getWebsiteUrl()); ?>, element.innerHTML);
			   	annotatorCallback(obj);			   	    
			}
			catch (e) {	
				console.log('Highlighting - JsonAnnotator problem');
		  	}
		}
		</script>
	<?php }
		else { ?>
		<script type="text/javascript">function ti_highlightTerms() {}</script>
	<?php } ?>

	<script type='text/javascript'>
		//this brings back the last search query in the search box.
		ti_showlastquery = function() {
			var query=<?php echo json_encode($wp_query->query_vars["s"]); ?>; //escape single qoutes, eventual newlines
			if (query=='') 
				query='<?php echo __('Semantic Search','ti-search'); ?>';						
			//applyDefaultValue(document.getElementById('s'), query);
			var list=tisearch_getSearchFields();
			for (var i=0; i<list.length; i++) {
				var elem=list[i];
				//elem=document.getElementById('s');
				elem.value=query;
				elem.onfocus = function() {
			      	this.style.color = '#000';
			      	if (query=='<?php echo __('Semantic Search','ti-search'); ?>')
				      	this.value='';
			      	this.select();				      	
					  //to handle the chrome mouseup-unselect problem. Browsers ...
			      	this.onmouseup = function() {
					  	this.onmouseup=null;
					  	return false;
				  	};
				};				
			}					
		};	

		//jquery.load is not good here because it is overwritten by the page's onload handler
		// Semantic search is replaced with Suchen.
		var ti_oldWindowOnLoad=window.onload;
		window.onload=function() {			
			if (ti_oldWindowOnLoad)
				 ti_oldWindowOnLoad();			
			ti_showlastquery();				   
	 	};		 		 	
	</script>
<?php 
}

function tisearch_load_locale() {
	// Here we manually fudge the plugin locale as WP doesnt allow many options
	$locale = get_locale();
	if( empty( $locale ) )
		$locale = 'en_US';

	$mofile = dirname( __FILE__ )."/locale/$locale.mo";
	load_textdomain('ti-search', $mofile);
}

function ti_getResultCount() {
	global $wp_query;
	return $wp_query->found_posts.' '.__('documents found.', 'ti-search');
}

function tisearch_search($posts, $query = false) {
	global $exitnexttime;
	if ($exitnexttime)
		return $posts;

	global $wp_query;
	//$query=$wp_query->query_vars["s"];
	//$query=str_replace('\\"','"',$query);
	//$query=stripslashes($query);	
	
	$query=get_search_query(false);
	$query=urldecode($query);
	
	kaimbo_handleShortcodes($query);
	
	$wp_query->query_vars["s"]=$query;
	$_GET["s"]=$query;   //if we don't set this back, the language switcher links will contain s=\\"someting\\"
	
	if (strlen($query)==0)
		return $posts;

	global $tisearch_serverUrl;

	$result=tisearch_getJson($tisearch_serverUrl.'json/search','');
	if (filter_var($result, FILTER_VALIDATE_URL) === FALSE) {
 		ti_errorLog("Connection to the search service failed. Returning the default Wordpress results :(");
 		ti_errorLog(print_r($result,true));
		return $posts;
	}	
	
	//var_dump($result."<br>");
	$url=ti_getWebsiteUrl();	
	
	//$lang=ICL_LANGUAGE_CODE;
	$lang=get_bloginfo("language");
	if (strlen($lang)>2)
		$lang=substr($lang,0,2);
	else $lang='en';

	$jsonQuery=$query.' '.$lang.'[Language] ("'.$url.'"[Host]';	
	if (function_exists(kaimbo_addExternalQuery)) { 
    	$jsonQuery=$jsonQuery.kaimbo_addExternalQuery($url);
    }
    $jsonQuery=$jsonQuery.')';

	$tmp=tisearch_getJson($result,'{"search":['.json_encode($jsonQuery.' 100[maxdocs]').',10]}');	
	
	$searchres=json_decode($tmp)->result;
	
// 	if ($searchres->size==0)
// 		return $posts; 
	
	$wp_query->max_num_pages=$searchres->pages;
	$wp_query->found_posts=$searchres->size;	
	$wp_query->extended_query=$searchres->queryWithSynonyms;
	//echo $wp_query->max_num_pages;

	global $wp;
	$request=$wp->request;
	$page='1';
	//www.x.com/category/action-adventure/page/2/?s=filmtitle
	$pi=strpos($request,'page/');	
	if (strlen($request)>0 && $pi>=0) {
 		$pi=$pi+5;
 		$end=strpos($request,'/',$pi);
 		if ($end==false)
 			 $end=strlen($request);
 		$page=substr($request,$pi,$end-$pi);
 		if (!is_numeric($page)) {
			$page='1';
		}
	}

	$documents= json_decode(tisearch_getJson($result,"{'getSnippetDocumentsOnPage':[$page]}"));
	if (!$documents || !isset($documents->result)) {
		ti_errorLog("Connection to the search service failed. Returning the default Wordpress results :(");
		ti_errorLog(print_r($documents,true));
		return $posts;
	}
	//var_dump($documents);
	
	if (function_exists(kaimbo_addExternalQuery)) {
		require_once 'externalpages/postIdForExternalUrl.php';
	}

	$options = get_option('ti_search_options');
	global $wpdb;
	$wpResult=Array();
	foreach ($documents->result as $doc) {
		$wpobj = new stdClass;

		$url=get_bloginfo('url');
		$url=substr($doc->url,strlen($url),strlen($doc->url));
		$url=str_replace("?lang=en","",$url);
		$url=str_replace("?lang=de","",$url);
		//echo $doc->url." - ".$url."<BR>";

		$pdf=false;
		$noPagePost=false;
		
		if ($url==="" || $url=="/") 
		{
			$post=null;
		}
		else { //normal case
			$post=get_page_by_path($url);
			if ($post==null)  {
				
			}
		}
						
		//echo $post." -> ".$url."<br>";
		
		if ($post==null) {
			global $exitnexttime;
			$exitnexttime=true;
			$id=url_to_postid($doc->url);  //post or page found
			$exitnexttime=false;
			if ($id==null) {
				$noPagePost=true;
			}
		}
		else
			$id=$post->ID;    //post or page found
			
		// $id." - ".$url." -> ".
		if ($noPagePost) {			
			$wpobj->post_title=$doc->title;
			if ($doc->title==null)
				$wpobj->post_title=$doc->url;
		}
		else {
			$wpobj=get_post($id);
			if ($wpobj==null) {
				$wpobj = new stdClass;
			}
		}
		
		if (function_exists(kaimbo_addExternalQuery)) {
			$id=kaimbo_getIDfromURL($doc->url, $doc->title);
			if ($id) { 
				$wpobj=get_post($id);
			}
			else continue; //skip it if we don't get the id.
			$wpobj->EXT_URL = $doc->url;
		}

		$wpobj->guid=$doc->url; //this is sometimes necessary because of the permalink override
		
		if (strpos($doc->url,"#")>0) {
			$wpobj->post_title= $wpobj->post_title." | ".$doc->title;
		}
		
		$origExcerpt=$wpobj->post_excerpt;
		if ($origExcerpt=="") {
 			$text = $wpobj->post_content;
			$text = strip_tags(strip_shortcodes($text));
			$text = apply_filters('the_content', $text);
			//$excerpt_length = apply_filters( 'excerpt_length', 55 );
			//$excerpt_more   = apply_filters( 'excerpt_more', ' ' . '[...]' );
			$origExcerpt = wp_trim_words( $text );
			//$origExcerpt=$text;			
		}		
 		if ($options['snippetType']!="off" || $origExcerpt=="") {
			$snippet=ti_highLightSnippet($doc, $doc->snippet);
			//$wpobj->post_content = $snippet;
			$wpobj->post_excerpt = $snippet;
		}
		else {			
			$wpobj->post_excerpt = $origExcerpt;
			//$wpobj->post_content = $origExcerpt;
		}
				
		//var_dump($wpobj);		
		array_push($wpResult,$wpobj);
	}

	//var_dump($wpResult);			
	return $wpResult;
}

function ti_fullSearchQuery($orig) {
	global $wp_query;
	
	$q=$wp_query->extended_query;

	if (!$q)
	  $q=$orig;

	//show the synonyms with gray.
	//$q='</span><span class="query-grayedouot">'.$q.'</span><span>';
// 	$i=strpos($q,$orig);
// 	if ($i!==false) {
// 		$q=substr($Q,0,$i).'<span class="query-greened">'.$orig.'</span>'.substr($q,$i);
// 	}
	
	return $q;
}

function kaimbo_handleShortcodes($query) {
  if ($query=="#logout") {
    $logoutUrl=str_replace("&amp;","&",wp_logout_url($_SERVER['HTTP_REFERER']));//."?action=logout&redirect_to="$_SERVER['REQUEST_URI'];
  	wp_redirect($logoutUrl);
  	exit;
  }
}

?>
