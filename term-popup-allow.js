TISearchPopups.showTermPopup=function(wrapper,termLabel,execute,executeLabel,synonyms,description,parents,termUri,haschildren) {
	this.showTermPopupReal(wrapper,termLabel,execute,executeLabel,synonyms,description,parents,termUri,haschildren);
};

TISearchPopups.addToOntology=function(divId) {
	var div=document.getElementById(divId);
	var el=jQuery('<span/>',{ style: 'font-family: FontAwesome;' });
	el=el[0];
	el.innerHTML='&#xf110;';	
	div.appendChild(el);
	
	var jsonConf = new com.transinsight.JsonConf(ti_serverUrlIE, null);
	var editable = new com.transinsight.yggdrasil.WebsiteSearcherService.ontology.JsonWebsiteEditableOntologyComponent(
			jsonConf);
	
	var termLabel='', description = "";
	for ( var i = 0; i < div.childNodes.length; i++) {
		if (div.childNodes.item(i).id == "termLabel")
			termLabel = div.childNodes.item(i).value;		
		else if (div.childNodes.item(i).id == "description")
			description = div.childNodes.item(i).value;
	}

    var result=editable.addToOntologyL(ti_wordpress_url, ti_accesskey, termLabel, this.getSynonyms(), description, this.getParents()).result;
	
	if (result=="OK") {	
		div.id="refreshingPopup";	
		this.closePopup(div.id, true);
		ti_highlightTerms();
	}
	else {		
		jQuery(el).replaceWith('Could not add the concept.');
		if (result=="LimitReached")
			jQuery(el).replaceWith('You need KAIMBO Professional to add more than 10 concepts.');
		
		var btn=document.getElementById('AddOntologyBtn');
		btn.parentNode.removeChild(btn);
	}
};

TISearchPopups.modifyTerm=function(divId) {
	var jsonConf = new com.transinsight.JsonConf(ti_serverUrlIE, null);
	var editable = new com.transinsight.yggdrasil.WebsiteSearcherService.ontology.JsonWebsiteEditableOntologyComponent(
			jsonConf);

	var div=document.getElementById(divId);
	var termLabel='', description = "", termUri="";
	for ( var i = 0; i < div.childNodes.length; i++) {
		if (div.childNodes.item(i).id == "termLabel")
			termLabel = div.childNodes.item(i).value;
		else if (div.childNodes.item(i).id == "termUri")			
			termUri = div.childNodes.item(i).value;
		else if (div.childNodes.item(i).id == "description")
			description = div.childNodes.item(i).value;
	}
	
	editable.modify(ti_wordpress_url, ti_accesskey, termUri, termLabel, this.getSynonyms(), description, this.getParents());
	
	div.id="refreshingPopup";	
	this.closePopup(div.id,true);
	ti_highlightTerms();
};

TISearchPopups.getSynonyms=function() {
	var synonyms="";
	for (var i=0; i<synonymCount; i++) {
		var el=document.getElementById("synonym-input-"+i);
		if (el) {
			var syn;
			if (el.textContent)
				syn=el.textContent;
			else syn=el.innerText;
			if (syn.length>0) {
				synonyms+=syn.substr(0,syn.length-1)+"\n";
			}
		}
	}
	
	var el=document.getElementById("addSynonym");
	if (el) {
		var syn=el.value;
		if (syn.length>1) {
			synonyms+=syn+"\n";
		}
	}	
	
	return synonyms;
};

TISearchPopups.getParents=function() {
	var parents="";
	for (var i=0; i<parentCount; i++) {
		var el=document.getElementById("parent-uri-"+i);
		if (el) {
			parents+=el.value+"\n";
		}
	}
	return parents;
};

TISearchPopups.deleteTerm=function(divId) {
	this.showAreYouSure(divId, 'Are you sure you want to delete this concept and it\'s related terms ?', 'reallyDeleteTerm');
};

TISearchPopups.reallyDeleteTerm=function(divId) {		
	var jsonConf = new com.transinsight.JsonConf(ti_serverUrlIE, null);
	var editable = new com.transinsight.yggdrasil.WebsiteSearcherService.ontology.JsonWebsiteEditableOntologyComponent(
			jsonConf);

	var div=document.getElementById(divId);
	var termUri="";
	for ( var i = 0; i < div.childNodes.length; i++) {		
		if (div.childNodes.item(i).id == "termUri") {			
			termUri = div.childNodes.item(i).value;
			break;
		}
	}	
	editable.deleteTerm(termUri, ti_wordpress_url, ti_accesskey);
	
	div.id="refreshingPopup";	
	this.closePopup(div.id, true);
	ti_highlightTerms();
};

