<div id="kaimbo_feedback" style="display:none" class="ti-search-popup">
	<form action="<?php echo $tisearch_serverUrl.'feedback/'; ?>">
		<input type="hidden" name="redirectUrl" value="<?php echo ti_getWebsiteUrl().'.'.$_SERVER['REQUEST_URI']; ?>">
		<input type="hidden" name="website" value="<?php echo ti_getWebsiteUrl(); ?>">
		<input type="checkbox" name="It is too slow."> <span>It is too slow.</span><br/>
		<input type="checkbox" name="Bad or no search results."> <span>Bad or no search results.</span><br/> 
		<input type="checkbox" name="It is changing the layout or behaviour of my pages."> <span>It is changing the layout or behaviour of my pages.</span><br/>		
		<input type="checkbox" name="I don't want to use an external service for the search."> <span>I don't want to use an external service for the search.</span><br/>
		<input type="checkbox" name="I don't want to extend the knowledge base."> <span>I don't want to extend the knowledge base.</span><br/>
		<br/>
		<input type="text" name="email"> <span>email address (optional)</span><br/>		
		My opinion about the plugin (optional):<br/>
		<textarea name="freetext"></textarea>		
		<br/>		
		<input type="Submit" class="ti-button btn btn-mini" value="Send">		
	</form>
	<div class="ti-cancel btn" onclick="TISearchPopups.hidePopup('kaimbo_feedback'); TISearchPopups.removeOverlay();">&#xf057;</div>
</div>

<script type="text/javascript">
</script>
