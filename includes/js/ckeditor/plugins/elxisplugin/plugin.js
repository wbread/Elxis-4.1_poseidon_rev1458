/*
Copyright (c) 2008-2012, Elxis Team - Ioannis Sannos. All rights reserved.
http://www.elxis.org
*/

(function(){
	CKEDITOR.plugins.add('elxisplugin', {
		lang:['en','el'],
		requires:['dialog'],
		init:function(editor) {
			var commandName = 'elxisplugin';
			editor.addCommand(commandName,new CKEDITOR.dialogCommand(commandName));
			editor.ui.addButton('ElxisPlugin',{
				label:editor.lang.elxisplugin.button,
				command:commandName
			});
			editor.addCss(
				'code { display:block; color:#555; visibility:visible; margin:2px 0; padding:2px 4px 2px 20px; line-height:16px; '+
				'background:#fff url('+CKEDITOR.plugins.get('elxisplugin').path+'images/elxis.png) 2px 2px no-repeat; '+
				'border:1px dotted #145167; } code:hover {color:#0C2A53; }'
             );
			CKEDITOR.dialog.add(commandName,CKEDITOR.getUrl(this.path+'dialogs/elxisplugin.js'))
		}
	})
})();
