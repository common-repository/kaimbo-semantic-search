<?php 
//this file contains the code for the Easy login and Options link related popups.

if (!is_user_logged_in()) { 

global $tisearch_serverUrl;
global $ti_accessKey;
?>
<div id="ti-search-wp-login" class="ti-search-popup"
	style="display: none; position: absolute; overflow: visible;">
	<form action="<?php echo get_option('home'); ?>/wp-login.php"
		method="post">
		<p>
			<span class="ti-login-label"><?php echo __('User Name','ti-search'); ?>
			</span><input type="text" name="log" id="kaimbo-loginput" value="" size="20">
		</p>
		<p>
			<span class="ti-login-label"><?php echo __('Password','ti-search'); ?>
			</span><input type="password" name="pwd" id="pwd" size="20">
		</p>
		<p style="float: left">
			<label for="rememberme"><input name="rememberme" id="rememberme"
				type="checkbox" checked="checked" value="forever"> <?php echo __('Remember me','ti-search'); ?>
			</label>
		</p>
		<p style="float: right">
			<input id="kaimbo-loginbtn" type="submit" name="submit" class="ti-button btn btn-mini"
				value="<?php echo __('&#xf00c; Log In','ti-search');?>" class="button">
		</p>
		<input type="hidden" name="redirect_to"
			value="<?php echo $_SERVER['REQUEST_URI']; ?>">
	</form>
	<div class="ti-cancel btn" onclick="TISearchPopups.closeLoginPopup()">&#xf057;</div>
</div>

<div id="ti-pencil" style="position: absolute; display: none;">
	<a href="javascript:void();" onclick="TISearchPopups.showLogin(this,0,0);"><?php echo __('WordPress Log In','ti-search'); ?></a>
</div>

<script type="text/javascript">
	TISearchPopups.closeLoginPopup=function() {
		var logindiv=document.getElementById("ti-search-wp-login");	
		logindiv.style.display="none";	
	};
	
	TISearchPopups.showLogin=function(me,xdist,ydist) {
		var logindiv=document.getElementById("ti-search-wp-login");				
		var offset=me.getBoundingClientRect();
		logindiv.style.display="block"; //need this here because the width is 0 before.
		
		var wi=offset.left+xdist;
		TISearchPopups.fixPosition(wi,offset.top+ydist,logindiv);

		function chromeHack(input) {
			var text = jQuery(input).val();
			var name = jQuery(input).attr('name');
			jQuery(input).after(input.outerHTML).remove();
			jQuery('input[name=' + name + ']').val(text);
		};		
		
		var pwd=document.getElementById("pwd");
		chromeHack(pwd);
		var input=document.getElementById("kaimbo-loginput");
		chromeHack(input);
		input.select();				

		var btn=document.getElementById("kaimbo-loginbtn");				
		btn.focus();
	};

	//hack for the stupid chrome bug, to not paint the edit boxes yellow
	//it still does it in some cases, because they have a !important browser style.
//	if (navigator.userAgent.toLowerCase().indexOf("chrome") >= 0) {
//		jQuery(window).load(function(){
//			jQuery('input:-webkit-autofill').each(function(){
//				var text = jQuery(this).val();
//				var name = jQuery(this).attr('name');
//				jQuery(this).after(this.outerHTML).remove();
//				jQuery('input[name=' + name + ']').val(text);
//			});
//		});
//  }
</script>
<?php
	$options = get_option('ti_search_options');
	if ($options[showLoginLink]=='off')
		return;
}

