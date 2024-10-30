<?php 

add_options_page( 'Kaimbo - Semantic Search Options', 'Kaimbo - Semantic Search', 'manage_options', 'ti-search', 'ti_search_options' );

function ti_search_options() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}		
?>

<script type="text/javascript">
// don't move this down, has to be before the ti_canItWorkChecks call
function kaimbo_switch_tab(tab) {
	for (var i=1; i<6; i++) {
		var el=document.getElementById('kaimbo_options_'+i);
		el.style.display="none";
		el=document.getElementById('kaimbo_tab_title-'+i);		
		jQuery(el).removeClass('pushed');
	}
	var el=document.getElementById('kaimbo_options_'+tab);
	el.style.display="block";
	el=document.getElementById('kaimbo_tab_title-'+tab);		
	jQuery(el).addClass('pushed');
}
</script>

<?php $switchTo3= kaimbo_toomanyPagesMessage();?>	

<div class="kaimbo_tabs_header">
	<div id="kaimbo_tab_title-1" class="pushed kaimbo_tab_title btn btn-mini" onclick="kaimbo_switch_tab(1);">Wordpress options</div>
	<div id="kaimbo_tab_title-2" class="kaimbo_tab_title btn btn-mini" onclick="kaimbo_switch_tab(2);">KAIMBO Service Settings</div>
	<div id="kaimbo_tab_title-3" class="kaimbo_tab_title btn btn-mini" onclick="kaimbo_switch_tab(3);">Indexing Information and Search Test</div>
	<div id="kaimbo_tab_title-4" class="kaimbo_tab_title btn btn-mini" onclick="kaimbo_switch_tab(4);">Search Statistics</div>
	<div id="kaimbo_tab_title-5" class="kaimbo_tab_title btn btn-mini" onclick="kaimbo_switch_tab(5);">Administration</div>
</div>

<div class="kaimbo_Options_tab" id="kaimbo_options_1">
	<div class="wrap">
		<form method="post" action="options.php">
			<?php 
			settings_fields('ti_search_options');
			do_settings_sections('ti-search');
			submit_button();
			?>
		</form>
	</div>
</div>
	
<?php 
	$url=get_bloginfo('url');
	if ($url[strlen($url)-1]!='/')
		$url=$url.'/';
	global $tisearch_serverUrl;
	global $ti_accessKey;
	$url=$tisearch_serverUrl."options?base=".$url."&accesskey=".$ti_accessKey;
?>

<div class="kaimbo_Options_tab" id="kaimbo_options_2" style="display:none">
	<div style="height:700px;">
	<iframe
		id="ti-options-frame" src="<?php echo $url;?>" width="100%"
		height="700px">
	</iframe>
	</div>
</div>

<div class="kaimbo_Options_tab" id="kaimbo_options_3" style="display:none">
	<div style="height:700px;">
	<iframe
		id="ti-options-frame" src="<?php echo $url.'&action=indexandsearch';?>" width="100%"
		height="700px">
	</iframe>
	</div>
</div>

<div class="kaimbo_Options_tab" id="kaimbo_options_4" style="display:none">
	<div style="height:900px;">
	<iframe
		id="ti-options-frame" src="<?php echo $url.'&action=stats';?>" width="100%"
		height="900px">
	</iframe>
	</div>
</div>

<div class="kaimbo_Options_tab" id="kaimbo_options_5" style="display:none">
	<?php ti_canItWorkChecks(); ?>		
	
	<a href="<?php echo plugin_dir_url(__FILE__)?>kaimbo-phpinfo.php" target="_blank">phpinfo</a>
</div>

<?php if ($switchTo3) 
	echo '<script type="text/javascript"> kaimbo_switch_tab(3); </script>'; 
?>

<!-- feedback, Logs -->
	<h3><a href="#" onclick="TISearchPopups.showFeedbackPopup(this)">Feedback, report a problem</a></h3>
	<?php include 'feedbackform.php' ?>

	<?php 
		$existing=get_option('kaimbo_errorlog');
		if ($existing) {
			//	echo "<br>";
			echo '<div id="kaimbo_moreLogs" onclick=TISearchPopups.descriptionOnOff(this,"",1)>';
			echo '<span id="showmoreDescription1" class="awesomeFont">Show logs &#xf078;</span> <span id="showlessDescription1" class="awesomeFont" style="display:none">Hide logs &#xf077;</span>';
			echo '<div id="kaimbo_DescriptionDiv" class="kaimbo_DescriptionDiv">';
			$parts=explode('~',$existing);	
			for ($i=0; $i<count($parts); $i++) {
				echo "$parts[$i]<br>";
			}
			echo '</div></div><br>';
		}		
	?>	
