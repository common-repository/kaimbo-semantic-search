TISearchPopups.showTermPopup=function(wrapper,termLabel,execute,executeLabel,synonyms,description,parents,termUri,haschildren) {
	this.showTermPopupReal(wrapper,termLabel,execute,executeLabel,synonyms,description,parents,termUri,haschildren,true);
};

TISearchPopups.addToOntology= function(div) {
	this.closePopup(div,true);
};

TISearchPopups.modifyTerm=function(div) {
	this.closePopup(div,true);
};

TISearchPopups.deleteTerm=function(div) {
	this.closePopup(div,true);
};
