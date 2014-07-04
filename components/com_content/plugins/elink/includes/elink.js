/*
Elxis CMS - plugin eLink
Created by Ioannis Sannos
Copyright (c) 2006-2013 elxis.org
http://www.elxis.org
*/

/* ADD A LINK TO A CATEGORY */
function elinkToCategory() {
	var selObj = document.getElementById('elinkctg');
	var seolink = selObj.options[selObj.selectedIndex].value;
	if (seolink == '') { return false; }
	var seltext = selObj.options[selObj.selectedIndex].text;
	seltext = seltext.replace(new RegExp("^[\\s]+", "g"), '');
	seltext = seltext.replace(new RegExp("^[\-]+", "g"), '');
	seltext = seltext.replace(new RegExp("^[\\s]+", "g"), '');
	var pcode = '<a href="#elink:content:'+seolink+'">'+seltext+'</a>';
	addPluginCode(pcode);
}

/* BROWSE CATEGORY ARTICLES */
function elinkBrowseCategory(plugid, fn) {
	var selObj = document.getElementById('elinkctg');
	var ctg_str = selObj.options[selObj.selectedIndex].id;
	var catid = ctg_str.replace(/ctg_/g, '');
	catid = parseInt(catid, 10);
	if (catid < 0) { return false; }
	var loadingtext = document.getElementById('lng_wait').innerHTML;
	var edata = {'id':plugid, 'fn':fn, 'catid':catid, 'task':'handler'};
	var eurl = document.getElementById('plugbase').innerHTML;
	var successfunc = function(xreply) {
		document.getElementById('elinkarticles').innerHTML = xreply;
	}
	elxAjax('POST', eurl, edata, 'elinkarticles', loadingtext, successfunc, null);
}
