function tisearch_getSearchFields() {
	// we use all searchboxes with name and id='s'
	//(the default wordpress theme has 2 search fields with id='s', which is incorrect by the way)
	var searchFields = Array();
	var created = 0;
	var list = document.getElementsByName('s');
	if (list.length > 1) {
		for ( var i = 0; i < list.length; i++) {
			if (list[i].id == 's') {
				//Element.extend(list[i]); // for IE7 activate the prototype.
				searchFields.push(list[i]);
				created++;
			}
		}
	}
	if (created == 0) {
		var el = document.getElementById('s'); // by id
		if (el == null && list.length > 0) {// if no element with id=s, we use all elements with name = 's'.
			for ( var i = 0; i < list.length; i++) {
				if (list[i] != null) {
					//Element.extend(list[i]); // for IE7 activate the prototype.
					searchFields.push(list[i]);
				}
			}
		}
		if (el!=null) {
			//Element.extend(el); // for IE7 activate the prototype.
			searchFields.push(el);
		}
	}
	return searchFields;
}

jQuery(window).load(function() {
	jQuery.ui.autocomplete.prototype._renderItem = function( ul, item){
		  var term = this.term.split(' ').join('|');
		  var re = new RegExp("(" + term + ")", "gi") ;
		  var t = item.label.replace(re,"<b>$1</b>");
		  return jQuery( "<li></li>" )
		     .data( "item.autocomplete", item )
		     .append( "<a>" + t + "</a>" )
		     .appendTo( ul );
		};
		jQuery("#keyword").autocomplete({ 
		  source: "ajax/autocomplete.php?action=keyword", 
		  minLength: 2 
	});
	
	var onSelect=function(event, ui) {
    	if (this.form) { 
    		this.form.submit();
    	}
	};
	// ti_serverUrl is declared by the caller kaimbo.php
	var searchFields = tisearch_getSearchFields();
	for ( var i = 0; i < searchFields.length; i++) {		
		kaimbo_createAutocomplete(searchFields[i],onSelect);
		
		searchFields[i].onkeydown = function (event) {
			if (event.keyCode == 13) {
				if (this.value=="#login") {
					//don't propagete the event forward	
					event = event || window.event;
					if (event.stopPropagation)
						event.stopPropagation();
			        event.cancelBubble = true;
			        if (event.preventDefault)
			        	event.preventDefault();
			        event.returnValue=false;
					return false;
				}
			}
		};
		searchFields[i].onkeyup = function (event) {
			if (event.keyCode == 13) {
				if (this.value=="#login") {
					var test=document.getElementById("ti-search-wp-login");
					if (test) {
						TISearchPopups.showLogin(this,0,40);
					}
					else {
						alert('You are already logged in');
					}
					//don't propagete the event forward	
					event = event || window.event;
					if (event.stopPropagation)
						event.stopPropagation();
			        event.cancelBubble = true;
			        if (event.preventDefault)
			        	event.preventDefault();
			        event.returnValue=false;
					return false;
				}
			}
		};
	}
});

function kaimbo_createAutocomplete(searchField, onSelect) {
//	new Autocomplete(searchFields[i], i, {
//	serviceUrl : ti_serverUrl,
//	minChars : 2,
//	onSelect : function(suggestion, data) {
//		document.forms['searchform'].submit();
//	},
//	relativeParent : searchFields[i].parentNode,
//	ontologyBranch : ti_wordpress_url
//});
//if (!kaimbo_suggestor) {
	var jsonConf = new com.transinsight.JsonConf(ti_serverUrl,null);
    kaimbo_suggestor = new com.transinsight.yggdrasil.jsonapi.JsonQuerySuggestComponent(jsonConf);
    try {
  	  kaimbo_suggestor.setOntologyBranchLabel(ti_wordpress_url, function() {});
    }
  	catch (e) {
  		console.log('error setting the ontology label');
  		return;
  	}
//}

  	jQuery(searchField).autocomplete({
      source: function (request, response) {
        kaimbo_suggestor.suggest(request.term, request.term.length, convertResponse);
        
        function convertResponse(obj) {		        	
      	  	var suggestions = [];
            var newtexts= [];
            var uris= [];
            for(var i=0; i<obj.result.length; i++) {
          	  uris[i]=obj.result[i].term.uri;		          	 
          	  suggestions[i]=obj.result[i].finding;
          	  if (suggestions[i]==null)
          		  suggestions[i]='';
          	  newtexts[i]=suggestions[i];		            
            
          	  var syncount=obj.result[i].synonyms.length;    	  
          	  if (syncount>3)
          		  syncount=3;
          	  for (var j=0; j<syncount; j++) {
          		  var add="";
          		  if (j==0) 
          			  add=' <span>&nbsp;&nbsp;&nbsp;';
          		  else add=", ";
          		  add=add+obj.result[i].synonyms[j];
          		  if (j==syncount-1) {
          			  if (obj.result[i].synonyms.length>syncount)
          				  add=add+", ...";
          			  add=add+"";
          			  add=add+"</span>";
          		  }
          		  suggestions[i]=suggestions[i]+add;
          	  }
      	  
          	  if (newtexts[i].indexOf(" ")>-1)
          		  newtexts[i]='"'+newtexts[i]+'"';    	      	
            }		        
        
            var result=[];
            for(var i=0; i<obj.result.length; i++) {
            	var sugg={"id":uris[i],
            			"label":suggestions[i],
            			"value":newtexts[i]};
            	result[i]=sugg;
            }
        		        	           
            response(result);		        
        }
      },
      minLength: 2,
      delay: 100,
      html: true,
      select: onSelect,    
      open: function(event,ui) {    	  
    	  var offset=jQuery('.ui-autocomplete').offset();
    	  jQuery(".ui-autocomplete").css("left",(offset.left+6)+"px");
      }
  	});
}
