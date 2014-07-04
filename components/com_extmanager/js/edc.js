/*
Component Extensions Manager 
JS for Elxis Downloads Center (EDC)
Author: Ioannis Sannos (datahell) 
Date: 2012-07-12 19:34:00 GMT
http://www.elxis.org
*/


/* DISPLAY A PRELOADER */
function edcLoading(elemid, specialmsg) {
	if (!document.getElementById(elemid)) { return false; }
	var sitebase = document.getElementById('sitebase').innerHTML;
	var contents = '<div class="ui_loading"><img src="'+sitebase+'/components/com_extmanager/css/progress_bar.gif" alt="loading" /><br />';
	if (specialmsg != '') { contents += specialmsg+'<br />'; }
	contents += edcLang.PLEASE_WAIT+'</div>';
	document.getElementById(elemid).innerHTML = contents;
}

/* LOAD EDC FRONTPAGE */
function edcFrontpage() {
	document.getElementById('ui_edc_filters').innerHTML = '';
	edcMarkCategory(0);
	document.getElementById('edccatid').innerHTML = 0;
	edcLoading('ui_edc_main', edcLang.LOADING_EDC);
	rnd = Math.floor((Math.random()*100)+1);
	var edcauth = document.getElementById('edcauth').innerHTML;
	var edata = { 'task':'frontpage', 'edcauth':edcauth, 'rnd':rnd };
	var eurl = document.getElementById('extmanbase').innerHTML+'browse/req';
	var successfunc = function(xreply) {
		document.getElementById('ui_edc_main').innerHTML = xreply;
	}
	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		document.getElementById('ui_edc_main').innerHTML = '<div class="elx_error">Could not load EDC! '+errorThrown+'</div>';
	}
	elxAjax('POST', eurl, edata, null, null, successfunc, errorfunc);
}

/* CONNECT TO ELXIS DC */
function edcConnect() {
	if (document.getElementById('edcauth').innerHTML != '') {
		edcFrontpage();
		return;
	}
	edcLoading('ui_edc_auth', edcLang.CONNECTING_EDC);
	rnd = Math.floor((Math.random()*100)+1);
	var edata = { 'task':'auth', 'rnd':rnd };
	var eurl = document.getElementById('extmanbase').innerHTML+'browse/req';

	var successfunc = function(xreply) {
		var jsonObj = JSON.parse(xreply);
		if (parseInt(jsonObj.error, 10) > 0) {
			if (jsonObj.errormsg != '') {
				document.getElementById('ui_edc_auth').innerHTML = '<div class="elx_error">'+jsonObj.errormsg+'</div>';
			} else {
				document.getElementById('ui_edc_auth').innerHTML = '<div class="elx_error">Authorization to EDC failed!</div>';
			}
			return false;
		} else {
			document.getElementById('edcauth').innerHTML = jsonObj.edcauth;
			document.getElementById('ui_edc_auth').innerHTML = '';
			edcFrontpage();
		}
	}

	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		document.getElementById('ui_edc_auth').innerHTML = '<div class="elx_error">Connection to EDC failed! '+errorThrown+'</div>';
	}

	elxAjax('POST', eurl, edata, null, null, successfunc, errorfunc);
}


/* LOAD REMOTE CONTENT */
function edcLoad(catid, fid, page) {
	if ((typeof page == "undefined") || (page == undefined)) { page = 1; }
	page = parseInt(page, 10);
	if (page < 1) { page = 1; }
	catid = parseInt(catid, 10);
	fid = parseInt(fid, 10);
	var edccatid = document.getElementById('edccatid').innerHTML;
	edccatid = parseInt(edccatid, 10);

	edcLoading('ui_edc_main', '');

	if (catid != edccatid) {
		edcMarkCategory(catid);
		if (catid == 0) {
			document.getElementById('ui_edc_filters').innerHTML = '';
		} else {
			edcLoadFilters(catid);
		}
	}

	edcLoadCategory(catid, fid, page);
	document.getElementById('edccatid').innerHTML = catid;
}