<!-- ------------- -->

<?php	
}

add_action('admin_init', 'ti_search_plugin_admin_init');

function ti_search_plugin_admin_init(){
	register_setting( 'ti_search_options', 'ti_search_options', 'ti_search_options_callback' );
	add_settings_section('ti_user_settings', 'User settings', 'ti_section1_text', 'ti-search');
	add_settings_field('canuser', 'These users are allowed to edit the knowledge base:', 'ti_search_settings1', 'ti-search', 'ti_user_settings');
	
	add_settings_section('ti_loginlink_settings', 'Website settings', 'ti_section2_text', 'ti-search');
	add_settings_field('snippetType', 'Show the Kaimbo excerpt, which contains the searched keywords:', 'ti_search_settings9', 'ti-search', 'ti_loginlink_settings');
	add_settings_field('showLoginLink', 'Show login link:', 'ti_search_settings2', 'ti-search', 'ti_loginlink_settings');
	add_settings_field('alignRight', 'Align options link to right:', 'ti_search_settings8', 'ti-search', 'ti_loginlink_settings');
	add_settings_field('enableOnlineEditor', 'Enable online editing of pages:', 'ti_search_settings3', 'ti-search', 'ti_loginlink_settings');
	
	add_settings_section('ti_highlight_settings', 'Highlighting settings', 'ti_section3_text', 'ti-search');	
	add_settings_field('highlightSearchOnOff', 'Highlight the searched keywords in the search result:', 'ti_search_settings_searchonoff', 'ti-search', 'ti_highlight_settings');
	add_settings_field('highlightTermOnOff', 'Highlight the concepts of the knowledge base on the pages:', 'ti_search_settings_termonoff', 'ti-search', 'ti_highlight_settings');
	add_settings_field('highlightForUnlogged', 'The concepts on the page will be clickable for unlogged users also, to see related concepts:', 'ti_search_settings4', 'ti-search', 'ti_highlight_settings');	
	//add_settings_section('ti_highlight_colors', 'Color settings', 'ti_section4_text', 'ti-search');
	add_settings_field('highlightBackground', 'The background color for highlighting:', 'ti_search_settings5', 'ti-search', 'ti_highlight_settings');
	add_settings_field('highlightTerm', 'The color for highlighted concepts:', 'ti_search_settings6', 'ti-search', 'ti_highlight_settings');
	add_settings_field('highlightKeyword', 'The color for highlighted searched keywords:', 'ti_search_settings7', 'ti-search', 'ti_highlight_settings');
	
	add_settings_field('editorBackground', 'The background color of the edited paragraph:', 'ti_search_settings10', 'ti-search', 'ti_highlight_settings');
	
	//add_settings_field('loginLinkLeftDistance', 'Login link distance from left side of the search field:', 'ti_search_settings3', 'ti-search', 'ti_loginlink_settings');
	//add_settings_field('loginLinkTopDistance', 'Login link distance below the search field:', 'ti_search_settings4', 'ti-search', 'ti_loginlink_settings');
}

function ti_section1_text() {
	//echo '<p>User Settings:</p>';
}
function ti_section2_text() {
	//echo '<p>Log in link settings:</p>';
}
function ti_section3_text() {
	//echo '<p>Log in link settings:</p>';
}
function ti_section4_text() {
	//echo '<p>Log in link settings:</p>';
}

function ti_search_settings1() {
	$users=get_users();
?>
<table class="ti-userselect">
	<tr>
		<th>&nbsp;</th>
		<th>login name</th>
		<th>display name</th>
		<th>firstname+lastname</th>
	</tr>
	<?php foreach ($users as $user) {  ?>
	<tr>
		<td><input name="ti_search_options[canuser-<?php echo $user->ID?>]"
			type="checkbox"
			<?php if (user_can($user->ID,"edit-ontology")) echo 'checked="checked"'; ?>>
			</input></td>
		<td><?php echo $user->user_login ?></td>
		<td><?php echo $user->display_name ?></td>
		<td><?php echo $user->user_firstname.' '.$user->user_lastname ?></td>
	</tr>
	<?php } ?>
</table>
<?php 
}

function ti_search_settings2() { 
	$options = get_option('ti_search_options'); 
	?><input name="ti_search_options[showLoginLink]" type="checkbox"
		<?php if ($options[showLoginLink]!="off") echo 'checked="checked"'; ?>>
	</input>	
<?php 
}

