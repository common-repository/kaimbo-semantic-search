<?php 
	// this file saves post edited by the user.
	
// 	$fname='wpdirpath.dat';
// 	$f=fopen($fname,"r");
// 	$wpdir=fgets($f);
// 	fclose($f);

	//get the full path of wp-load.php. This is current path + go up kaimbo, plugins, wp-content
	$wpdir=getcwd()."/../../..";	
	require_once($wpdir."/wp-load.php"); 
	
	$comingContent=stripslashes($_POST['text']);
	$contentOnEditStart=stripslashes($_POST['originalText']);
	if (current_user_can("edit-ontology")) {
		
		$post_id=$_POST['post_id'];
		$my_post=get_post($post_id);	

		if ($my_post->post_content!=$contentOnEditStart) {
			echo "Your modification can not be saved, because somebody else already saved a modification during your edit.";
			$f=fopen(plugin_dir_path(__FILE__)."save.log","w");
			fwrite($f,$my_post->post_content);
			fclose($f);
			$f=fopen(plugin_dir_path(__FILE__)."save1.log","w");
			fwrite($f,$_POST['originalText']);
			fclose($f);
			return;
		}
		
		$my_post->post_content = $comingContent; 			
		
		try {
			$trid = $wpdb->get_results("SELECT * FROM wp_icl_translations WHERE element_id=" . $post_id, ARRAY_A);			
		}
		catch (Exception $e) {
			echo 'Caught exception: ', $e->getMessage(), "\n";
		}
		
		wp_update_post( $my_post );

		if (sizeof($trid)==1) {		
			$trid=$trid[0];
			unset($trid["translation_id"]);	
			$format=array("%s","%d","%d","%s");
			if ($trid["source_language_code"]==null) 
				unset($trid["source_language_code"]);
			else array_push($format,"%s"); 
			
			$success=$wpdb->update("wp_icl_translations",$trid, 
					array( 'element_type' => $trid["element_type"], 'element_id' => $trid["element_id"] ) );
			if (!$success) {
				$wpdb->show_errors(); 
				$wpdb->print_error();
				echo "Insert failed, you lost the translation connection in the wp_icl_translations.\n"; 
				var_dump($trid);
				echo "\n";
			}			
		}
		//ti_triggerCrawl($post_id); the update_post already does this.
	}	
	echo 'OK';
?>