/* MARK CURRENT CATEGORY IN MENU */
function edcMarkCategory(catid) {
	catid = parseInt(catid, 10);
	for (i=0; i<30; i++) {
		var elemid = 'edcctg'+i;
		if (!document.getElementById(elemid)) { continue; }
		if (i == catid) {
			document.getElementById(elemid).className = 'ui_bold';
		} else {
			document.getElementById(elemid).className = '';
		}
	}
}


/* LOAD EDC FILTERS FOR THE SELECTED CATEGORY */
function edcLoadFilters(catid) {
	rnd = Math.floor((Math.random()*100)+1);
	var edcauth = document.getElementById('edcauth').innerHTML;
	var edata = { 'task':'filters', 'catid':catid, 'edcauth':edcauth, 'fid':0, 'rnd':rnd };
	var eurl = document.getElementById('extmanbase').innerHTML+'browse/req';
	var successfunc = function(xreply) {
		document.getElementById('ui_edc_filters').innerHTML = xreply;
	}
	elxAjax('POST', eurl, edata, null, null, successfunc, null);
}


/* LOAD EDC SELECTED CATEGORY */
function edcLoadCategory(catid, fid, page) {
	if ((typeof page == "undefined") || (page == undefined)) { page = 1; }
	page = parseInt(page, 10);
	if (page < 1) { page = 1; }
	catid = parseInt(catid, 10);
	fid = parseInt(fid, 10);
	rnd = Math.floor((Math.random()*100)+1);
	var edcauth = document.getElementById('edcauth').innerHTML;
	var edata = { 'task':'category', 'catid':catid, 'fid':fid, 'page':page, 'edcauth':edcauth, 'rnd':rnd };
	var eurl = document.getElementById('extmanbase').innerHTML+'browse/req';
	var successfunc = function(xreply) {
		document.getElementById('ui_edc_main').innerHTML = xreply;
	}
	elxAjax('POST', eurl, edata, null, null, successfunc, null);
}


/* DOWNLOAD PACKAGE */
function edcDownload(pcode) {
	if (pcode == '') { alert('This file can not be downloaded!'); return false; }
	var edcurl = document.getElementById('edcurl').innerHTML;
	if (edcurl == '') { alert('EDC location is unknown!'); return false; }
	var elxisid = document.getElementById('elxisid').innerHTML;
	var edcauth = document.getElementById('edcauth').innerHTML;
	if (edcauth == '') { alert('You are not authorized to access EDC!'); return false; }
	rnd = Math.floor((Math.random()*100)+1);
	var frObj = document.getElementById('edcframe');
	frObj.src = edcurl+'?task=download&elxisid='+elxisid+'&edcauth='+edcauth+'&pcode='+pcode;
}


/* VIEW EXTENSION'S DETAILS */
function edcLoadExtension(id, catid, fid) {
	id = parseInt(id, 10);
	if (id < 1) { alert('Invalid extension!'); return false; }
	catid = parseInt(catid, 10);
	fid = parseInt(fid, 10);
	var edccatid = document.getElementById('edccatid').innerHTML;
	edccatid = parseInt(edccatid, 10);

	if ((catid > 0) && (catid != edccatid)) {
		document.getElementById('edccatid').innerHTML = catid;
		edcMarkCategory(catid);
		edcLoadFilters(catid);
	}

	edcLoading('ui_edc_main', '');

	rnd = Math.floor((Math.random()*100)+1);
	var edcauth = document.getElementById('edcauth').innerHTML;
	var edata = { 'task':'view', 'id':id, 'catid':catid, 'fid':fid, 'edcauth':edcauth, 'rnd':rnd };
	var eurl = document.getElementById('extmanbase').innerHTML+'browse/req';
	var successfunc = function(xreply) {
		document.getElementById('ui_edc_main').innerHTML = xreply;
	}
	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		document.getElementById('ui_edc_main').innerHTML = '<div class="elx_error">Connection to EDC failed! '+errorThrown+'</div>';
	}
	elxAjax('POST', eurl, edata, null, null, successfunc, null);
}