function ti_search_settings8() {
	$options = get_option('ti_search_options');
	?><input name="ti_search_options[alignRight]" type="checkbox"
		<?php if ($options[alignRight]=="on") echo 'checked="checked"'; ?>>
	</input>	
<?php 
}

function ti_search_settings9() {
	$options = get_option('ti_search_options');
	?><input name="ti_search_options[snippetType]" type="checkbox"
		<?php if ($options[snippetType]!="off") echo 'checked="checked"'; ?>
	  title="If unchecked, the original WordPress snippet will be shown - the beginning of the page/post">		
	</input>	
<?php 
}

function ti_search_settings3() {
	$options = get_option('ti_search_options');
	?><input name="ti_search_options[enableOnlineEditor]" type="checkbox"
		<?php if ($options[enableOnlineEditor]!="off") echo 'checked="checked"'; ?>>
	</input>	
<?php 
}

function ti_search_settings4() {
	$options = get_option('ti_search_options');
	?><input name="ti_search_options[highlightForUnlogged]" type="checkbox"
		<?php if ($options[highlightForUnlogged]=="on") echo 'checked="checked"'; ?>>
	</input>	
<?php 
}

function ti_search_settings5() {
	$options = get_option('ti_search_options');
	$value=$options[highlightBackground];
	if (!$value)
		$value="#EEEEEE";
	?><input name="ti_search_options[highlightBackground]" type="text"
		value="<?php echo $value; ?>">
	</input>	
	<span style="border: solid 1px gray; background-color: <?php echo $value; ?>">&nbsp;&nbsp;&nbsp;&nbsp;</span>
<?php 
}
function ti_search_settings6() {
	$options = get_option('ti_search_options');
	$value=$options[highlightTerm];
	if (!$value)
		$value="inherit";
	?><input name="ti_search_options[highlightTerm] || " type="text"
		value="<?php echo $value; ?>">
	</input>	
	<span style="border: solid 1px gray; background-color: <?php echo $value; ?>">&nbsp;&nbsp;&nbsp;&nbsp;</span>
<?php 
}
function ti_search_settings7() {
	$options = get_option('ti_search_options');
	$value=$options[highlightKeyword];
	if (!$value)
		$value="#73990E";
	?><input name="ti_search_options[highlightKeyword]" type="text"
		value="<?php echo $value; ?>">
	</input>	
	<span style="border: solid 1px gray; background-color: <?php echo $value; ?>">&nbsp;&nbsp;&nbsp;&nbsp;</span>
<?php 
}

function ti_search_settings10() {
	$options = get_option('ti_search_options');
	$value=$options[editorBackground];
	if (!$value)
		$value="#EEEEEE";
	?><input name="ti_search_options[editorBackground]" type="text"
		value="<?php echo $value; ?>">
	</input>	
	<span style="border: solid 1px gray; background-color: <?php echo $value; ?>">&nbsp;&nbsp;&nbsp;&nbsp;</span>
<?php 
}

function ti_search_settings_searchonoff() {
	$options = get_option('ti_search_options');
	?><input name="ti_search_options[highlightSearchOnOff]" type="checkbox"
		<?php if ($options[highlightSearchOnOff]!="off") echo 'checked="checked"'; ?>>
	</input>	
<?php 
}
function ti_search_settings_termonoff() {
	$options = get_option('ti_search_options');
	?><input name="ti_search_options[highlightTermOnOff]" type="checkbox"
		<?php if ($options[highlightTermOnOff]!="off") echo 'checked="checked"'; ?>>
	</input>	
<?php 
}

