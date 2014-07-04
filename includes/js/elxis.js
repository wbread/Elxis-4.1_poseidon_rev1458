/** 
Elxis CMS generic javascript
Created by Ioannis Sannos / Elxis Team
http://www.elxis.org
*/

/* TOGGLE XML PARAMETERS GROUP VISIBILITY */
function elxToggleParamsGroup(groupid) {
	groupid = parseInt(groupid);
	if (groupid < 1) { return false; }
	var tblid = 'params_group_'+groupid;
	var togid = 'params_toggler_'+groupid;
	if (document.getElementById) {
		var tblObj = document.getElementById(tblid);
		var togObj = document.getElementById(togid);
	} else if (document.all) {
		var tblObj = document.all[tblid];
		var togObj = document.all[togid];
	} else if (document.layers) {
		var tblObj = document.layers[tblid];
		var togObj = document.layers[togid];
	} else {
		return false;
	}

	if (tblObj.style.display == 'none') {
		elxShow(tblObj);
		togObj.className = 'elx_params_group';
	} else {
		elxHide(tblObj);
		togObj.className = 'elx_params_group_collapsed';
	}
}


/* CHECK IF VALUE IS IN ARRAY */
function elxInArray(val, arr) {
	if (arr instanceof Array) {
		for (var i in arr) {
			if (val == arr[i]) { return true; }
		}
	}
	return false;
}


