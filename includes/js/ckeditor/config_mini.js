/*****************************************
Copyright (c) 2003-2011, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license

Elxis CMS - Copyright 2006-2012 elxis.org. All rights reserved.
CKEditor configuration - HTML - Normal
******************************************/


CKEDITOR.editorConfig = function( config )
{
	//generic
	config.entities_greek = false;
	config.entities_latin = false;
	config.processNumerical = true;
	config.width = 580;
	//plugins
	config.removePlugins = 'filebrowser,flash,format,forms,iframe';
	//toolbar
	config.toolbar = [
	{ name: 'document',    items : [ 'DocProps','Preview','Print' ] },
	{ name: 'clipboard',   items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
	{ name: 'editing',     items : [ 'Find','Replace','-','SelectAll','-','SpellChecker', 'Scayt' ] },
	
	'/',
	{ name: 'links',       items : [ 'Link','Unlink','Anchor' ] },
	{ name: 'paragraph',   items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','CreateDiv','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','BidiLtr','BidiRtl' ] },
	'/',
	
	{ name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ] },
	{ name: 'insert',      items : [ 'Image','Table','HorizontalRule','Smiley','SpecialChar','PageBreak' ] },
	
	{ name: 'tools',       items : [ 'Maximize', 'ShowBlocks' ] },
	'/',
	{ name: 'styles',      items : [ 'Styles','Format','Font','FontSize' ] },
	{ name: 'colors',      items : [ 'TextColor','BGColor' ] }
	
	];

};