function ti_search_options_callback($input) {
	$result=Array();
	foreach (get_users() as $user) {
		$id=$user->ID;
		$user = new WP_User($id);
		if ($input['canuser-'.$id]!='on' && user_can($id,"edit-ontology")) {
			$user->add_cap("edit-ontology",false); //remove the capability if he lost it.
		}
	}
	foreach ($input as $key => $value) {
		if (strpos($key,"canuser")===0 && $value=='on') {
			$k=strpos($key,'-');
			$user_id=substr($key,$k+1);
			
			$user = new WP_User( $user_id );
			$user->add_cap("edit-ontology");			
		}
	}
		
	if ($input[snippetType]!='on')
		$input[snippetType]='off';
	
	if ($input[showLoginLink]!='on')
		$input[showLoginLink]='off';
	
	if ($input[alignRight]!='on')
		$input[alignRight]='off';
	
	if ($input[enableOnlineEditor]!='on')
		$input[enableOnlineEditor]='off';
	
	if ($input[highlightForUnlogged]!='on')
		$input[highlightForUnlogged]='off';
	
	if ($input[highlightTermOnOff]!='on')
		$input[highlightTermOnOff]='off';
	
	if ($input[highlightSearchOnOff]!='on')
		$input[highlightSearchOnOff]='off';
	
	try {
		$path=plugin_dir_path(__FILE__);
		$myFile = $path."/css/custom-highlight.css";
		$fh = @fopen($myFile, 'w');
		if (!$fh) {
			 add_settings_error("ti_settings",1,"Could not save the color settings. We need write access for the wordpress in the plugins/kaimbo folder to apply the colors to the css.");
			 return $input;
		}
		@fwrite($fh, ".KeywordHighlight , .TermHighlight { background-color: $input[highlightBackground] }\n");
		@fwrite($fh, ".TermHighlight { color: $input[highlightTerm] }\n");
		@fwrite($fh, ".KeywordHighlight { color: $input[highlightKeyword] }\n");	
		@fwrite($fh, ".ti-commonAncestor { background-color: $input[editorBackground] !important}\n");
		@fclose($fh);
				
		$myFile = $path."/css/custom-logged-in.css";
		$fh = @fopen($myFile, 'w');
		@fwrite($fh, ".TermAnnotation { background-color: $input[highlightBackground] }\n");
		@fwrite($fh, ".TermAnnotation { color: $input[highlightTerm] }\n");		
		@fclose($fh);	
	}
	catch (Exception $e) {		
	}
	
	return $input;
}

function ti_canItWorkChecks() {
	
	function opendown($alsoSwitch=false) {
		?>
		<script type="text/javascript">
			<?php if ($alsoSwitch) echo "kaimbo_switch_tab(5);"?>
			//jQuery.load(new function() {
				var el=document.getElementById('kaimbo_requirements');
				TISearchPopups.descriptionOnOff(el,"kaimbo_reqDiv");
			//});			
		</script>
		<?php 		
	}

    echo '<div id="kaimbo_requirements" class="kaimbo_closeable" onclick=TISearchPopups.descriptionOnOff(this,"kaimbo_reqDiv",2)>';
    echo '<h3 class="awesomeFont"><span id="showmoreDescription2">Prerequisites verification: &#xf078;</span> <span id="showlessDescription2" style="display:none">Prerequisites verification: &#xf077;</span></h3>';
	
	$problem=false; $p1=false; $p2=false; $p3=false;
	echo '<div id="kaimbo_reqDiv" class="kaimbo_DescriptionDiv">';
	echo '1. Checking if curl module is available: <span class="kaimbo_bold">';
	if (!ti_iscurlinstalled()) {
		 echo "NO</span><br>";
		 $p1=true;
	}
	else {
		echo "Yes</span><br>";
		try {
			$curltest=tisearch_getUsingCurl('https://transinsight.com/curltest.php','');
		}
		catch (Exception $e) {
			ti_errorLog($e);			
		};
		if ($curltest!='Works') {
			echo '&nbsp;&nbsp;&nbsp;&nbsp;Checking if curl works: <span class="kaimbo_bold">NO</span>';
			$p1=true;
		}
		else {
			echo '&nbsp;&nbsp;&nbsp;&nbsp;Checking if curl works: <span class="kaimbo_bold">Yes</span>';
		}
	}
	echo "<br><br>";

	echo '2. Checking if file_get_contents is available: <span class="kaimbo_bold">';
	if (!ini_get('allow_url_fopen')) {
		echo 'NO</span><br>';
		$p2=true;
	}
	else {
		echo 'Yes</span><br>';
		echo '&nbsp;&nbsp;&nbsp;&nbsp;Checking if file_get_contents works: <span class="kaimbo_bold">';
		$fgc=@file_get_contents('https://transinsight.com/curltest.php','');
		if ($fgc!='Works') {
			echo 'NO</span><br>';
			$p2=true;
		}
		else {
			echo 'Yes</span><br>';
		}
	}
	echo "<br>";
	
	echo '3. Checking if wp_remote_post works: <span class="kaimbo_bold">';
	$response=wp_remote_post('https://transinsight.com/curltest.php','');
	if( is_wp_error( $response ) ) {
		$error_message = $response->get_error_message();
		ti_errorLog($error_message);
		echo 'NO</span><br>';
		$p3=true;
	}
	else {
		if (!$response || !is_array($response) || $response['body']!='Works') {
			echo 'NO</span><br>';
			$p3=true;
		}
		else {
			echo 'Yes</span><br>';
		}
	}
	
	if ($p1 && $p2 && p3) $problem=true;
	echo '<br><span class="kaimbo_bold">At least one of the above must work</span>, otherwise the KAIMBO plugin can not connect to the search service, therefore can not return results.<br>';

	echo '<br>4. Testing outgoing https port (must be open): <span class="kaimbo_bold">';	
	
	$fp = @fsockopen('ssl://transinsight.com', 443, $errno, $errstr, 5); //@ supresses the error		
		
	if ($fp===false) {
		echo 'CLOSED. Port is blocked, the KAIMBO plugin will not be able to connect to the search service. Error '.$errno.", ".$errstr;
		$problem=true;
	} else {
		echo 'Open';	
		fclose($fp);
	}
	echo "</span><br>";
	
	if (!$problem && kaimbo_checkIfLocal()) {
		echo '<br><span style="color:red">You are trying to use the KAIMBO plugin on a server which is not accessible (reachable) from the internet.<br>';
		echo 'Your website must allow the crawler "Kaimbo 1.0" in the robots.txt to acces the website.<br>';
		echo 'Another cause can be that your server is in a local network behind a firewall.<br>'; 
		echo 'In these cases the plugin cannot index the website, thus the search functionality will not work. You can use only the inline editor.</span><br>';
		echo '</div></div>';		
		echo '<br><hr>';
		opendown(true);
		return; //no need to check the rest.
	}

	if (!$problem) {
		echo '<br>5. Testing JSON parsing (the service response has to arrive correctly): <span class="kaimbo_bold">';
		global $tisearch_serverUrl;
		$json=tisearch_getJson($tisearch_serverUrl."jsontest","");
		if (!json_decode($json)) {
			echo 'Error - The response can not be correctly parsed/decoded. You may have to address your hosting provider.'.'</span><br>';
			$problem=true;
		}
		else {
			echo 'OK</span><br>';
		}
	}	
	
	echo '</div>';
	echo '</div>';
	if ($problem) {
		echo '<span style="color:red">Your web hosting doesn\'t met the requirements to run the KAIMBO search plugin correctly. Please contact us at kaimbo@transinsight.com, telling us which points failed. The cause should be a firewall settings problem, which your hosting provider can correct. You can ask your provider directly, or send us the result of the checks, and the hosting provider data.</span><br>';
		opendown(true);
	}
	else {
		echo '<span class="kaimbo_bold" style="color:green">All OK, the KAIMBO search plugin prerequisites are met.</span><br>';
	}
	echo '<br><hr>';
}