//$current_url = add_query_arg( $wp->query_string, '', home_url( $wp->request ) )
if (is_user_logged_in()) { ?>
<div id="ti-pencil" style="position: absolute; display: none;">
	<a href="javascript:void(0)" onclick="TISearchPopups.showOptionsPopup(this);">KAIMBO - WordPress Options</a>
</div>

<script type="text/javascript">
// do not move these 3 functions from here, we include them only if there is a wordpress user logged in !
TISearchPopups.showOptionsPopup=function(element) {		
	var div =jQuery('<div/>', {
		'style' : 'position:absolute;',
		'id': 'TiOptions',
		'class': 'ti-search-popup',
		'tabindex': '0'
	});		
	
	var list='<a href="<?php echo admin_url(); ?>options-general.php?page=ti-search"><div class="termLink"><?php echo __("KAIMBO Options - Backend","ti-search"); ?></div></a>';		
	list+='<a href="javascript:void(0)" onclick="TISearchPopups.showOntology(this); TISearchPopups.closePopup(\'TiOptions\',true);"><div class="termLink"><?php echo __("Knowledge Base - Vocabulary","ti-search"); ?></div></a>';
	list+='<a href="<?php echo wp_logout_url($_SERVER['REQUEST_URI']); ?>"><div class="termLink"><?php echo __("WordPress Log Out","ti-search"); ?></div></a>';
	list+='<hr/>';
	list+='<a href="javascript:void(0)" onclick="TISearchPopups.aboutPopup(this); TISearchPopups.closePopup(\'TiOptions\',true);"><div class="termLink"><?php echo __("About KAIMBO","ti-search"); ?></div></a>';
	list+=TISearchPopups.getCancel("TISearchPopups.closePopup(\'TiOptions\',true);");
	div.html(list);
	div=div[0];
	
	div.onkeyup = function (event) {
		if (event.keyCode == 27) {
			TISearchPopups.closePopup('TiOptions',true);
		}
	};
	
	var offset=jQuery(element).offset();	
	document.body.appendChild(div);
	TISearchPopups.fixPosition(offset.left, offset.top+20, div);
	div.focus();
	//return false;
};

TISearchPopups.showOntology=function(element) {		
	var div = jQuery('<div/>', {
		'style' : 'position:absolute;',
		'id': 'TiOntology',
		'class': 'ti-search-popup',
		'tabindex': '0'
	});	

	var url='<?php echo $tisearch_serverUrl;?>ontologytable?base='+ti_wordpress_url+'&accesskey=<?php echo $ti_accessKey;?>';
	var list='<iframe id="ti-ontology-frame" src="'+url+'"></iframe>';
	list+=TISearchPopups.getCancel("TISearchPopups.closePopup(\'TiOntology\',true);");
	div.html(list);
	div=div[0];
	
	div.onkeyup = function (event) {
		if (event.keyCode == 27) {
			TISearchPopups.closePopup('TiOntology',true);
		}
	};

	document.body.appendChild(div);
	
	var offset=jQuery(element).offset();
	var left=offset.left;	
	TISearchPopups.fixPosition(left-20, offset.top-20, div);	
	div.focus();
};

TISearchPopups.aboutPopup=function(element) {		
	var div = jQuery('<div/>', {
		'style' : 'position:absolute;',
		'id': 'TiKaimboAbout',
		'class': 'ti-search-popup',
		'tabindex': '0'
	});	

	var list ='<div class="byDiv">by</div>';						
		list+='<a href="http://kaimbo.com" target="_blank"><img src="'+ti_plugin_dir+'/images/smallkaimbo.png" align="left"></a>';
		list+='<a id="KaimboLink" href="http://kaimbo.com" target="_blank"><span>www.kaimbo.com</span></a>';			
	    list+='<div id="TransinsightLink"><a href="http://transinsight.com" target="_blank"><span>www.transinsight.com</span></a></div>';
	    list+='<div id="TransinsightLogo"><a href="http://transinsight.com" target="_blank"><img src="'+ti_plugin_dir+'/images/ti.png" align=right"></a></div>';		    
	list+=TISearchPopups.getCancel("TISearchPopups.closePopup(\'TiKaimboAbout\',true);");
	div.html(list);
	div=div[0];
	
	div.onkeyup = function (event) {
		if (event.keyCode == 27) {
			TISearchPopups.closePopup('TiKaimboAbout',true);
		}
	};
	
	var offset=jQuery(element).offset();
	document.body.appendChild(div);
	TISearchPopups.fixPosition(offset.left-40, offset.top-70, div);	
	div.focus();
};
</script>

<?php } ?>
	
<script type="text/javascript">
	TISearchPopups.setLinkPosition=function() {		
		var list=tisearch_getSearchFields();
		if (list.length>0) {
			var edit=list[0];
			//var wrapper=edit.parentNode;			
			this.attachToBody(document.body,"ti-pencil");
			this.attachToBody(document.body,"ti-search-wp-login");			
			
			var offset=jQuery(edit).offset();					   	    	
			var pencil=document.getElementById("ti-pencil");


			pencil.style.display="block";				
			//+leftoption;//+edit.offsetWidth/2-pencil.offsetWidth/2;	
			var top=offset.top + edit.offsetHeight;//+topoption;		
//			var adminbar=document.getElementById('wpadminbar');
//			if (adminbar && !jQuery.browser.msie)
//				top=top+adminbar.offsetHeight;		
			
			//find if any of the parents of the searchbox have "position:fixed" and set the same to the #ti-pencil
			var fixedParentList = jQuery(edit).parents().filter(function(){
				return jQuery(this).css('position')=='fixed';
				});
			if (fixedParentList.size()>0){
				var top=offset.top+edit.offsetHeight;
				jQuery("#ti-pencil").css("position","fixed").offset({top:(offset.top+35)});
				var w=window,d=document,e=d.documentElement,g=document.body,x=w.innerWidth&&e.clientWidth&&g.clientWidth,y=w.innerHeight&&e.clientHeight&&g.clientHeight;
				pencil.style.right=((x - offset.left - edit.offsetWidth) + 5 + 'px');
				}
			
						
			if (<?php
				$options = get_option('ti_search_options');
				if ($options[alignRight]=='on') 
					echo 'true';
				else 
					echo 'false';
			?>)	{		
				//aligned right					    				
				pencil.style.top=(top) + 'px';
				//this gets the broser window widht and height.
				var w=window,d=document,e=d.documentElement,g=document.body,x=w.innerWidth&&e.clientWidth&&g.clientWidth,y=w.innerHeight&&e.clientHeight&&g.clientHeight;
				pencil.style.right=((x - offset.left - edit.offsetWidth) + 5 + 'px');
				
				
			}					
			else { //aligned left.
				TISearchPopups.fixPosition(offset.left,top,pencil);
			}			
		}
	};	
	jQuery(window).resize(function(){
		TISearchPopups.setLinkPosition();
	});
	jQuery(document).ready(function() {			
		TISearchPopups.setLinkPosition();   
 	});
 </script>	
 