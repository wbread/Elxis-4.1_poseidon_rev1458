/*
Elxis CMS - plugin Contact
Created by Ioannis Sannos
Copyright (c) 2006-2012 elxis.org
http://www.elxis.org
*/

/* ADD CONTACT CODE */
function addContactCode() {
	var cmail = document.getElementById('contact_email').value;
	if ((cmail == '') || (cmail.indexOf('\@') == -1)) { return false; }
	var pcode = '{contact}'+cmail+'{/contact}';
	addPluginCode(pcode);
}
