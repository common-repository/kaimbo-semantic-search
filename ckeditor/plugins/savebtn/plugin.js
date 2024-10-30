(function() {
	var save = {
		exec : function(editor) {
			if (editor.checkDirty()) {				
				TISearchPopups.correctTheSpelling();
			}
			else {
				TISearchPopups.cancelTheEditor(editor);
			}			
		}
	};

	var bs = 'savebtn';
	CKEDITOR.plugins.add(bs, {
		init : function(editor) {
			editor.addCommand(bs, save);
			editor.ui.addButton("savebtn", {
				label : 'Save',
				icon : this.path + "save.png",
				command : bs
			});
		}
	});
})();