/* LOAD AUTHOR'S EXTENSIONS */
function edcAuthor(uid) {
	uid = parseInt(uid, 10);
	if (uid < 1) { alert('Invalid author!'); return false; }
	document.getElementById('ui_edc_filters').innerHTML = '';
	edcMarkCategory(0);
	document.getElementById('edccatid').innerHTML = 0;
	edcLoading('ui_edc_main', '');

	rnd = Math.floor((Math.random()*100)+1);
	var edcauth = document.getElementById('edcauth').innerHTML;
	var edata = { 'task':'author', 'uid':uid, 'edcauth':edcauth, 'rnd':rnd };
	var eurl = document.getElementById('extmanbase').innerHTML+'browse/req';
	var successfunc = function(xreply) {
		document.getElementById('ui_edc_main').innerHTML = xreply;
	}
	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		document.getElementById('ui_edc_main').innerHTML = '<div class="elx_error">Could not load author extensions! '+errorThrown+'</div>';
	}
	elxAjax('POST', eurl, edata, null, null, successfunc, errorfunc);
}


/* PROMPT INSTALL/UPDATE PACKAGE */
function edcPrompt(action, pcode, exttitle, extversion) {
	if (pcode == '') { alert('Installation package is missing!'); return false; }
	document.getElementById('ui_edc_response').innerHTML = '';
	document.getElementById('ui_edc_response').style.display = 'none';

	if (action == 'install') {
		var lng_aboutto = edcLang.ABOUT_TO_INSTALL;
	} else if (action == 'update') {
		var lng_aboutto = edcLang.ABOUT_TO_UPDATE_TO;
	} else {
		alert('Invalid action!');
		return false;
	}

	lng_aboutto = lng_aboutto.replace(/X1/gi, '<strong>'+exttitle+'</strong>');
	lng_aboutto = lng_aboutto.replace(/X2/gi, '<strong>'+extversion+'</strong>');
	var prompttxt = '<p>'+lng_aboutto+'</p>';
	if (action == 'install') {
		prompttxt += '<div class="ui_install_buttons"><a href="javascript:void(null);" onclick="edcInstall(\''+pcode+'\', \'install\');">'+edcLang.INSTALL+'</a> ';
	} else {
		prompttxt += '<div class="ui_install_buttons"><a href="javascript:void(null);" onclick="edcInstall(\''+pcode+'\', \'update\');">'+edcLang.UPDATE+'</a> ';
	}
	prompttxt += '<a href="javascript:void(null);" onclick="edcClosebox();">'+edcLang.CANCEL+'</a></div>';
	document.getElementById('ui_lightbox_message').innerHTML = prompttxt;
	document.getElementById('edcfadebox').style.display = 'block';
	document.getElementById('edclightbox').style.display = 'block';
}


/* CLOSE PROMPT BOX */
function edcClosebox() {
	document.getElementById('edclightbox').style.display = 'none';
	document.getElementById('edcfadebox').style.display = 'none';
}


/* INSTALL/UPDATE PACKAGE */
function edcInstall(pcode, edctask) {
	if (pcode == '') { alert('Installation package is missing!'); return false; }
	document.getElementById('ui_edc_response').innerHTML = '';
	document.getElementById('ui_edc_response').style.display = 'block';
	edcLoading('ui_edc_response', '');
	rnd = Math.floor((Math.random()*100)+1);
	var edcauth = document.getElementById('edcauth').innerHTML;
	var elxisid = document.getElementById('elxisid').innerHTML;
	var edata = { 'task':edctask, 'edcauth':edcauth, 'pcode':pcode, 'rnd':rnd };
	var eurl = document.getElementById('extmanbase').innerHTML+'install/edc';
	var successfunc = function(xreply) {
		var jsonObj = JSON.parse(xreply);
		if (parseInt(jsonObj.error, 10) == 1) {
			if (jsonObj.errormsg != '') {
				document.getElementById('ui_edc_response').innerHTML = '<div class="elx_error">'+jsonObj.errormsg+'</div>';
			} else {
				document.getElementById('ui_edc_response').innerHTML = '<div class="elx_error">Action failed!</div>';
			}
			return false;
		} else {
			var lng_insuccess = edcLang.EXT_INST_SUCCESS;
			var responsetxt = lng_insuccess.replace(/X1/gi, jsonObj.exttype+' <strong>'+jsonObj.extension+'</strong>');
			responsetxt = responsetxt.replace(/X2/gi, '<strong>'+jsonObj.version+'</strong>');
			responsetxt += '<br />';
			var len = jsonObj.warnings.length;
			for (var i = 0; i < len; i++) {
				if (i == 0) {
					responsetxt += '<span class="ui_warntitle">'+edcLang.SYSTEM_WARNINGS+'</span><br />';
				}
				var n = i + 1;
				responsetxt += '<strong>'+n+'.</strong> '+jsonObj.warnings[i]+'<br />';
			}
			document.getElementById('ui_edc_response').innerHTML = responsetxt;
		}
	}
	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		document.getElementById('ui_edc_response').innerHTML = '<div class="elx_error">Connection to EDC failed! '+errorThrown+'</div>';
	}
	elxAjax('POST', eurl, edata, null, null, successfunc, errorfunc);
}


