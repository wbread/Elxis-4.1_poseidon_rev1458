/*
Elxis CMS - plugin Automatic Links
Created by Ioannis Sannos
Copyright (c) 2006-2012 elxis.org
http://www.elxis.org
*/

/* ADD AUTOLINKS CODE */
function addAutolinkCode() {
	var kwords = document.getElementById('autolink_keys').value;
	var pcode = '{autolinks}'+kwords+'{/autolinks}';
	addPluginCode(pcode);
}