function kaimbo_toomanyPagesMessage() {
	$theirPC=get_option("kaimbo_pagecount");
	//$showmessage= $theirPC>10000;

	//if (!$showmessage) {
		global $tisearch_serverUrl;
		$intheIndex=tisearch_getJson($tisearch_serverUrl.'getIndexedPageCountAndEdition/?base='.ti_getWebsiteUrl(),'');
		if ($intheIndex) {
			$intheIndex=@json_decode($intheIndex);
			if (is_array($intheIndex)) {
				// Array(indexedPageCount, editionMax)
				if ($intheIndex[0] >= $intheIndex[1] || $theirPC> $intheIndex[1])
					$showmessage=true;
			}
		}
	//}	
	
	if ($showmessage) {
	?>
		<h4>Dear KAIMBO user</h4>
		<span style="color:red">Your website has more pages than your current KAIMBO plugin Edition supports.</span><br/>
		In the Beta period we index maximum 10000 pages on a website. In the near future this will be reduced to the real limit of your <a href="http://kaimbo.com/editions" target="_blank">Edition</a> (Free, Personal, Professional).<br/>
		If not the complete website is indexed, the plugin will not replace the original Wordpress search functionality, rather<br/>
		<strong>you can use/test the search functionality here in the plugin settings, if you select the "Indexing Information and Search Test" tab.</strong><br> 
		Currently <?php echo $intheIndex[0];?> pages are quened to be indexed.
		<strong>If you would like to activate the KAIMBO plugin for the complete website, please contact us at <a href="mailto:kaimbo@transinsight.com">kaimbo@transinsight.com</a>, stating which edition would you like to order.</strong>		 
		<br/><br/>	
	<?php 	
		return $showmessage;
	}
}
?>
