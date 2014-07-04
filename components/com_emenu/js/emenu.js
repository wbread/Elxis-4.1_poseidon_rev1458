/* COMPONENT eMenu JS by Ioannis Sannos (datahell) */

/* SELECT COMPONENT */
function emenu_pickcomponent(cmp) {
	if (typeof cmp == 'undefined') {
		var obj = document.getElementById('pickcomponent');	
    	var component = obj.options[obj.selectedIndex].value;
    } else {
    	component = cmp;
   	}
   	if (component == '') { return; }
	var edata = { 'component': component };
	var eurl = document.getElementById('epremenuurl').value+'mitems/generator.html';
	var loadtext = document.getElementById('eprloadingtxt').value;
	elxAjax('POST', eurl, edata, 'emenu_generator', loadtext, null, null);
}

/* SET MENU ITEM INFO */
function emenu_setlink(xtitle, xlink, secure, alevel) {
	secure = parseInt(secure);
	document.getElementById('eprtitle').value = xtitle;
	document.getElementById('eprlink').value = xlink;
	if (secure == 1) {
		document.getElementById('eprsecure_1').checked = 'checked';
		document.getElementById('eprsecure_2').checked = '';
	} else {
		document.getElementById('eprsecure_1').checked = '';
		document.getElementById('eprsecure_2').checked = 'checked';
	}
	if (typeof alevel != 'undefined') {
		alevel = parseInt(alevel, 10);
		var selObj = document.getElementById('epralevel');
		for (var i=0; i < selObj.options.length; i++) {
			if (selObj.options[i].value == alevel) {
				selObj.selectedIndex = i;
			}
		}
	}
}

/* SET MENU ITEM INFO FROM POPUP WINDOW */
function emenu_osetlink(xtitle, xlink, secure, alevel) {
	secure = parseInt(secure);
	window.opener.document.getElementById('eprtitle').value = xtitle;
	window.opener.document.getElementById('eprlink').value = xlink;
	if (secure == 1) {
		window.opener.document.getElementById('eprsecure_1').checked = 'checked';
		window.opener.document.getElementById('eprsecure_2').checked = '';
	} else {
		window.opener.document.getElementById('eprsecure_1').checked = '';
		window.opener.document.getElementById('eprsecure_2').checked = 'checked';
	}
	if (typeof alevel != 'undefined') {
		alevel = parseInt(alevel, 10);
		var selObj = window.opener.document.getElementById('epralevel');
		for (var i=0; i < selObj.options.length; i++) {
			if (selObj.options[i].value == alevel) {
				selObj.selectedIndex = i;
			}
		}
	}
	window.close();
}
