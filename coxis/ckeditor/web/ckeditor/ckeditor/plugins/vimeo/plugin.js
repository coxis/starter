﻿(function(){var vimeoCmd={exec:function(editor){editor.openDialog('vimeo');return}};
CKEDITOR.plugins.add('vimeo',{lang:['en','uk'],requires:['dialog'],
	init:function(editor){var commandName='vimeo';editor.addCommand(commandName,vimeoCmd);
				editor.ui.addButton('vimeo',{label:editor.lang.vimeo.button,command:commandName,icon:this.path+"images/vimeo.png"});
				CKEDITOR.dialog.add(commandName,CKEDITOR.getUrl(this.path+'dialogs/vimeo.js'))}})})();
