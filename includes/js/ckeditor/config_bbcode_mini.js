/*****************************************
Copyright (c) 2003-2011, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license

Elxis CMS - Copyright 2006-2012 elxis.org. All rights reserved.
CKEditor configuration - BBcode - Basic
******************************************/


CKEDITOR.editorConfig = function( config )
{
	//generic
	config.entities_greek = false;
	config.entities_latin = false;
	config.width = 450;

	//bbcode specific
	config.extraPlugins = 'bbcode';
	config.removePlugins = 'smiley,bidi,button,dialogadvtab,div,filebrowser,flash,format,forms,horizontalrule,iframe,indent,justify,liststyle,pagebreak,showborders,stylescombo,table,tabletools,templates';
	config.disableObjectResizing = true;
	config.fontSize_sizes = '30/30%;50/50%;100/100%;120/120%;150/150%;200/200%;300/300%';
	config.toolbar = [
		['Undo','Redo'],
		['Link', 'Unlink'],
		['Bold', 'Italic','Underline'],
		['NumberedList','BulletedList','-','Blockquote'],
		['Maximize']
	];
	config.smiley_images = [
		'regular_smile.gif','sad_smile.gif','wink_smile.gif','teeth_smile.gif','tounge_smile.gif','embaressed_smile.gif',
		'omg_smile.gif','whatchutalkingabout_smile.gif','angel_smile.gif','shades_smile.gif','cry_smile.gif','kiss.gif'
	];
	config.smiley_descriptions = [
		'smiley', 'sad', 'wink', 'laugh', 'cheeky', 'blush', 'surprise',
		'indecision', 'angel', 'cool', 'crying', 'kiss'
	];
				
};
