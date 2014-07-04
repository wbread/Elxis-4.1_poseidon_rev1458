/*
Multilingal XML parameters
Author: Ioannis Sannos (a.k.a. datahell)
http://www.elxis.org
*/

/* SWITCH PARAMETER ELEMENT'S LANGUAGE */
function paramlang_switch(elname, isrtl) {
	if (!document.getElementById('translp_'+elname)) { return; } //select
	if (!document.getElementById('params'+elname)) { return; } //basic input
	var selObj = document.getElementById('translp_'+elname);
	var cur_lang = selObj.options[selObj.selectedIndex].value;
	var textObj = document.getElementById('params'+elname);
	var textdir = getLangParamDir(isrtl, cur_lang);

	selObj.className = 'selectbox mlflag'+cur_lang;
	textObj.className = 'inputbox mlflag'+cur_lang;
	textObj.dir = textdir;

	if (document.getElementById('params'+elname+'_ml'+cur_lang)) {
		var hidObj = document.getElementById('params'+elname+'_ml'+cur_lang);
		textObj.value = hidObj.value;
		param_marksaved(textObj);
	} else {
		var hidObj = document.createElement('input');
		hidObj.setAttribute('type', 'hidden');
		hidObj.setAttribute('name', 'params['+elname+'_ml'+cur_lang+']');
		hidObj.setAttribute('id', 'params'+elname+'_ml'+cur_lang);
		hidObj.setAttribute('dir', textdir);
		hidObj.setAttribute('value', '');
		document.getElementById('mlparamscontainer').appendChild(hidObj);
		textObj.value = '';
		param_markunsaved(textObj);
	}
}

/* SAVE LANGUAGE SPECIFIC PARAM VALUE */
function paramlang_save(elname) {
	if (!document.getElementById('translp_'+elname)) { return; } //select
	if (!document.getElementById('params'+elname)) { return; } //basic input
	var selObj = document.getElementById('translp_'+elname);
	var cur_lang = selObj.options[selObj.selectedIndex].value;
	var textObj = document.getElementById('params'+elname);

	if (document.getElementById('params'+elname+'_ml'+cur_lang)) {
		var hidObj = document.getElementById('params'+elname+'_ml'+cur_lang);
		hidObj.value = textObj.value;
	} else {
		var hidObj = document.createElement('input');
		hidObj.setAttribute('type', 'hidden');
		hidObj.setAttribute('name', 'params['+elname+'_ml'+cur_lang+']');
		hidObj.setAttribute('id', 'params'+elname+'_ml'+cur_lang);
		hidObj.setAttribute('value', textObj.value);
		hidObj.setAttribute('dir', textObj.dir);
		document.getElementById('mlparamscontainer').appendChild(hidObj);
	}
	param_marksaved(textObj);
}

/* GET PARAMETER INPUT FIELD TEXT DIR */
function getLangParamDir(isrtl, lng) {
	if (isrtl != 1) { return 'ltr'; }
	if (lng == 'ac') { return 'rtl'; }
	if (lng == 'ah') { return 'rtl'; }
	if (lng == 'aj') { return 'rtl'; }
	if (lng == 'al') { return 'rtl'; }
	if (lng == 'ap') { return 'rtl'; }
	if (lng == 'aq') { return 'rtl'; }
	if (lng == 'ar') { return 'rtl'; }
	if (lng == 'at') { return 'rtl'; }
	if (lng == 'aw') { return 'rtl'; }
	if (lng == 'fa') { return 'rtl'; }
	if (lng == 'he') { return 'rtl'; }
	if (lng == 'ph') { return 'rtl'; }
	return 'ltr';
}

/* MARK PARAMETER ELEMENT AS UNSAVED */
function param_markunsaved(obj) {
	obj.style.borderColor = '#FF0000';
}

/* MARK PARAMETER ELEMENT AS SAVED */
function param_marksaved(obj) {
	obj.style.borderColor = '#AAAAAA';
}