TISearchPopups.showAreYouSure=function(divId,message,execute) {
	if (execute.indexOf('TISearchPopups')!=0) {
		execute='TISearchPopups.'+execute;
	}
	var element=document.getElementById(divId);			
	var offset=jQuery(element).offset();
	
	var overlay = document.createElement("div");
	overlay.setAttribute("id","yesnoOverlay");
	TISearchPopups.fixPosition(offset.left-10, offset.top-10, overlay);
	overlay.style.width=element.offsetWidth+15+"px";
	overlay.style.height=element.offsetHeight+20+"px";
	document.body.appendChild(overlay);
	
	var div = jQuery('<div/>', {
		'style' : 'position:absolute;',
		'id': 'AreYouSure',
		'class': 'ti-search-popup',
		'tabindex': '0'
	});
	div=div[0];
		
	list=message+"<br><br>";
	list+='<input type="button" class="ti-button btn btn-mini" value="&#xf00d; '+'No'+'" onclick="TISearchPopups.closePopup(\'AreYouSure\',true,\'yesnoOverlay\');" style="float:left;" />';
	list+='<input type="button" class="ti-button btn btn-mini" value="&#xf00c; '+'Yes'+'" onclick="'+execute+'(\''+divId+'\'); TISearchPopups.closePopup(\'AreYouSure\',true,\'yesnoOverlay\');" style="float:right;" />';	
	list+='</div>';
	list+=this.getCancel("TISearchPopups.closePopup(\'AreYouSure\',true,\'yesnoOverlay\');");	
	div.innerHTML=list;	
	
	div.onkeyup = function (event) {
		if (event.keyCode == 27) {
			TISearchPopups.closePopup('AreYouSure',true,'yesnoOverlay');
		}
	};			
		
	document.body.appendChild(div);
	TISearchPopups.fixPosition(offset.left, offset.top, div);
		
	var disty=(element.clientHeight-div.offsetHeight)/2;
	var distx=(element.clientWidth-div.offsetWidth)/2;
	div.style.margin=disty+'px auto auto '+distx+'px';
	
	div.focus();
};

TISearchPopups.addAsSynonym=function(divId) {
	var div=document.getElementById(divId);
	var jsonConf = new com.transinsight.JsonConf(ti_serverUrlIE, null);
	var editable = new com.transinsight.yggdrasil.WebsiteSearcherService.ontology.JsonWebsiteEditableOntologyComponent(
			jsonConf);

	var label="",uri="";
	for ( var i = 0; i < div.childNodes.length; i++) {		
		if (div.childNodes.item(i).id == "synonym") {			
			label = div.childNodes.item(i).value;
		}
		if (div.childNodes.item(i).id == "addSynonymUri") {			
			uri = div.childNodes.item(i).value;
		}
	}
	
	editable.AddSynonym(ti_wordpress_url, ti_accesskey, uri,label);
	
	div.id="refreshingPopup";	
	this.closePopup(div.id, true);
	ti_highlightTerms();
};

TISearchPopups.correctSpelling=function(wrapperId) {	
	var el=jQuery('.ti-commonAncestor')[0];
	if (!el) return;
	
	this.contentBase=null;
	var contentBase=kaimbo_getContentBaseElement();	
	if (!contentBase) {
		console.log("contentbase not found in correctSpelling");
		return;
	}
	
	var parents=[];
	do {
		parents.push(el);
		var p=el.parentNode;							
		el=p;
	} while  (el!=contentBase);	
	el=jQuery('<div/>');
	el=el[0];
	el.id=contentBase.id; 
	el.className=contentBase.className;
	
	//set the page's original content in the element.
	var tagsHidden=ti_beforeHighlight.replace(/\[/g,'<p><span class="kaimbo-hidetag">[').replace(/\]/g,']</san></p>');
	el.innerHTML=tagsHidden;
	
	var post_content=el;
	for (var i=parents.length-1; i>=0; i--) {		
		var children=el.childNodes;
		for (var j=0; j<children.length; j++) {
			if (children[j].id==parents[i].id && children[j].tagName==parents[i].tagName && parents[i].className.indexOf(children[j].className)>-1)				 
			{ 
				var s1,s2;
				if (children[j].textContent) {
					s1=children[j].textContent;
					s2=parents[i].textContent;
				}
				else {
					s1=children[j].innerText;
					s2=parents[i].innerText;
				}
				if (!s1) s1='';
				if (!s2) s2='';
				s1=s1.replace(/\s/g, "");
				s2=s2.replace(/\s/g, "");  //\u00a0 = &nbsp
				//alert(s1); alert(s2);
				if (s1.length>0 && s2.length>0 && s1==s2) { //(s1.indexOf(s2)>-1 || s2.indexOf(s1)>-1)) {
					el=children[j];		
					break;
				}
			}
		}
	}
	if (/*el==contentBase &&*/ jQuery(el).html().length==0) {
		alert("Sorry, this part is dynamic content, it is not editable here.");
		jQuery('.ti-commonAncestor').removeAttribute('contenteditable');
		jQuery('.ti-commonAncestor').removeClass('ti-commonAncestor');
		return;
	}
	
	var accept;
	do {
		accept=true;
		var tag=el.tagName.toLowerCase();
		if (!(el==contentBase || el==document.body || tag=="div" || tag=="p")) {
			el=el.parentNode;
			accept=false;
		}
	} while (accept==false);
	
	jQuery('.ti-commonAncestor').removeClass('ti-commonAncestor');
	var dom_content=contentBase;
	ti_dom_content_before_edit=dom_content.innerHTML;
	dom_content.parentNode.replaceChild(post_content,dom_content);	
	this.contentBase=post_content;	
	jQuery(el).addClass("ti-commonAncestor");
	
	//remove the highlights (if somehow by a bug it was saved inside the page before.
	//jQuery('.TermAnnotation').replaceWith(jQuery('.TermAnnotation').html());
	
	el.setAttribute("contenteditable",true);
	var stupidadminbar=document.getElementById('wpadminbar');	
	if (stupidadminbar) {
		stupidadminbar.style.zIndex=9998;
	}
	editor = CKEDITOR.inline(el);	
	editor.resetDirty();
};