/* SHOW ELEMENT OBJECT */
function elxShow(obj) {
	if (!obj) { return; }
	var tag = obj.tagName;
	tag = tag.toLowerCase();
	var blockElements = new Array('address', 'blockquote', 'div', 'dl', 'fieldset', 'form', 
	'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'hr', 'noscript', 'ol', 'p', 'pre', 'table', 'ul');
	if (tag == 'table') {
		var ieversion = elxIEVersion();
		if (ieversion > 0) {
			if (ieversion >= 8) {
				obj.style.display = 'table';
			} else {
				obj.style.display = 'block';
			}
		} else {
			obj.style.display = 'table';
		}
		obj.style.visibility = 'visible';
	} else if (elxInArray(tag, blockElements) == true) {
		obj.style.display = 'block';
		obj.style.visibility = 'visible';
	} else {
		obj.style.display = 'inline';
		obj.style.visibility = 'visible';
	}
}


/* HIDE ELEMENT OBJECT */
function elxHide(obj) {
	if (!obj) { return; }
	obj.style.display = 'none';
	obj.style.visibility = 'hidden';
}


/* SHOW PARAMS GROUP (TRIGGERD BY FORM ELEMENT) */
function elxShowParams(obj, optionsstr, typ) {
	elxShowHideParams(obj, optionsstr, typ, 1);
}


/* HIDE PARAMS GROUP (TRIGGERD BY FORM ELEMENT) */
function elxHideParams(obj, optionsstr, typ) {
	elxShowHideParams(obj, optionsstr, typ, 0);
}


/* SHOW OR HIDE PARAMS GROUP (TRIGGERD BY FORM ELEMENT) */
function elxShowHideParams(obj, optionsstr, typ, show) {
	if (optionsstr == '') { return false; }
	var selIndex = 0;
	typ = parseInt(typ);
	if (typ == 1) { //select
		selIndex = obj.selectedIndex;
	} else if (typ == 2) { //radio
		var objname = obj.name;
		objname = objname.replace('[', '');
		objname = objname.replace(']', '');
		selIndex = obj.id.replace(objname, '');
		selIndex = parseInt(selIndex);
	} else {
		return false;
	}

	var par_aids = optionsstr.split(';');
	if (par_aids instanceof Array) {
		for (var i in par_aids) {
			var optstr = par_aids[i];
			if (optstr != '') {
				var par_bids = optstr.split(':');
				var curIndex = parseInt(par_bids[0]);
				if (curIndex == selIndex) {
					var trigstr = par_bids[1];
					if (trigstr != '') {
						var par_cids = trigstr.split(',');
						if (par_cids instanceof Array) {
							for (var x in par_cids) {
								var par_cid = 'params_group_'+parseInt(par_cids[x]);
								var togid = 'params_toggler_'+parseInt(par_cids[x]);
								if (document.getElementById(par_cid) == null) {
									continue;
								} else {
									var pObj = document.getElementById(par_cid);
									if (show == 0) {
										elxHide(pObj);
										if (document.getElementById(togid)) {
											document.getElementById(togid).className = 'elx_params_group_collapsed';
										}
									} else {
										elxShow(pObj);
										if (document.getElementById(togid)) {
											document.getElementById(togid).className = 'elx_params_group';
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}
}


/* GET INTERNET EXPLORER VERSION */
function elxIEVersion() {
	if (/MSIE (\d+\.\d+);/.test(navigator.userAgent)) {
		var ieversion=new Number(RegExp.$1);
		if (ieversion>=5) {
			var version = parseFloat(ieversion);
			version.toFixed(2);
			return version;
		}
	}
	return 0;
}


/* RESIZE IFRAME TO FIT CONTENT'S WIDTH (SAME DOMAIN PAGES ONLY) */
function elxResizeIframe(frameid) {
    var myIFrame = document.getElementById(frameid);
    if (!myIFrame) { return false; }
    var h = 0;
	if (myIFrame.contentDocument && myIFrame.contentDocument.body.offsetHeight) {
		h = myIFrame.contentDocument.body.offsetHeight; 
	} else if (myIFrame.Document && myIFrame.Document.body.scrollHeight) {
		h = myIFrame.Document.body.scrollHeight;
	} else if (myIFrame.contentWindow.document && myIFrame.contentWindow.document.documentElement) {
		h = myIFrame.contentWindow.document.documentElement.offsetHeight;
	} else if (myIFrame.contentWindow.document && myIFrame.contentWindow.document.body) {
		h = myIFrame.contentWindow.document.body.offsetHeight;
	} else if (myIFrame.contentDocument.document && myIFrame.contentDocument.document.body) {
		h = myIFrame.contentDocument.document.body.offsetHeight;
	} else if (myIFrame.contentDocument.document && myIFrame.contentDocument.documentElement.body) {
		h = myIFrame.contentDocument.document.documentElement.offsetHeight;
	}

    h = parseInt(h);
    if (h > 0) {
    	h = parseInt(h * 1.065);
    	var getFFVersion = navigator.userAgent.substring(navigator.userAgent.indexOf("Firefox")).split("/")[1];
    	if (parseFloat(getFFVersion) >= 0.1) { h += 30; }
		myIFrame.style.height = h+'px';
		if (myIFrame.addEventListener) {
			myIFrame.addEventListener("load", elxReResizeIframe, false);
		} else if (myIFrame.attachEvent) {
			myIFrame.detachEvent("onload", elxReResizeIframe);
			myIFrame.attachEvent("onload", elxReResizeIframe);
		}
	}
}


/* RE-RESIZE IFRAME */
function elxReResizeIframe(loadevt) {
	var crossevt=(window.event)? event : loadevt;
	var iframeroot = (crossevt.currentTarget) ? crossevt.currentTarget : crossevt.srcElement;
	if (iframeroot) {
		elxResizeIframe(iframeroot.id);
	}
}

/* VALIDATE EMAIL ADDRESS */
function elxValidateEmail(str, allowempty) {
	if (str == '') {
		if (allowempty == true) { return true; } else { return false; }
	}
	var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
	if (str.search(emailPattern) == -1) { return false; }
    return true;
}

/* VALIDATE EMAIL INPUT FIELD */
function elxValidateEmailBox(fel, allowempty) {
	if (!document.getElementById(fel)) { return true; }
	var fstr = document.getElementById(fel).value;
	return elxValidateEmail(fstr, allowempty);
}

/* VALIDATE DATE */
function elxValidateDate(str, format, allowempty) {
	if (str == '') {
		if (allowempty == true) { return true; } else { return false; }
	}

	var m = 0; var d = 0; var y = 0; var h = 0; var i = 0; var s = 0; var daytime = false;
	if (format == 'Y-m-d') {
		if (str.search(/^\d{4}[\-]\d{1,2}[\-]\d{1,2}/g) != 0) { return false; }
		var parts = str.split('-'); if (parts.length != 3) { return false; }
		d = parseInt(parts[2], 10); m = parseInt(parts[1], 10); y = parseInt(parts[0], 10);
	} else if (format == 'Y/m/d') {
		if (str.search(/^\d{4}[\/]\d{1,2}[\/]\d{1,2}/g) != 0) { return false; }
		var parts = str.split('/'); if (parts.length != 3) { return false; }
		d = parseInt(parts[2], 10); m = parseInt(parts[1], 10); y = parseInt(parts[0], 10);
	} else if (format == 'd-m-Y') {
		if (str.search(/^\d{1,2}[\-]\d{1,2}[\-]\d{4}/g) != 0) { return false; }
		var parts = str.split('-'); if (parts.length != 3) { return false; }
		d = parseInt(parts[0], 10); m = parseInt(parts[1], 10); y = parseInt(parts[2], 10);
	} else if (format == 'd/m/Y') {
		if (str.search(/^\d{1,2}[\/]\d{1,2}[\/]\d{4}/g) != 0) { return false; }
		var parts = str.split('/'); if (parts.length != 3) { return false; }
		d = parseInt(parts[0], 10); m = parseInt(parts[1], 10); y = parseInt(parts[2], 10);
	} else if (format == 'm/d/Y') {
		if (str.search(/^\d{1,2}[\/]\d{1,2}[\/]\d{4}/g) != 0) { return false; }
		var parts = str.split('/'); if (parts.length != 3) { return false; }
		d = parseInt(parts[1], 10); m = parseInt(parts[0], 10); y = parseInt(parts[2], 10);
	} else if (format == 'm-d-Y') {
		if (str.search(/^\d{1,2}[\-]\d{1,2}[\-]\d{4}/g) != 0) { return false; }
		var parts = str.split('-'); if (parts.length != 3) { return false; }
		d = parseInt(parts[1], 10); m = parseInt(parts[0], 10); y = parseInt(parts[2], 10);
	} else if (format == 'Y-m-d H:i:s') {
		if (str.search(/^\d{4}[\-]\d{1,2}[\-]\d{1,2}[\s]\d{1,2}[\:]\d{1,2}[\:]\d{1,2}/g) != 0) { return false; }
		var parts = str.split('-'); if (parts.length != 3) { return false; }
		y = parseInt(parts[0], 10);	m = parseInt(parts[1], 10);
		var out = extractTime(parts[2]); if (out === false) { return false; }
		d = out[0]; h = out[1]; i = out[2]; s = out[3]; daytime = true;
	} else if (format == 'Y/m/d H:i:s') {
		if (str.search(/^\d{4}[\/]\d{1,2}[\/]\d{1,2}[\s]\d{1,2}[\:]\d{1,2}[\:]\d{1,2}/g) != 0) { return false; }
		var parts = str.split('/'); if (parts.length != 3) { return false; }
		y = parseInt(parts[0], 10);	m = parseInt(parts[1], 10);
		var out = extractTime(parts[2]); if (out === false) { return false; }
		d = out[0]; h = out[1]; i = out[2]; s = out[3]; daytime = true;
	} else if (format == 'd-m-Y H:i:s') {
		if (str.search(/^\d{1,2}[\-]\d{1,2}[\-]\d{4}[\s]\d{1,2}[\:]\d{1,2}[\:]\d{1,2}/g) != 0) { return false; }
		var parts = str.split('-'); if (parts.length != 3) { return false; }
		d = parseInt(parts[0], 10); m = parseInt(parts[1], 10);
		var out = extractTime(parts[2]); if (out === false) { return false; }
		y = out[0]; h = out[1]; i = out[2]; s = out[3]; daytime = true;
	} else if (format == 'd/m/Y H:i:s') {
		if (str.search(/^\d{1,2}[\/]\d{1,2}[\/]\d{4}[\s]\d{1,2}[\:]\d{1,2}[\:]\d{1,2}/g) != 0) { return false; }
		var parts = str.split('/'); if (parts.length != 3) { return false; }
		d = parseInt(parts[0], 10); m = parseInt(parts[1], 10);
		var out = extractTime(parts[2]); if (out === false) { return false; }
		y = out[0]; h = out[1]; i = out[2]; s = out[3]; daytime = true;
	} else if (format == 'm-d-Y H:i:s') {
		if (str.search(/^\d{1,2}[\-]\d{1,2}[\-]\d{4}[\s]\d{1,2}[\:]\d{1,2}[\:]\d{1,2}/g) != 0) { return false; }
		var parts = str.split('-'); if (parts.length != 3) { return false; }
		d = parseInt(parts[1], 10); m = parseInt(parts[0], 10);
		var out = extractTime(parts[2]); if (out === false) { return false; }
		y = out[0]; h = out[1]; i = out[2]; s = out[3]; daytime = true;
	} else if (format == 'm/d/Y H:i:s') {
		if (str.search(/^\d{1,2}[\/]\d{1,2}[\/]\d{4}[\s]\d{1,2}[\:]\d{1,2}[\:]\d{1,2}/g) != 0) { return false; }
		var parts = str.split('/'); if (parts.length != 3) { return false; }
		d = parseInt(parts[1], 10); m = parseInt(parts[0], 10);
		var out = extractTime(parts[2]); if (out === false) { return false; }
		y = out[0]; h = out[1]; i = out[2]; s = out[3]; daytime = true;
	} else {
		return false; //not supported format
	}

	if (daytime === true) {
		var dt = new Date(y, m - 1, d, h, i, s);
		if (dt.getMonth() + 1 != m) { return false; }
		if (dt.getDate() != d) { return false; }
		if (dt.getFullYear() != y) { return false; }
		if (dt.getHours() != h) { return false; }
		if (dt.getMinutes() != i) { return false; }
		if (dt.getSeconds() != s) { return false; }
		return true;
	} else {
		var dt = new Date(y, m - 1, d, h, i, s);
		if (dt.getMonth() + 1 != m) { return false; }
		if (dt.getDate() != d) { return false; }
		if (dt.getFullYear() != y) { return false; }
		return true;		
	}
}

/* USED IN elxValidateDate */
function extractTime(str) {
	var out = new Array(0, 0, 0, 0);
	var parts = str.split(' '); if (parts.length != 2) { return false; }
	out[0] = parseInt(parts[0], 10);
	var parts2 = parts[1].split(':'); if (parts2.length != 3) { return false; }
	out[1] = parseInt(parts2[0], 10); out[2] = parseInt(parts2[1], 10); out[3] = parseInt(parts2[2], 10);
	return out;
}

/* VALIDATE DATE INPUT FIELD */
function elxValidateDateBox(fel, format, allowempty) {
	if (!document.getElementById(fel)) { return true; }
	var fstr = document.getElementById(fel).value;
	return elxValidateDate(fstr, format, allowempty);
}

/* VALIDATE NUMERIC INPUT FIELD */
function elxValidateNumericBox(fel, allowempty) {
	if (!document.getElementById(fel)) { return true; }
	var fstr = document.getElementById(fel).value;
	if (fstr == '') {
		if (allowempty == true) { return true; } else { return false; }
	}
	var strValidChars = "0123456789.-";
	var strChar;
	var blnResult = true;
	for (i = 0; i < fstr.length && blnResult == true; i++) {
		strChar = fstr.charAt(i);
		if (strValidChars.indexOf(strChar) == -1) { blnResult = false; }
	}
	return blnResult;
}


/* VALIDATE DATE */
function elxValidateURL(str, allowempty) {
	if (str == '') {
		if (allowempty == true) { return true; } else { return false; }
	}
	var regexp = /(http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/
	return regexp.test(str);
}

/* VALIDATE DATE BOX */
function elxValidateURLBox(fel, allowempty) {
	if (!document.getElementById(fel)) { return true; }
	var fstr = document.getElementById(fel).value;
	return elxValidateURL(fstr, allowempty);
}

/* FOCUS ON A FORM ELEMENT */
function elxFocus(efel) {
	var element = document.getElementById(efel);
	if (!element) { return; }
	element.style.backgroundColor = '#feeded';
	element.style.borderColor = '#f7c2c2';
	element.focus();
	setTimeout("elxRestoreBoxColor('" + efel + "')", 1500);
}

/* RESTORE FORM ELEMENT BG COLOUR */
function elxRestoreBoxColor(efel) {
	document.getElementById(efel).style.backgroundColor = '#FFFFFF';
	document.getElementById(efel).style.borderColor = '#bbb';
}

/* PASSWORD STRENGTH METER */
function elxPasswordMeter(fname, fpword, fpuname) {
	if (!document.getElementById(fpword)) { return; }
	var fimg = fpword+'meter';
	if (!document.getElementById(fimg)) { return; }
	var password = document.getElementById(fpword).value;
	if ((fpuname != null) && (fpuname != '')) {
		if (document.getElementById(fpuname)) { var username = document.getElementById(fpuname).value; } else { var username = ''; }
	} else {
		var username = '';
	}
	var score = elxCheckStrongPassword(password, username);
	var baseurl = document.getElementById('elxisbase'+fname).value;
	if (score == -2000) {
		document.getElementById(fimg).src = baseurl+'/includes/libraries/elxis/form/level0.png';
		document.getElementById(fimg).title = 'short';
	} else if (score == -2001) {
		document.getElementById(fimg).src = baseurl+'/includes/libraries/elxis/form/level0.png';
		document.getElementById(fimg).title = 'username equals password';
	} else if (score < 20) {
		document.getElementById(fimg).src = baseurl+'/includes/libraries/elxis/form/level1.png';
		document.getElementById(fimg).title = 'very weak - '+score+'%';
	} else if (score < 40) {
		document.getElementById(fimg).src = baseurl+'/includes/libraries/elxis/form/level2.png';
		document.getElementById(fimg).title = 'weak - '+score+'%';
	} else if (score < 60) {
		document.getElementById(fimg).src = baseurl+'/includes/libraries/elxis/form/level3.png';
		document.getElementById(fimg).title = 'good - '+score+'%';
	} else if (score < 80) {
		document.getElementById(fimg).src = baseurl+'/includes/libraries/elxis/form/level4.png';
		document.getElementById(fimg).title = 'strong - '+score+'%';
	} else {
		document.getElementById(fimg).src = baseurl+'/includes/libraries/elxis/form/level5.png';
		document.getElementById(fimg).title = 'very strong - '+score+'%';
	}
}

/* CHECK PASSWORD STRENGTH */
function elxCheckStrongPassword(password, username) {
	if (password.length < 4) { return -2000; }
	if (username != '') {
    	if (password.toLowerCase()==username.toLowerCase()) { return -2001; }
	}
	var score = 0;
    score += password.length * 4;
    score += (elxCheckRepetition(1,password).length - password.length) * 1;
	score += (elxCheckRepetition(2,password).length - password.length) * 1;
	score += (elxCheckRepetition(3,password).length - password.length) * 1;
	score += (elxCheckRepetition(4,password).length - password.length) * 1;
	if (password.match(/(.*[0-9].*[0-9].*[0-9])/)){ score += 5;}
	if (password.match(/(.*[!,@,#,$,%,^,&,*,?,_,~].*[!,@,#,$,%,^,&,*,?,_,~])/)){ score += 5 ;}
	if (password.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/)){ score += 10;}
	if (password.match(/([a-zA-Z])/) && password.match(/([0-9])/)){ score += 15;}
	if (password.match(/([!,@,#,$,%,^,&,*,?,_,~])/) && password.match(/([0-9])/)){ score += 15;}
	if (password.match(/([!,@,#,$,%,^,&,*,?,_,~])/) && password.match(/([a-zA-Z])/)){score += 15;}
	if (password.match(/^\w+$/) || password.match(/^\d+$/) ){ score -= 10;}
	if (score < 0) { score = 0; }
	if (score > 100) { score = 100; }
	return parseInt(score);
}

/* CHECK STRING REPETITION */
function elxCheckRepetition(pLen,str) {
	var res = '';
	for (var i=0; i<str.length ; i++) {
		var repeated=true;
		for (var j=0;j < pLen && (j+i+pLen) < str.length;j++) {
			repeated=repeated && (str.charAt(j+i)==str.charAt(j+i+pLen));
		}
		if (j<pLen){ repeated=false; }
		if (repeated) { i+=pLen-1; repeated=false; } else { res+=str.charAt(i); }
	}
	return res;
}

/* POPUP WINDOW (optional attributes: w, h, title, scrollbars) */
function elxPopup(pageURL, w, h, pageTitle, scrbars) {
	if (!w) {
		var w = 600;
	} else {
		w = parseInt(w);
		if (w < 10) { w = 600; }
	}
	if (!h) {
		var h = 400;
	} else {
		h = parseInt(h);
		if (h < 10) { h = 400; }
	}
    if ((pageTitle === undefined) || (pageTitle === null) || (pageTitle == '')) { var pageTitle = 'popup window'; }
    if ((scrbars === undefined) || (scrbars === null) || (scrbars == '')) { var scrbars = 'yes'; }
	var pleft = (screen.width/2)-(w/2);
	var ptop = (screen.height/2)-(h/2);
	var win2 = window.open(pageURL, pageTitle, 'status=no, width='+w+', height='+h+', top='+ptop+', left='+pleft+', resizable=no, toolbar=no, menubar=no, location=no, directories=no, scrollbars='+scrbars+', copyhistory=no');
	win2.focus();
}

/* CREATE A STANDARD AJAX OBJECT */
function newStdAjax() {
    var ro;
    if (window.XMLHttpRequest) {
        ro = new XMLHttpRequest();
    } else if (window.ActiveXObject) {
        ro = new ActiveXObject("Msxml2.XMLHTTP");
        if (!ro) {
            ro = new ActiveXObject("Microsoft.XMLHTTP");
        }
    }
    return ro;
}

/* COMPATIBILITY CODE FOR JSON stringify */
var JSON = JSON || {};
JSON.stringify = JSON.stringify || function (obj) {
	var t = typeof (obj);
    if (t != "object" || obj === null) {
        if (t == "string") obj = '"'+obj+'"';
        return String(obj);
    } else {
        var n, v, json = [], arr = (obj && obj.constructor == Array);
        for (n in obj) {
            v = obj[n]; t = typeof(v);
            if (t == "string") v = '"'+v+'"';
            else if (t == "object" && v !== null) v = JSON.stringify(v);
            json.push((arr ? "" : '"' + n + '":') + String(v));
        }
        return (arr ? "[" : "{") + String(json) + (arr ? "]" : "}");
    }
};


/* AJAX WRAPPER - WORKS WITH OR WITHOUT JQUERY */
function elxAjax(etype, eurl, edata, eloadelement, eloadtext, successfunc, errorfunc) {
	if ((etype == null) || (etype == '')) { etype = 'GET'; }
	if (eurl == '') { return false; }
	if (errorfunc == null) {
		errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
        	alert('Error! '+errorThrown);
    	}
	}

	if (successfunc == null) {
		if ((eloadelement != null) && (eloadelement != '')) {
			successfunc = function (result) {
				document.getElementById(eloadelement).innerHTML = result;
			}
		} else {
			successfunc = function (result) { }
		}
	}

 	if ((eloadtext != null) && (eloadtext != '') && (eloadelement != null) && (eloadelement != '')) {
 		document.getElementById(eloadelement).innerHTML = eloadtext;
 		if (typeof jQuery != 'undefined') {
 			$('#'+eloadelement).fadeIn('slow');
		 } else {
		 	document.getElementById(eloadelement).style.display = '';
	 	}
	}

	if (etype == 'GET') {
		if (typeof jQuery != 'undefined') {
			if (edata && (typeof (edata) === 'object')) { edata = $.param(edata); }
        	$.ajax({
            	type: 'GET',
            	url: eurl,
            	data: edata,
            	success: successfunc,
            	error: errorfunc
        	});
		} else {
			var rhttp = newStdAjax();
			if (edata && (typeof (edata) === 'object')) {
				var sdata = '';
				for (k in edata) { sdata += k+'='+edata[k]+'&'; }
				sdata += 'rnd='+Math.random();
				edata = sdata;
			}

			try {
            	rhttp.open('GET', eurl+'?'+edata, true);
            	rhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            	rhttp.setRequestHeader('charset', 'utf-8');
				rhttp.onreadystatechange = function () {
					if (rhttp.readyState == 4) {
						if (rhttp.status != 200) {
							errorfunc(rhttp.responseText, rhttp.status, rhttp.statusText);
						} else {
							successfunc(rhttp.responseText);
						}
					}
				};
            	rhttp.send(null);
			}
			catch(e){}
			finally{}
		}

        return;
	}

	if (etype == 'JSON') {
		if (edata && (typeof(edata) === 'object')) { edata = JSON.stringify(edata); }
		if (typeof jQuery != 'undefined') {
        	$.ajax({
            	type: 'POST',
            	url: eurl,
            	data: edata,
            	dataType: 'json',
            	contentType: 'application/json; charset=utf-8',
            	success: successfunc,
				error: errorfunc
			});
			return;
		} else {
			var rhttp = newStdAjax();
			try {
            	rhttp.open('POST', eurl, true);
            	rhttp.setRequestHeader('Content-Type', 'application/json');
            	rhttp.setRequestHeader('charset', 'utf-8');
				rhttp.onreadystatechange = function () {
					if (rhttp.readyState == 4) {
						if (rhttp.status != 200) {
							errorfunc(rhttp.responseText, rhttp.status, rhttp.statusText);
						} else {
							successfunc(rhttp.responseText);
						}
					}
				};
            	rhttp.send(edata);
			}
			catch(e){}
			finally{}
		}
	}

	if (etype == 'POST') {
		if (typeof jQuery != 'undefined') {
			if (edata && (typeof (edata) === 'object')) { edata = $.param(edata); }
        	$.ajax({
            	type: 'POST',
            	url: eurl,
            	data: edata,
            	dataType: 'html',
            	contentType: 'application/x-www-form-urlencoded; charset=utf-8',
            	success: successfunc,
				error: errorfunc
			});
			return;
		} else {
			if (edata && (typeof (edata) === 'object')) {
				var sdata = '';
				for (k in edata) { sdata += k+'='+edata[k]+'&'; }
				sdata += 'rnd='+Math.random();
				edata = sdata;
			}
			var rhttp = newStdAjax();
			try {
           		rhttp.open('POST', eurl, true);
           		rhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
           		rhttp.setRequestHeader('charset', 'utf-8');
				rhttp.onreadystatechange = function () {
					if (rhttp.readyState == 4) {
						if (rhttp.status != 200) {
							errorfunc(rhttp.responseText, rhttp.status, rhttp.statusText);
						} else {
							successfunc(rhttp.responseText);
						}
					}
				};
           		rhttp.send(edata);
			}
			catch(e){}
			finally{}
		}
	}
}

/* SUBMIT ELXIS FORM */
function elxSubmit(pressbutton, formname, actionurl) {
	if (typeof pressbutton == 'undefined') { pressbutton = ''; }
	if (typeof formname == 'undefined') { formname = 'elxisform'; }
	if (formname == '') { formname = 'elxisform'; }
	if (typeof actionurl == 'undefined') { actionurl = ''; }
	if (formname == 'elxisform') {
		document.elxisform.task.value = pressbutton;
		if (actionurl != '')  { document.elxisform.action = actionurl; }
		elxformvalelxisform();
	} else {
		document[formname].task.value = pressbutton;
		if (actionurl != '')  { document[formname].action = actionurl; }
		var func = window['elxformval'+formname];
		if (typeof func === 'function') { func(); }
	}
}

/* SET AUTOCOMPLETE OFF FOR AN ELEMENT */
function elxAutocompOff(elem) {
	if (null == elem) { return; }
	if (window.addEventListener) {
		window.addEventListener('load', function() {
			document.getElementById(elem).setAttribute("autocomplete", "off");
		}, false);
	} else if (window.attachEvent) {
		window.attachEvent('onload', function() {
			document.getElementById(elem).setAttribute("autocomplete", "off");
		});
	}
}

/* SUPPORT FOR MULTIPLE WINDOW ONLOAD EVENTS */
function elxLoadEvent(func) {
	if (typeof window.onload != 'function') {
		window.onload = func;
	} else {
		var oldonload = window.onload;
		window.onload = function() {
			if (oldonload) { oldonload(); }
			func();
		}
	}
}
