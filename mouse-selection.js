//this file handles the text selection and the click event on the selected text.
(function() {
	// this wrap construct prevents the functions declared here to be visible
	// only here, and not conflict with anything else.

	if (!window.Kolich) {
		Kolich = {};
	}

	Kolich.Selector = {};
	Kolich.Selector.text = '';
	Kolich.commonAncestor = null;
	Kolich.cursorOverEditable = true;

	Kolich.Selector.mouseup = function() {
		if (jQuery('.ti-commonAncestor').length > 0) {
			return;
		}
		if (Kolich.Selector.text != '' && Kolich.commonAncestor
				&& !inSearchForm(Kolich.commonAncestor)) {
			TISearchPopups.showAddTermOrSynonym(Kolich.Selector.x,
					Kolich.Selector.y, Kolich.Selector.text,
					Kolich.commonAncestor);

			Kolich.Selector.text = '';
		} else
			markSelection();
	};

	function inSearchForm(el) {
		if (el.id == "searchform")
			return true;
		if (el == document.body)
			return false;
		return inSearchForm(el.parentNode);
	}

	jQuery(document).ready(function() {
		jQuery(document).bind("mouseup", Kolich.Selector.mouseup);
		// jQuery(document).bind("onselectstart",
		// Kolich.Selector.onselectstart);
		// jQuery(document).bind("onselectionchange",
		// Kolich.Selector.onselectstart);
	});

	// Kolich.Selector.onselectstart=function(event) {
	// if (event.stopPropagation)
	// event.stopPropagation();
	// event.cancelBubble = true;
	// if (event.preventDefault)
	// event.preventDefault();
	// event.returnValue=false;
	// return false;
	// };

	var markSelection = (function() {
		var markerTextChar = "";
		var markerTextCharEntity = "";

		return function() {
			var markerEl = null;
			var markerId = "sel_" + new Date().getTime() + "_"
					+ Math.random().toString().substr(2);
			var sel = null, range = null;

			if (document.selection && document.selection.createRange) {
				// Clone the TextRange and collapse
				range = document.selection.createRange().duplicate();
				sel = range.text;
				range.collapse(false);

				// Create the marker element containing a single invisible
				// character
				// by creating literal HTML and insert it
				range.pasteHTML('<span id="' + markerId
						+ '" style="position: relative;">'
						+ markerTextCharEntity + '</span>');
				markerEl = document.getElementById(markerId);
			} else if (window.getSelection
					&& (sel = window.getSelection()) != "") {
				if (sel.getRangeAt) {
					range = sel.getRangeAt(0).cloneRange();
				} else {
					// Older WebKit doesn't have getRangeAt
					range.setStart(sel.anchorNode, sel.anchorOffset);
					range.setEnd(sel.focusNode, sel.focusOffset);

					// Handle the case when the selection was selected backwards
					// (from the end to the start in the
					// document)
					if (range.collapsed !== sel.isCollapsed) {
						range.setStart(sel.focusNode, sel.focusOffset);
						range.setEnd(sel.anchorNode, sel.anchorOffset);
					}
				}
				range.collapse(false);
				// Create the marker element containing a single invisible
				// character
				// using DOM methods and insert it
				markerEl = document.createElement("span");
				markerEl.id = markerId;
				markerEl.appendChild(document.createTextNode(markerTextChar));
				range.insertNode(markerEl);
			}

			if (markerEl) {
				Kolich.commonAncestor = range.commonAncestorContainer;
				if (!Kolich.commonAncestor)
					 Kolich.commonAncestor=range.parentElement(); 
				groupAncestor();
				// Find markerEl position
				// http://www.quirksmode.org/js/findpos.html
				var obj = markerEl;
				var left = -obj.scrollLeft, top = -obj.scrollTop;
				do {
					left = left+ obj.offsetLeft;
					top = top+ obj.offsetTop;
				} while (obj = obj.offsetParent);

				Kolich.Selector.text = sel;
				Kolich.Selector.x = left;
				Kolich.Selector.y = top;

				markerEl.parentNode.removeChild(markerEl);
			} else
				Kolich.Selector.text = "";
		};
	})();

	function groupAncestor() {
		if (!Kolich.commonAncestor)
			return;

		var tag = Kolich.commonAncestor.tagName.toLowerCase();
		if (Kolich.commonAncestor.id == "content"
				|| Kolich.commonAncestor == document.body || tag == "div"
				|| tag == "p" /* || tag=="li" */)
			return;
		Kolich.commonAncestor = Kolich.commonAncestor.parentNode;
		groupAncestor();
	}

})();