/*
Elxis Translator
Author: Ioannis Sannos (a.k.a. datahell)
http://www.elxis.org
*/


/* PICK LANGUAGE */
function etrans_picklang(lng) {
	var elxis_base = document.getElementById('etrans_elxbase').innerHTML;
	
	var imgObj = document.getElementById('etrans_flag');
	imgObj.src = elxis_base+'/includes/libraries/elxis/language/flags/'+lng+'.png';
	imgObj.title = lng;
	document.getElementById('etrans_language').value = lng;

	var transObj = document.getElementById('etrtranslation');
	if ((lng == 'fa') || (lng == 'he') || (lng == 'ac') || (lng == 'ah') || (lng == 'aj') || (lng == 'ap') || (lng == 'aq') || (lng == 'ar') || (lng == 'at') || (lng == 'aw') || (lng == 'ph')) {
		var textdir = 'rtl';
	} else {
		var textdir = 'ltr';
	}
	transObj.dir = textdir;
	trans_marksiunsaved(transObj);

	var coverObj = document.getElementById('etrans_picklang_cover');
	coverObj.style.display = 'none';
}


/* COPY SOURCE TEXT */
function etrans_copy() {
	var origObj = document.getElementById('etroriginal');
	var transObj = document.getElementById('etrtranslation');

	if (origObj.innerText != undefined) {
		transObj.value = origObj.innerText;
	} else if (origObj.textContent != undefined) {
		transObj.value = origObj.textContent;
	} else {
		transObj.value = origObj.innerHTML;
	}

	trans_marksiunsaved(transObj);
}


/* COPY SOURCE HTML */
function etrans_copyhtml() {
	var origObj = document.getElementById('etroriginal');
	var transObj = document.getElementById('etrtranslation');
	
	transObj.value = origObj.innerHTML;
	trans_marksiunsaved(transObj);
}


/* MARK ELEMENT AS UNSAVED */
function trans_marksiunsaved(obj) {
	obj.style.borderColor = '#FF0000';
}


/* MARK ELEMENT AS SAVED */
function trans_marksisaved(obj) {
	obj.style.borderColor = '#AAAAAA';
}


/* SAVE TRANSLATION */
function etrans_sisave(islongtext) {
	var transObj = document.getElementById('etrtranslation');
	var cur_lang = document.getElementById('etrans_language').value;
	var trtext = transObj.value;
	if (trtext == '') { return false; }
	var trid = document.getElementById('etrans_trid').value;
	trid = parseInt(trid, 10);
	var ctg = document.getElementById('etrans_category').value;
	var elem = document.getElementById('etrans_element').value;
	var elid = document.getElementById('etrans_elid').value;
	var edata = {'trid': trid, 'category': ctg, 'element': elem, 'elid': elid, 'language': cur_lang, 'translation': trtext };
	if (islongtext == 1) {
		var eurl = document.getElementById('etrans_combase').innerHTML+'api/tsave';
	} else {
		var eurl = document.getElementById('etrans_combase').innerHTML+'api/save';
	}
	var successfunc = function(xreply) {
		var jsonObj = JSON.parse(xreply);
		if (parseInt(jsonObj.error, 10) > 0) {
			return false;
		} else {
			if (trid < 1) { document.getElementById('etrans_trid').value = jsonObj.trid; }
			trans_marksisaved(transObj);
		}
	}
	elxAjax('POST', eurl, edata, null, null, successfunc, null);
}

/* SWITCH TEXT DIRECTION */
function etrans_switchdir() {
	var transObj = document.getElementById('etrtranslation');
	var cur_dir = transObj.dir;
	if (cur_dir == 'ltr') {
		transObj.dir = 'rtl';
	} else {
		transObj.dir = 'ltr';
	}
}

/* GET AUTO TRANSLATION FROM MICROSOFT */
function etrans_sibing() {
	var transObj = document.getElementById('etrtranslation');
	var origObj = document.getElementById('etroriginal');
	var cur_lang = document.getElementById('etrans_language').value;
	var bingapi = document.getElementById('etrans_bingapi').value;
	var deflang = document.getElementById('etrans_deflang').value;

	var original_text = origObj.innerHTML;
	if (original_text == '') { return false; }
	if (bingapi == '') { return false; }

	window.bingcallback = function(response) {
		transObj.value = response;
		trans_marksiunsaved(transObj);
	}

	var s = document.createElement("script");
	s.src = 'https://api.microsofttranslator.com/V2/Ajax.svc/Translate?oncomplete=bingcallback&appId='+bingapi+'&from='+deflang+'&to='+cur_lang+'&contentType=text/plain&text='+original_text;
	document.getElementsByTagName("head")[0].appendChild(s);	
}
