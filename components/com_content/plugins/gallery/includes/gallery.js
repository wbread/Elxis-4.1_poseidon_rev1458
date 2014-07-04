/*
Elxis CMS - plugin Gallery
Created by Stavros Stratakis / Ioannis Sannos
Copyright (c) 2006-2012 elxis.org
http://www.elxis.org
*/

/* LIST FOLDER IMAGES */
function egalFolderImages(plugid, fn) {
	var selObj = document.getElementById('egalleryctg');
	var fpath = selObj.options[selObj.selectedIndex].value;
	if (fpath == '') {
		document.getElementById('egalimages').innerHTML = '';
		return;
	}
	var loadingtext = document.getElementById('lng_wait').innerHTML;
	var edata = { 'id':plugid, 'fn':fn, 'fpath':fpath, 'task':'handler', 'act':'list' };
	var eurl = document.getElementById('plugbase').innerHTML;
	var successfunc = function(xreply) {
		document.getElementById('egalimages').innerHTML = xreply;
	}
	elxAjax('POST', eurl, edata, 'egalimages', loadingtext, successfunc, null);
}

/* LINK TO A FOLDER */
function egaltoFolder() {
	var selObj = document.getElementById('egalleryctg');
 	var fpath = selObj.options[selObj.selectedIndex].value;
	if (fpath != '') {
		var relp = document.getElementById('relpath').innerHTML;
		var code = '{gallery}'+relp+fpath+'{/gallery}';
		addPluginCode(code);
	} else {
		return;
	}
}

/* SUBMIT UPLOAD GALLERY FORM */
function plugGallerySubmit() {
	var selObj = document.getElementById('galfolder');
 	var folder = selObj.options[selObj.selectedIndex].value;
	var newfolder = document.getElementById('galnewfolder').value;
	if (folder == '') {
		if (newfolder == '') { alert('Select a folder to upload images!'); return false; }
	}
	if (newfolder != '') {
		var reg = /^([a-zA-Z0-9_-]+)$/;
		if (!reg.test(newfolder)) { alert('Invalid folder name!'); return false; }
	}
	document.pluggalform.submit();
	return true;
}

/* UPLOAD FORM VALIDATOR */
function elxformvalpluggalform() {
	plugGallerySubmit();
	return false;
}
