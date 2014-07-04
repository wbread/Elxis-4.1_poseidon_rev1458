/*
Copyright (c) 2008-2012, Elxis Team - Ioannis Sannos. All rights reserved.
http://www.elxis.org
*/

( function(){
	var showElxisPlugins = function(editor){
		var elng = editor.lang.elxisplugin;
		var plugfn = 0;
		var inputtextid = '';
		return {
			title: elng.importplugin,
			minWidth: 400,
			minHeight: 240,
			onOk:function(){
				var val = this.getContentElement('epluginsert','elxplugcode').getInputElement().getValue();
				if ((val == '') || (val.indexOf('\#elink\:') != -1)) {
					this.getParentEditor().insertHtml(val);
				} else {
					var plughtml = '<code>'+val+'</code>';
					this.getParentEditor().insertHtml(plughtml);					
				}
			},
			onLoad:function() {
				var plugcodeimport = CKEDITOR.tools.addFunction(
					function(plugcode) {
 						if (document.getElementById(inputtextid)) {
 							document.getElementById(inputtextid).value = plugcode;
						 }
					}
				);
				plugfn = plugcodeimport;
			},
			onShow:function(){this.getContentElement('epluginsert','elxplugcode').getInputElement().setValue('')},
			contents: [{
				id: 'epluginsert',
				label: elng.insertplugin,
				elements:[
					{
						type: 'text',
						id: 'elxplugcode',
						label: '<strong>'+elng.code+'</strong>',
      					labelLayout: 'vertical',
						focus: function(){ this.getElement().focus(); },
                        validate: CKEDITOR.dialog.validate.notEmpty(elng.plugnoempty),
                        setup: function(element){
							this.setValue('');
						}
					},
					{
                        type:'html',
						id:'elxplugminihelp',
                        html:'<div style="margin:10px 0;">'+elng.plugmanually+'<br /><br />'+elng.plugtryguide+'</div>'
					},
					{
						type:'button',
						id:'browseplugins',
						hidden:false,
						style:'display:inline-block;margin-top:10px;',
						label:elng.guidedinput,
						onClick:function() {
							var dialog = CKEDITOR.dialog.getCurrent();
							inputtextid = dialog.getContentElement('epluginsert','elxplugcode').getInputElement().getAttribute('id');
							var elximportURL = elxplugImporterURL(CKEDITOR.plugins.get('elxisplugin').path)+'?fn='+plugfn;
							var plugWidth = 880;
							var plugHeight = 400;
    						var centerWidth = (window.screen.width - plugWidth) / 2;
							var centerHeight = (window.screen.height - plugHeight) / 2;
							plugWindow = window.open(elximportURL, 'pluginhelper', 'menubar=no, toolbar=no, directories=no, status=no, location=no, scrollbars=yes, resizable=yes, width='+plugWidth+', height='+plugHeight+', left='+centerWidth+', top='+centerHeight);
							plugWindow.focus();
						}
					}
				]
			}, {
        		id:'eplughelp',
        		label:elng.help,
        		elements:[
					{
                        type:'html',
						id:'elxplughelp',
						html:'<p style="margin:0 0 10px 0; padding:0; text-align:center;"><img src="'+CKEDITOR.plugins.get('elxisplugin').path+'images/elxis_logo.png" alt="elxis" border="0" /></p>'
						+'<p style="margin:0 0 30px 0; padding:0; font-size:12px; line-height:20px;">'+elng.genplugsyntax+'<br />'
						+'<span style="color:#FF0000;">{pluginname <span style="color:blue;">optional_attributes</span>}<span style="color:blue;">optional_code</span>{/pluginname}</span><br />'
						+elng.extactdepplug+'</p>'
                        +'<div style="text-align:center; color:#555;"><span style="font-weight:bold; color:#555;">ElxisPlugin</span> plugin for CKEditor created by <a href="http://www.isopensource.com/" title="Is Open Source" target="_blank" style="cursor:pointer !important; color:blue !important; text-decoration:underline !important;">Ioannis Sannos</a><br />(c)2012 Is Open Source - All rights reserved</div>'
					},
				]
    		}]
		}
	};

	CKEDITOR.dialog.add('elxisplugin', function(editor) {
		return showElxisPlugins(editor);
	});
		
})();

/* GET IMPORTER URL */
function elxplugImporterURL(plugurl) {
	if (document.getElementById('acontentbase')) {
		return document.getElementById('acontentbase').innerHTML+'plugin/';
	}
	var loc_url = document.location.href;
	var splitted = loc_url.split("\/content\/");
	var firstpart = splitted[0];
	if (firstpart.indexOf('index.php') > -1) {
		var helperurl = firstpart.replace('index.php', 'inner.php')+'/content/plugin/';
	} else {
		var elxis_root = plugurl.replace('includes/js/ckeditor/plugins/elxisplugin/', '');
		var adir = firstpart.replace(elxis_root, '');
		if (adir.indexOf('\/') > -1) {
			var xtemp1 = adir.replace('\/', '\/inner.php\/');
			var helperurl = elxis_root+''+xtemp1+'/content/plugin/';
		} else {
			var helperurl = elxis_root+''+adir+'/inner.php/content/plugin/';
		}
	}
	return helperurl;
}
