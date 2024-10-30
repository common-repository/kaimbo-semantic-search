CKEDITOR.disableAutoInline = true;
editor = null;

// if we leave, and we have modification, the editor window is kept visible.
// this way we "force" pressing save or cancel.
function handleEditorLeave(event) {
	var editor = event.editor;
	if (editor.checkDirty()) {
		//alert('Please click Save or Cancel');
		//	 correctTheSpelling();
		var el=jQuery('.ti-commonAncestor')[0];
		el.blur();
		el.setTimeout(el.focus(),100);
	}
	else {
		cancelTheEditor(editor);		
	}
}

// The "instanceCreated" event is fired for every editor instance created.
CKEDITOR.on('instanceCreated', function(event) {
	var editor = event.editor;
	editor.on('blur', handleEditorLeave);
});

CKEDITOR.on('instanceReady', function(event) {	
	var editor = event.editor;
	editor.focus();
});

CKEDITOR.config.extraPlugins = 'cancelbtn,savebtn'; // no space !
