(function() {
	// Section 1 : Code to execute when the toolbar button is pressed
	var a = {
		exec : function(editor) {
			TISearchPopups.cancelTheEditor(editor);		
		}
	};

	// Section 2 : Create the button and add the functionality to it
	var b = 'cancelbtn';
	CKEDITOR.plugins.add(b, {
		init : function(editor) {
			editor.addCommand(b, a);
			editor.ui.addButton(b, {
				label : 'Cancel',
				icon : this.path + "cancel.png",
				command : b
			});
		}
	});	
})();
