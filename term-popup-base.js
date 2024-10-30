TISearchPopups = new function() {

this.showAddTermOrSynonym= function(x,y,text,commonAncestor) {
	var test=document.getElementById("AddToOntology");
	if (test) return;
	test=document.getElementById("MultipleTerms");
	if (test) return;
	
	var clickAction="TISearchPopups.correctSpelling(\'AddAsTermOrSynonym\'); TISearchPopups.closePopup(\'AddAsTermOrSynonym\');";
	var disableStr="";
	var base=kaimbo_getContentBaseElement(); //if we don't find that (the page has some extra element in it like [contactform]), the page is not editable.
	if (!ti_is_page || TISearchPopups.editAllowed!="true" || !base || ti_beforeHighlight.replace(/\s/g, "")=="") {
		disableStr='disabled="disabled"';  //if the content is empty, we don't enable the edit paragraph (dynamic pages).
		clickAction="";
	}
	
	var div = jQuery('<div/>', {
		'style' : 'position:absolute;',
		'id': 'AddAsTermOrSynonym',
		'class': 'ti-search-popup',
		'tabindex': '0'
	});	
	div=div[0];
	
	var list='<p><label>Add to Knowledge Base</label><br>'
		+'<input type="text" id="text"></input><br>';
	list+='<span onclick="TISearchPopups.showTermPopup(document.getElementById(\'AddAsTermOrSynonym\'),TISearchPopups.getChildInputValue(this.parentNode,\'text\'),\'addToOntology\', \'Add\'); TISearchPopups.closePopup(\'AddAsTermOrSynonym\'); ">add as concept</span><br>';
	list+='<span onclick="TISearchPopups.addAsSynonymPopup(\'AddAsTermOrSynonym\',TISearchPopups.getChildInputValue(this.parentNode,\'text\')); TISearchPopups.closePopup(\'AddAsTermOrSynonym\');">add as synonym</span></p>';
	list+='<p><label>More options:</label><br>';
	list+='<span onclick="'+clickAction+'"'+disableStr+'>edit paragraph</span><br>';
	//list+='<span onclick="xy(\'AddAsTermOrSynonym\',\'); TISearchPopups.closePopup(\'AddAsTermOrSynonym\');" disabled="disabled">edit knowledge base</span></p>';	
	
	list+=this.getCancel("TISearchPopups.closePopup(\'AddAsTermOrSynonym\',true);");
	div.innerHTML=list;	
	
	this.setChildInputValue(div, 'text', text);
	
	div.onkeyup = function (event) {
		if (event.keyCode == 27) {
			TISearchPopups.closePopup('AddAsTermOrSynonym', true);
		}
	};
		
	commonAncestor.className+=" ti-commonAncestor";
	//there is no other way here than handeling the admin bar manually.	
	document.body.appendChild(div);
	this.fixPosition(x, y, div);
	div.focus();
};

this.getChildInputValue= function(div, id) {
	for ( var i = 0; i < div.childNodes.length; i++) {
		if (div.childNodes.item(i).id == id && div.childNodes.item(i).tagName=="INPUT") {
			return div.childNodes.item(i).value;
		}
		var res=this.getChildInputValue(div.childNodes.item(i),id);
		if (res) 
			return res;
	}	
}

//left, top, term label, the javascript method to execute
this.showTermPopupReal=function(wrapper,termLabel,execute,executeLabel,synonyms,description,parents,termUri,hasChildren,disabled) {
		var test=document.getElementById("AddToOntology");
		if (test) return;					
		var test=document.getElementById("AddAsSynonym");
		if (test) return;
		
		if (!synonyms) synonyms='';
		if (!description) description='';
		var div = jQuery('<div/>', {
			'style' : 'position:absolute;',
			'id': 'AddToOntology',
			'class': 'ti-search-popup',
			'tabindex': '0'
		});		
		div=div[0];
		
		var disablingD='';
		if (disabled) {
			disablingD='disabled="disabled"';
			executeLabel='Close';
		}		
		if (execute=='modifyTerm') {
			if(hasChildren=='true')
				disablingD='disabled="disabled"';	//delete		
		}		
		
		var popup='<span id="termLabel" style="font-weight:bold">Concept: </span> <br>'
						+ '<input type="hidden" id="origLabel">'
						+ '<input type="hidden" id="termUri" value="'+termUri+'">'
						+ '<input type="hidden" id="hasChildren" value="'+hasChildren+'">'
						+ '<input type="text" id="termLabel">'
						+ '<div class="redX hideUnlogged" style="float:right" onclick="TISearchPopups.deleteTerm(\'AddToOntology\');" '+disablingD+' title="Deletes a concept from the background knowledge">&#xf00d;</div>'
						+' <br> <br>'
						+ this.getSynonymBox(synonyms)
						+ '<div id="more" onclick=TISearchPopups.descriptionOnOff(this)> <span id="showmoreDescription">Show description &#xf078;</span> <span id="showlessDescription" style="display:none">Hide description &#xf077;</span> </div>'
						+ '<div id="kaimbo_DescriptionDiv" class="kaimbo_DescriptionDiv">'+						
							this.getParentBox(parents)+
							'<span style="font-weight:bold">Description: </span><br> <textarea id="description" rows="5">'+description+'</textarea></div>'
						+ this.getCancel("TISearchPopups.closePopup(\'AddToOntology\',true);");		
		popup=popup+ '<input id="AddOntologyBtn" type="button" class="ti-button btn btn-mini hideUnlogged" value="&#xf00c; '+executeLabel+'" onclick="TISearchPopups.'+execute+'(\'AddToOntology\')" style="float:right" />';
		div.innerHTML=popup;		
		
		this.setChildInputValue(div,"termLabel",termLabel);
		this.setChildInputValue(div,"origLabel",termLabel);
		
		div.onkeyup = function (event) {
			if (event.keyCode == 13) {
				TISearchPopups.newSynonym();
			}
			if (event.keyCode == 27) {
				TISearchPopups.closePopup('AddToOntology',true);
			}
		};
					
		var offset=jQuery(wrapper).offset();		
		document.body.appendChild(div);		
		this.fixPosition(offset.left, offset.top, div);
		div.focus();
		
		var parentField=document.getElementById("addParent");
		
		var old=document.getElementById("Autocomplete_addParent-1"); 
		if (old) old.parentNode.removeChild(old);				
//		new Autocomplete(parentField,-1, { serviceUrl:ti_serverUrl, minChars:2, onSelect:function(suggestion, data, termUris){			
//			parentField.value=data;
//			var parentUri=document.getElementById("addParentUri");
//			parentUri.value=termUris.namespace+termUris.uriId;
//		}, relativeParent:parentField.parentNode, ontologyBranch:ti_wordpress_url} );		
};

//gets the  child Input field with this id
this.setChildInputValue= function(div,id,value) {
	for ( var i = 0; i < div.childNodes.length; i++) {
		if (div.childNodes.item(i).id == id && div.childNodes.item(i).tagName=="INPUT") {
			div.childNodes.item(i).setAttribute('value',value);
			return true; 		
		}
		var res=this.setChildInputValue(div.childNodes.item(i),id,value);
		if (res==true) 
			return true;
	}	
}

this.descriptionOnOff=function(el, divid, count) {
	if (!divid) divid="kaimbo_DescriptionDiv";
	if (!count) count='';
	if (!jQuery(el).hasClass("on")) {
		jQuery(el).addClass("on");		
		jQuery('#'+divid).addClass("on");
		document.getElementById("showmoreDescription"+count).style.display="none";
		document.getElementById("showlessDescription"+count).style.display="block";
	}
	else {
		jQuery(el).removeClass("on");
		jQuery('#'+divid).removeClass("on");
		document.getElementById("showmoreDescription"+count).style.display="block";
		document.getElementById("showlessDescription"+count).style.display="none";
	}
};

//left, top, term label, the javascript method to execute
this.addAsSynonymPopup= function(wrapperId,termLabel, disabled) {
		var wrapper=document.getElementById(wrapperId);
		var test=document.getElementById("addAsSynonym");
		if (test) return;
		
		var div = jQuery('<div/>', {
			'style' : 'position:absolute;',
			'id': 'AddAsSynonym',
			'class': 'ti-search-popup',
			'tabindex': '0'
		});		
		div=div[0];
		
		var disabling='';
		if (disabled)
			disabling='disabled="disabled"';
		var popup='<span id="termLabel" style="font-weight:bold">Synonym: </span> <br>'						
					+ '<input type="text" id="synonym"> <br> <br>' 											
					+'<span style="font-weight:bold">Concept: </span><br>'
					+'<input type="text" id="addSynonym" /><br><br>'
					+'<input type="hidden" id="addSynonymUri" />'												
					+this.getCancel("TISearchPopups.closePopup(\'AddAsSynonym\',true);");								
		popup=popup +'<input type="button" class="ti-button btn btn-mini" value="&#xf00c; Add" onclick="TISearchPopups.addAsSynonym(\'AddAsSynonym\')" style="float:right" '+disabling+'/>';
		div.innerHTML=popup;		
		
		this.setChildInputValue(div, 'synonym', termLabel);
		
		div.onkeyup = function (event) {
			if (event.keyCode == 27) {
				TISearchPopups.closePopup('AddAsSynonym',true);
			}
		};
		
		var offset=jQuery(wrapper).offset();						
		document.body.appendChild(div);
		this.fixPosition(offset.left, offset.top, div);
		div.focus();
		var parentField=document.getElementById("addSynonym");
		
		var old=document.getElementById("Autocomplete_addSynonym-1"); 
		if (old) old.parentNode.removeChild(old);				
		
//		new Autocomplete(parentField,-1, { serviceUrl:ti_serverUrl, minChars:2, onSelect:function(suggestion, data, termUris){			
//			parentField.value=data;
//			var parentUri=document.getElementById("addSynonymUri");
//			parentUri.value=termUris.namespace+termUris.uriId;
//		}, relativeParent:parentField.parentNode, ontologyBranch:ti_wordpress_url} );		
		var onSelect=function(event, ui){			
			parentField.value=ui.item.label;
			var parentUri=document.getElementById("addSynonymUri");
			parentUri.value=ui.item.id.namespace+ui.item.id.uriId;
		};
		kaimbo_createAutocomplete(parentField,onSelect);
};

this.getSynonymBox=function(synonyms) {	
	if (synonyms=='') synonymCount=0;
		else synonymCount=synonyms.length;
	var string='<div id="ti-synonyms">'+		
		'<span style="font-weight:bold">Related concepts: </span><br>';		

	for (var i=0; i<synonymCount; i++) {
		string+=this.getSynonymDiv(synonyms[i],i);
	}	
	string+="</div>";
	string+='<div id="synonymplus" class="hideUnlogged">'+
		'<input type="text" id="addSynonym" />'+
		'<div class="plus" onclick="TISearchPopups.newSynonym()">&#xf067;</div>'+
	'</div>';
	return string;
};

this.getSynonymDiv=function(synonym,i) {
	var box=
		'<div id="synonym-'+i+'">'+
			'<li id="synonym-input-'+i+'" class="synonym" disabled="disabled" />'+synonym+
				'<div class="redX hideUnlogged" onclick="TISearchPopups.removeSynonym('+i+')">&#xf00d;</div>'+
			'</li>'+
		'</div>';
	return box;
};

this.removeSynonym=function(i) {
	var el=document.getElementById('synonym-'+i);
	el.parentNode.removeChild(el);
};

this.newSynonym=function() {
	var syn=document.getElementById('addSynonym');
	var el=jQuery('<div/>');
	el=el[0];
	var value=syn.value;
	if (value.length=='')
		return;
	if (!(/\d/.test(value)) && !(/[a-zA-Z]/.test(value)))
		return; //if it has no letter and no digit, we don't accept it.
	
	var syndiv=this.getSynonymDiv(value, synonymCount++);
	el.innerHTML=syndiv;
	el=el.childNodes[0];
	var div=document.getElementById('ti-synonyms');
	div.appendChild(el);
	syn.value="";
};

this.getParentBox=function(parents) {		
	if (parents) parentCount=parents.length;
		else parentCount=0;
	var string='<div id="ti-parents" style="display:none">'+		
		'<span style="font-weight:bold" title="Parent Terms">Topics: </span>'+
		'<div class="hideUnlogged">'+
			'<input type="text" id="addParent" />'+
			'<input type="hidden" id="addParentUri" />'+
			'<div class="plus" onclick="TISearchPopups.newParent()" >&#xf067;</div>'+
		'</div>';

	for (var i=0; i<parentCount; i++) {
		string+=this.getParentDiv(parents[i],i);
	}
	string+="<br></div>";
	return string;
};

this.getParentDiv=function(parent,i) {
	parent=parent.split("\t");
	var box=
		'<div id="parent-'+i+'"'+
			'<li id="parent-input-'+i+'" class="parent" disabled="disabled" />'+parent[0]+			
				'<div class="redX hideUnlogged" onclick="TISearchPopups.removeParent('+i+')">&#xf00d;</div>'+
			'</li> <input type="hidden" id="parent-uri-'+i+'" class="parent" value="'+parent[1]+'" />'+
		'</div>';	
	return box;
};

this.removeParent=function(i) {
	var el=document.getElementById('parent-'+i);
	el.parentNode.removeChild(el);
};

this.newParent= function() {
	var parent=document.getElementById('addParent');
	var parentUri=document.getElementById('addParentUri');
	var el=jQuery('<div/>'); el=el[0];
	el.innerHTML=this.getParentDiv(parent.value+"\t"+parentUri.value, parentCount++);
	el=el.childNodes[0];
	var div=document.getElementById('ti-parents');
	div.appendChild(el);
	parent.value="";
};

this.fixPosition=function(x, y, div, wrapper) {
	var w=window,d=document,e=d.documentElement,g=document.body,tx=w.innerWidth&&e.clientWidth&&g.clientWidth;
	var ty=w.innerHeight&&e.clientHeight; if (g.clientHeight>0) ty=ty&&g.clientHeight;
	
    var wi=div.offsetWidth;  // start it more left if it is longer than the available place on the right in the browser.
    if (x+wi>tx-20) {
    	x=tx-wi-20;
    }
//    
//    var he=div.offsetHeight;  // start it more on the top if it goes below the available place on the right in the browser.
//    if (ty>0 && y+he>ty-1) {
//    	y=ty-he-1;
//    }
    
	div.style.top=(y) + 'px';
	div.style.left=(x+5) + 'px';		
	
//	if (!wrapper) return;
//	var parent=wrapper;
//	while (parent!=document.body) {				
//		if (parent.getStyle("overflow")=="hidden")
//			if (parent.getWidth()<x+div.getWidth() || parent.getHeight()<y+div.getHeight()) {
//				parent.setStyle({ overflow: 'visible' });				
//			}
//		parent=parent.parentNode;
//	};
};

this.closePopup=function(elId, removeAncestorClass, dispose) {
	if (removeAncestorClass) {
		jQuery('.ti-commonAncestor').removeClass('ti-commonAncestor');
	}
	var el=document.getElementById(elId);
	el.parentNode.removeChild(el);
	
	if (dispose) {
		el=document.getElementById(dispose);
		el.parentNode.removeChild(el);
	}
};

this.hidePopup=function(elid) {
	var el=document.getElementById(elid);
	el.style.display="none";
};

this.termPopup=function(event, element, terms) {	
	var test=document.getElementById("AddAsTermOrSynonym");
	if (test) return;
	
	event = event || window.event;
	if (event.button==0) {
		//alert (event.button);		
		if (event.stopPropagation)
			event.stopPropagation();
        event.cancelBubble = true;
        if (event.preventDefault)
        	event.preventDefault();
        event.returnValue=false;
	}
	else return; //don't show the popup, it was the middle button pressed.
	
	var test=document.getElementById("FollowOrEdit");
	if (!test) { 
		if (element.parentNode.tagName=="A") {
			this.showFollowOrEdit(event, element, terms);
			return;
		}
	}
	
	if (terms.length==1) {		
		var synonyms=terms[0].synonyms.split('\t');
		this.showTermPopup(element, terms[0].label, 'modifyTerm', 'Save', synonyms, terms[0].description, terms[0].parents, terms[0].uri, terms[0].hasChildren);		
		return;			//25 is ~ 1 line height, so we show below the clicked word.
	}	
	
	var test=document.getElementById("AddToOntology");
	if (test) {  return; }
	var test=document.getElementById("MultipleTerms");
	if (test) { return;  }
	var test=document.getElementById("AddAsTermOrSynonym");
	if (test) { return; }
	
	var div = jQuery('<div/>', {
		'style' : 'position:absolute;',
		'id': 'MultipleTerms',
		'class': 'ti-search-popup',
		'tabindex': '0'
	});	
	div=div[0];
	
	var list='<span id="termLabel" style="font-weight:bold">Multiple Concepts Found: </span>';
	for (var i=0; i<terms.length; i++) {
		var termStr=JSON.stringify(terms[i]).replace(/"/g,"'").replace(/\'\[/g,"[").replace(/\]\'/g,"]").replace(/\\\'/g,"'").replace(/\\t/g,"\t");
		list+='<div class="termLink" onclick="TISearchPopups.termPopup(null,document.getElementById(\'MultipleTerms\'),['+termStr+']); TISearchPopups.closePopup(\'MultipleTerms\');">'+
			   terms[i].label+'</div>';							
	}
	list+=this.getCancel("TISearchPopups.closePopup(\'MultipleTerms\',true);");
	div.innerHTML=list;	
	
	div.onkeyup = function (event) {
		if (event.keyCode == 27) {
			TISearchPopups.closePopup('MultipleTerms',true);
		}
	};
	
	var offset=jQuery(element).offset();	
	document.body.appendChild(div);
	this.fixPosition(offset.left, offset.top, div);
	div.focus();
	
	return;
};

this.showFollowOrEdit=function(event, element, terms) {		
	var div = jQuery('<div/>', {
		'style' : 'position:absolute;',
		'id': 'FollowOrEdit',
		'class': 'ti-search-popup',
		'tabindex': '0'
	});	
	div=div[0];
	
	var list=this.followlink(element);
	list+='<span id="termLabel" style="font-weight:bold">Edit concept: </span><br>';
	
	for (var i=0; i<terms.length; i++) {
		var termStr=JSON.stringify(terms[i]).replace(/"/g,"'").replace(/\'\[/g,"[").replace(/\]\'/g,"]").replace(/\\\'/g,"'").replace(/\\t/g,"\t");
		list+='<div class="termLink" onclick="TISearchPopups.termPopup(null,document.getElementById(\'FollowOrEdit\'),['+termStr+']); TISearchPopups.closePopup(\'FollowOrEdit\');">'+
			   terms[i].label+'</div>';							
	}
	list+=this.getCancel("TISearchPopups.closePopup(\'FollowOrEdit\',true);");
	div.innerHTML=list;	
	
	div.onkeyup = function (event) {
		if (event.keyCode == 27) {
			TISearchPopups.closePopup('FollowOrEdit',true);
		}
	};
	
	var offset=jQuery(element).offset();	
	document.body.appendChild(div);
	this.fixPosition(offset.left, offset.top, div);
	div.focus();
	
	return;
};

this.followlink=function(wrapper) {	
	var x= wrapper.parentNode.outerHTML;
	x.onclick=null;
	x=x.replace(/onclick=\"TISearchPopups.termPopup\(.*\)\"/gi,"");
	var tags= '<div class="FollowLink"><span id="termLabel" style="font-weight:bold">Follow link: </span><br>'
				+'<span class="termLink">'+x+'</spam>'
			 +'</div><br>';
    return tags;
};

this.getCancel=function(onclick) {
	if (onclick.indexOf('closePopup(')==0) {
		onclick="TISearchPopups."+onclick;
	}
	return '<div class="ti-cancel btn" onclick="'+onclick+'">&#xf057;</div>';
};

//function getWrapperRelativeOffset(wrapper,element) {
	//var offset=jQuery(element).position();
		//var wrapperOffset=Element.cumulativeOffset(wrapper);	
		//offset.left-=wrapperOffset.left;
		//offset.top-=wrapperOffset.top;
		//offset=jQuery(element).offset();
    //return offset;
//}

this.showFeedbackPopup=function(element) {
	this.showOverlay(9000);
	this.attachToBody(document.body, 'kaimbo_feedback');
	var el=document.getElementById('kaimbo_feedback');		
	el.style.display="block";
	this.fixPosition((jQuery(window).innerWidth()-el.offsetWidth)/2, (jQuery(window).innerHeight()-el.offsetHeight)/2, el);	
};

this.showOverlay=function(zindex) {
	var overlay = document.createElement("div");
	overlay.setAttribute("id","kaimbo_overlay");
	overlay.setAttribute("class", "kaimbo_overlay");
	overlay.style.width=jQuery(document).width()+"px";
	overlay.style.height=jQuery(document).height()+"px"; 
	
	if (zindex) {
		overlay.style.zIndex=zindex;
	}
	document.body.appendChild(overlay);
};

this.removeOverlay=function() {	
	var div=document.getElementById("kaimbo_overlay");
	div.parentNode.removeChild(div);
};

this.attachToBody=function(wrapper, id) {
	var pencil=document.getElementById(id);		
	if (pencil && pencil.parentNode!=wrapper) {				
		wrapper.appendChild(pencil);
	}
};

this.showResultsInAdmin=function(id, ypos) {
	this.showOverlay(9000);
	this.attachToBody(document.body, id);
	var el=document.getElementById(id);		
	el.style.display="block";
	this.fixPosition((jQuery(window).innerWidth()-el.offsetWidth)/2, /*(jQuery(window).innerHeight()-el.offsetHeight)/2*/ ypos, el);	
};

}
