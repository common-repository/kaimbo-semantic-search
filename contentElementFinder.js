//get the element which contains the wordpress page content. by default #content
function kaimbo_getContentBaseElement() {	
	function getInChildren(el) {
		var content='';
		if (el.textContent)
			content=el.textContent;
		else content=el.innerText;
		if (!content)
			return;
		
		content=content.replace(/\s/g, "");  //\u00a0 = &nbsp
		//console.log(content);
		
		if (content.indexOf(searchedContent)<0) 
			return ; //we are in a wrong branch
		
		for (var index in el.children) {
			var child=el.children[index];
			var inchildren=getInChildren(child);
			if (inchildren)
				return inchildren;
		}		
		
		//if all children returned null, but this is correct, we have the good parent !
		if (content==searchedContent) {			
			return el;
		}
	}

	var element=document.getElementById('content');	
	if (!element) {
		var el=jQuery('<div/>');
		el=el[0];
		el.innerHTML=ti_beforeHighlight; //the data from the wordpress post->content		
		
		if (el.textContent)
			searchedContent=el.textContent;					
		else searchedContent=el.innerText;
		
		searchedContent=searchedContent.replace(/\s/g, "");  //\u00a0 = &nbsp
		//console.log(searchedContent);
		element=getInChildren(document.body);
		if(!element) {
			console.log("contentBaseElement not found.");
			return null;
		}		
		//go up if needed in the divs without own textcontent until the correct is found
		el=el.children[0];
		while (element.children[0].id!=el.id || element.children[0].ClassNames!=el.ClassNames) {
			element=element.parentNode;
		}
	}

	return element;
}