/* RATE EXTENSION */
function edcRate(id, rating) {
	id = parseInt(id, 10);
	if (id < 1) { return false; }
	rating = parseInt(rating, 10);
	if (rating < 1) { return false; }
	if (rating > 5) { return false; }
	rnd = Math.floor((Math.random()*100)+1);
	var edcauth = document.getElementById('edcauth').innerHTML;
	var edata = { 'task':'rate', 'id':id, 'rating':rating, 'edcauth':edcauth, 'rnd':rnd };
	var eurl = document.getElementById('extmanbase').innerHTML+'browse/req';
	var successfunc = function(xreply) {
		var jsonObj = JSON.parse(xreply);
		if (parseInt(jsonObj.error, 10) > 0) {
			if (jsonObj.msg != '') {
				alert(jsonObj.msg);
			} else {
				alert('Rating failed');
			}
		} else {
			var edccatid = document.getElementById('edccatid').innerHTML;
			edccatid = parseInt(edccatid, 10);
			edcLoadExtension(id, edccatid, 0);
		}
	}
	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) { alert(errorThrown); }
	elxAjax('POST', eurl, edata, null, null, successfunc, errorfunc);
}


/* REPORT EXTENSION */
function edcReport(id, rcode) {
	id = parseInt(id, 10);
	if (id < 1) { return false; }
	rcode = parseInt(rcode, 10);
	if (rcode < 1) { return false; }
	if (confirm(edcLang.AREYOUSURE+" \n"+edcLang.ACTION_WAIT)) {
		rnd = Math.floor((Math.random()*100)+1);
		var edcauth = document.getElementById('edcauth').innerHTML;
		var edata = { 'task':'report', 'id':id, 'rcode':rcode, 'edcauth':edcauth, 'rnd':rnd };
		var eurl = document.getElementById('extmanbase').innerHTML+'browse/req';
		var successfunc = function(xreply) {
			var jsonObj = JSON.parse(xreply);
			if (parseInt(jsonObj.error, 10) > 0) {
				if (jsonObj.msg != '') {
					alert(jsonObj.msg);
				} else {
					alert('Report failed');
				}
			} else {
				if (jsonObj.msg != '') {
					alert(jsonObj.msg);
				} else {
					alert('The extension has been reported to Elxis Team');
				}
			}
		}
		var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) { alert(errorThrown); }
		elxAjax('POST', eurl, edata, null, null, successfunc, errorfunc);
	}
}


/* REGISTER SITE AT ELXIS.ORG */
function edcRegister() {
	var conftext = edcLang.NAMEMAIL_ELXIS+" \n"+edcLang.INFO_STAY_PRIVE+" "+edcLang.CONTINUE;
	if (confirm(conftext)) {
		rnd = Math.floor((Math.random()*100)+1);
		var edcauth = document.getElementById('edcauth').innerHTML;
		var edata = { 'task':'register', 'edcauth':edcauth, 'rnd':rnd };
		var eurl = document.getElementById('extmanbase').innerHTML+'browse/req';
		var successfunc = function(xreply) {
			var jsonObj = JSON.parse(xreply);
			if (parseInt(jsonObj.error, 10) > 0) {
				if (jsonObj.msg != '') {
					alert(jsonObj.msg);
				} else {
					alert('Registration failed');
				}
			} else {
				document.location.reload(true);
			}
		}
		var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) { alert(errorThrown); }
		elxAjax('POST', eurl, edata, null, null, successfunc, errorfunc);
	}
}
