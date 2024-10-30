<?php 
//this displays the indexing warning for the logged-in searchers.

if (!current_user_can( 'manage_options' )) 	
	return;	

if (kaimbo_wasFullyCrawled())
	return;

?>
<div id="ti-pencil-1" class="ti-search-popup" style="position: absolute; display: none;">
	<?php echo '<span>Page indexing is in progress. In the meanwhile the default wordpress search results are shown!</span>'; ?>
	<?php 
		$count=kaimbo_getWpPageCount()*1.3;  //adjust for estimate of pdf files.
		if (!$count || $count==0) $count=1;
		
		$result=tisearch_getJson($tisearch_serverUrl.'getpagecount?base='.ti_getWebsiteUrl(),'');
		if (is_numeric($result)) {
			$percent=floor($result*100/$count);
			if ($percent>99)
				$percent=99;
			echo "<br>$percent% done (refresh the page to see the progress)";
		}
	?>
	
	<div class="ti-cancel btn" onclick="TISearchPopups.closePopup('ti-pencil-1',true);">&#xf057;</div>
</div>		

<script type="text/javascript">
	TISearchPopups.setLinkPosition1=function() {		
		var list=tisearch_getSearchFields();
		if (list.length>0) {
			var edit=list[0];
			//var wrapper=edit.parentNode;			
			this.attachToBody(document.body,"ti-pencil-1");
			
			var offset=edit.getBoundingClientRect();						   	    	
			var pencil=document.getElementById("ti-pencil-1");
			pencil.style.display="block";				
			//+leftoption;//+edit.offsetWidth/2-pencil.offsetWidth/2;							
			var top=offset.top + edit.offsetHeight;//+topoption;	
			var optionEl=document.getElementById('ti-pencil');
			if (optionEl) top+=20;
			var adminbar=document.getElementById('wpadminbar');
			if (adminbar && !jQuery.browser.msie)
				top=top+adminbar.offsetHeight;	
			pencil.style.width=edit.offsetWidth+"px";
			TISearchPopups.fixPosition(offset.left,top,pencil);								
		}
	};	

	jQuery(window).load(function() {			
		TISearchPopups.setLinkPosition1();   
 	});
</script>
	