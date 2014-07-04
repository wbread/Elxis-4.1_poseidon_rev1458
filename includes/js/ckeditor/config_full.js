/*****************************************
Copyright (c) 2003-2011, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license

Elxis CMS - Copyright 2006-2013 elxis.org. All rights reserved.
CKEditor configuration - HTML - Full
******************************************/

CKEDITOR.editorConfig = function( config )
{
	//generic
	config.entities_greek = false;
	config.entities_latin = false;
	config.processNumerical = true;
	config.width = 900;
	//plugins
	config.extraPlugins = 'youtube,elxisplugin';//stylesheetparser: to parse all styles in CSS
	config.contentsCss = '/templates/system/css/standard.css';
	config.docType = '<!DOCTYPE html>';
	//toolbar
	config.toolbar = [
	{ name: 'document',    items : [ 'Source','-','DocProps','Preview','Print','-','Templates' ] },
	{ name: 'clipboard',   items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
	{ name: 'editing',     items : [ 'Find','Replace','-','SelectAll','-','SpellChecker', 'Scayt' ] },
	{ name: 'forms',       items : [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField' ] },
	'/',
	{ name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ] },
	{ name: 'paragraph',   items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','CreateDiv','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','BidiLtr','BidiRtl' ] },
	{ name: 'links',       items : [ 'Link','Unlink','Anchor' ] },
	{ name: 'insert',      items : [ 'Image','Youtube','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak' ] },
	'/',
	{ name: 'styles',      items : [ 'Styles','Format','Font','FontSize' ] },
	{ name: 'colors',      items : [ 'TextColor','BGColor' ] },
	{ name: 'tools',       items : [ 'Maximize', 'ShowBlocks','-','ElxisPlugin','-','About' ] }
	];

};