TISearchPopups.correctTheSpelling=function() {
	this.showOverlay();
	var el=jQuery('.ti-commonAncestor')[0];
	el.removeAttribute("contenteditable");
	jQuery(el).removeClass("ti-commonAncestor");
	editor.destroy();	
	
	var params={};	
	
	var contentBase=this.contentBase;
	
	var editedtext=contentBase.innerHTML.replace(/<p><span class="kaimbo-hidetag">\[/g,'[').replace(/\]<\/span><\/p>/g,']');
	
	params.text=editedtext;	
	params.post_id=ti_post_id;	
	params.originalText=ti_beforeHighlight;
	
	var onfinish=function() {
		if(this.readyState == 4 && this.status == 200) {
			if (this.responseText!='OK') {
				jQuery(contentBase).html(this.responseText);
				TISearchPopups.removeOverlay();
			}
			else {				
//				var stupidadminbar=document.getElementById('wpadminbar');	
//				if (stupidadminbar) {
//					stupidadminbar.style.zIndex=9999;
//				}
//				TISearchPopups.removeOverlay();
				window.location.reload();
			}
		}		
	};	
	result=this.sendPhpRequest(ti_plugin_dir+"savecorrection.php",params, onfinish);
};

TISearchPopups.sendPhpRequest=function(url, params, onfinish) {	
	// Mozilla/Safari
	var xmlHttpReq=null; 
	if (window.XMLHttpRequest) {
	    xmlHttpReq = new XMLHttpRequest();
	}
	// IE
	else if (window.ActiveXObject) {
	    xmlHttpReq = new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlHttpReq.open("POST", url, true);
	xmlHttpReq.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
	//self.xmlHttpReq.setRequestHeader("Content-length", QueryString.length);	
	xmlHttpReq.onreadystatechange = onfinish;
	var sparams="post_id="+params.post_id+"&text="+encodeURIComponent(params.text)+"&originalText="+encodeURIComponent(params.originalText);		
	return xmlHttpReq.send(sparams);
};

TISearchPopups.post_to_url=function(path, params) {
    var method = "post"; 
    // The rest of this code assumes you are not using a library.
    // It can be made less wordy if you use one.
    var form = document.createElement("form");
    form.setAttribute("method", method);
    form.setAttribute("action", path);

    for(var key in params) {
        if(params.hasOwnProperty(key)) {
            var hiddenField = document.createElement("input");
            hiddenField.setAttribute("type", "hidden");
            hiddenField.setAttribute("name", key);
            hiddenField.setAttribute("value", params[key]);

            form.appendChild(hiddenField);
         }
    }
    document.body.appendChild(form);
    form.submit();
};

TISearchPopups.cancelTheEditor=function(editor) {
	editor.destroy();
	var dom_content=this.contentBase;
	dom_content.innerHTML=ti_dom_content_before_edit;				
	var el=jQuery('.ti-commonAncestor')[0];
	if (el)
		el.removeAttribute("contenteditable");
	jQuery('.cke_focus').removeClass("cke_focus");
	jQuery(el).removeClass("ti-commonAncestor");
	
	var stupidadminbar=document.getElementById('wpadminbar');	
	if (stupidadminbar) {
		stupidadminbar.style.zIndex=9999;
	}
};
