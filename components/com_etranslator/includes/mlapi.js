/*
Elxis Translator
Author: Ioannis Sannos (a.k.a. datahell)
http://www.elxis.org
*/

/* GET MULTILINGUAL FORM INSTANCE */
function getMLIstance(mlinstance) {
	mlinstance = parseInt(mlinstance, 10);
	if (mlinstance == 1) {
		if (typeof mldata1 == "undefined") { return false; }
		var mldata = mldata1;
	} else if (mlinstance == 2) {
		if (typeof mldata2 == "undefined") { return false; }
		var mldata = mldata2;
	} else if (mlinstance == 3) {
		if (typeof mldata3 == "undefined") { return false; }
		var mldata = mldata3;
	} else if (mlinstance == 4) {
		if (typeof mldata4 == "undefined") { return false; }
		var mldata = mldata4;
	} else if (mlinstance == 5) {
		if (typeof mldata5 == "undefined") { return false; }
		var mldata = mldata5;
	} else {
		if (typeof mldata1 == "undefined") { return false; }
		var mldata = mldata1;
	}
	return mldata;
}

/* CHECK IF LANGUAGE IS AN RTL ONE */
function isRightToLeft(rtllangs, lng) {
	for (var i=0; i < rtllangs.length; i++) {
		if (rtllangs[i] == lng) { return true; }
	}
	return false;
}

/* GET MULTILINGUAL ITEM */
function getMLItem(mlitems, elemid) {
	for (var i=0; i < mlitems.length; i++) {
		if (mlitems[i].item == elemid) { return mlitems[i]; }
	}
	return false;
}

/* SWITCH TRANSLATE ELEMENT LANGUAGE */
function translang_switch(mlinstance, elemid) {
	var mldata = getMLIstance(mlinstance);
	if (mldata === false) { return false; }
	var mlitem = getMLItem(mldata.items, elemid);
	if (mlitem === false) { return false; }
	if (typeof mlapibase == "undefined") { return false; }

	var selObj = document.getElementById('transl_'+elemid);
	var cur_lang = selObj.options[selObj.selectedIndex].value;
	var origObj = document.getElementById(elemid);
	var transObj = document.getElementById('trans_'+elemid);
	var btnObj = document.getElementById('transb_'+elemid);
	var msgObj = document.getElementById('transmsg_'+elemid);

	selObj.className = 'selectbox mlflag'+cur_lang;
	msgObj.innerHTML = '';
	msgObj.className = 'ml_message';
	if (cur_lang == mldata.lang) {
		origObj.style.display = '';
		transObj.style.display = 'none';
		btnObj.style.display = 'none';
		msgObj.style.display = 'none';
	} else {
		if (isRightToLeft(mldata.rtllangs, cur_lang) === true) {
			transObj.dir = 'rtl';
		} else {
			transObj.dir = 'ltr';
		}
		transObj.className = 'inputbox mlflag'+cur_lang;

		transObj.value = '';
		msgObj.innerHTML = mldata.waitmsg;
		origObj.style.display = 'none';
		transObj.style.display = '';
		btnObj.style.display = '';
		msgObj.style.display = '';

		var edata = { 'category': mlitem.ctg, 'element': mlitem.elem, 'elid': mlitem.elid, 'language': cur_lang };
		var eurl = mlapibase+'load';
		var successfunc = function(xreply) {
			var jsonObj = JSON.parse(xreply);
			if (parseInt(jsonObj.error, 10) > 0) {
				msgObj.className = 'ml_message ml_error';
				document.getElementById('transid_'+elemid).value = 0;
				if (jsonObj.errormsg != '') {
					msgObj.innerHTML = jsonObj.errormsg;
				} else {
					msgObj.innerHTML = 'Action failed!';
				}
				return false;
			} else {
				msgObj.style.display = 'none';
				msgObj.innerHTML = '';
				document.getElementById('transid_'+elemid).value = jsonObj.trid;
				if (jsonObj.trid > 0) {
					trans_marksaved(transObj);
				} else {
					trans_markunsaved(transObj);
				}
				transObj.value = jsonObj.translation;
			}
		}
		elxAjax('POST', eurl, edata, null, null, successfunc, null);
	}
}


/* SAVE TEXT STRING TRANSLATION */
function translang_save(mlinstance, elemid) {
	var mldata = getMLIstance(mlinstance);
	if (mldata === false) { return false; }
	var mlitem = getMLItem(mldata.items, elemid);
	if (mlitem === false) { return false; }
	if (typeof mlapibase == "undefined") { return false; }

	var selObj = document.getElementById('transl_'+elemid);
	var cur_lang = selObj.options[selObj.selectedIndex].value;
	var transObj = document.getElementById('trans_'+elemid);
	var msgObj = document.getElementById('transmsg_'+elemid);
	var transidObj = document.getElementById('transid_'+elemid);

	var trtext = transObj.value;
	if (trtext == '') {
		msgObj.className = 'ml_message ml_error';
		msgObj.innerHTML = mldata.prtransmsg;
		msgObj.style.display = '';
		return false;
	}

	msgObj.className = 'ml_message';
	msgObj.style.display = '';
	msgObj.innerHTML = mldata.waitmsg;

	var trid = parseInt(transidObj.value, 10);
	var edata = {'trid': trid, 'category': mlitem.ctg, 'element': mlitem.elem, 'elid': mlitem.elid, 'language': cur_lang, 'translation': trtext };
	var eurl = mlapibase+'save';
	var successfunc = function(xreply) {
		var jsonObj = JSON.parse(xreply);
		if (parseInt(jsonObj.error, 10) > 0) {
			msgObj.className = 'ml_message ml_error';
			document.getElementById('transid_'+elemid).value = 0;
			if (jsonObj.errormsg != '') {
				msgObj.innerHTML = jsonObj.errormsg;
			} else {
				msgObj.innerHTML = 'Action failed!';
			}
			return false;
		} else {
			msgObj.className = 'ml_message ml_success';
			msgObj.innerHTML = jsonObj.successmsg;

			trans_marksaved(transObj);
			if (trid < 1) { transidObj.value = jsonObj.trid; }
		}
	}
	elxAjax('POST', eurl, edata, null, null, successfunc, null);
}

/* DELETE TRANSLATION */
function translang_delete(mlinstance, elemid) {
	var mldata = getMLIstance(mlinstance);
	if (mldata === false) { return false; }
	var mlitem = getMLItem(mldata.items, elemid);
	if (mlitem === false) { return false; }
	if (typeof mlapibase == "undefined") { return false; }

	var selObj = document.getElementById('transl_'+elemid);
	var cur_lang = selObj.options[selObj.selectedIndex].value;
	var transObj = document.getElementById('trans_'+elemid);
	var msgObj = document.getElementById('transmsg_'+elemid);
	var transidObj = document.getElementById('transid_'+elemid);
	var origObj = document.getElementById(elemid);
	var btnObj = document.getElementById('transb_'+elemid);

	var trid = parseInt(transidObj.value, 10);
	if (trid < 1) {
		transObj.value = '';
		transObj.style.display = 'none';
		btnObj.style.display = 'none';
		origObj.style.display = '';
		msgObj.style.display = 'none';
		translang_switchSelector(selObj, mldata.lang);
		trans_marksaved(transObj);
		return;
	}

	msgObj.className = 'ml_message';
	msgObj.style.display = '';
	msgObj.innerHTML = mldata.waitmsg;

	var edata = {'trid': trid, 'category': mlitem.ctg, 'element': mlitem.elem, 'elid': mlitem.elid, 'language': cur_lang };
	var eurl = mlapibase+'delete';
	var successfunc = function(xreply) {
		var jsonObj = JSON.parse(xreply);
		if (parseInt(jsonObj.error, 10) > 0) {
			msgObj.className = 'ml_message ml_error';
			if (jsonObj.errormsg != '') {
				msgObj.innerHTML = jsonObj.errormsg;
			} else {
				msgObj.innerHTML = 'Action failed!';
			}
			return false;
		} else {
			msgObj.style.display = 'none';
			transidObj.value = 0;
			transObj.value = '';
			transObj.style.display = 'none';
			btnObj.style.display = 'none';
			origObj.style.display = '';
			translang_switchSelector(selObj, mldata.lang);
		}
	}
	elxAjax('POST', eurl, edata, null, null, successfunc, null);
}


/* GET AUTO TRANSLATION FROM MICROSOFT */
function translang_bing(mlinstance, elemid) {
	var mldata = getMLIstance(mlinstance);
	if (mldata === false) { return false; }
	var mlitem = getMLItem(mldata.items, elemid);
	if (mlitem === false) { return false; }
	if (typeof mlapibase == "undefined") { return false; }

	var selObj = document.getElementById('transl_'+elemid);
	var cur_lang = selObj.options[selObj.selectedIndex].value;
	var transObj = document.getElementById('trans_'+elemid);
	var msgObj = document.getElementById('transmsg_'+elemid);
	var origObj = document.getElementById(elemid);

	msgObj.style.display = '';
	var original_text = origObj.value;
	if (original_text == '') {
		msgObj.className = 'ml_message ml_error';
		msgObj.innerHTML = 'First provide the original text for language '+mldata.lang+'!';
		return false;
	}

	if (mldata.bingapi == '') {
		msgObj.className = 'ml_message ml_error';
		msgObj.innerHTML = 'Enable Bing translation by providing an API Id in eTranslator component!';
		return false;
	}

	msgObj.className = 'ml_message';
	msgObj.innerHTML = mldata.waitmsg;

	window.mycallback = function(response) {
		transObj.value = response;
		msgObj.style.display = 'none';
		trans_markunsaved(transObj);
	}

	var s = document.createElement("script");
	s.src = 'https://api.microsofttranslator.com/V2/Ajax.svc/Translate?oncomplete=mycallback&appId='+mldata.bingapi+'&from='+mldata.lang+'&to='+cur_lang+'&contentType=text/plain&text='+original_text;
	document.getElementsByTagName("head")[0].appendChild(s);	
}


/* SET LANGUAGE SELECTOR */
function translang_switchSelector(selObj, lng) {
	for (var i=0; i < selObj.options.length; i++) {
		if (selObj.options[i].value == lng) {
			if (selObj.selectedIndex != i) {
				selObj.selectedIndex = i;
			}
			break;
		}
	}
	selObj.className = 'selectbox mlflag'+lng;
}


/* MARK ELEMENT AS UNSAVED */
function trans_markunsaved(obj) {
	obj.style.borderColor = '#FF0000';
}


/* MARK ELEMENT AS SAVED */
function trans_marksaved(obj) {
	obj.style.borderColor = '#AAAAAA';
}

/* SWITCH TRANSLATE TEXTAREA ELEMENT LANGUAGE */
function translang_edswitch(mlinstance, elemid, iseditor) {
	var mldata = getMLIstance(mlinstance);
	if (mldata === false) { return false; }
	var mlitem = getMLItem(mldata.items, elemid);
	if (mlitem === false) { return false; }
	if (typeof mlapibase == "undefined") { return false; }

	var selObj = document.getElementById('transl_'+elemid);
	var cur_lang = selObj.options[selObj.selectedIndex].value;
	var btnObj = document.getElementById('transb_'+elemid);
	var msgObj = document.getElementById('transmsg_'+elemid);
	var beforeObj = document.getElementById('transbef_'+elemid);

	if (isRightToLeft(mldata.rtllangs, cur_lang) === true) {
		var editordir = 'rtl';
	} else {
		var editordir = 'ltr';
	}

	selObj.className = 'selectbox mlflag'+cur_lang;
	msgObj.innerHTML = '';
	msgObj.className = 'ml_message';

	if (cur_lang == mldata.lang) {
		btnObj.style.display = 'none';
		msgObj.style.display = 'none';
		trans_marksaved(selObj);
		if (iseditor == 1) {
			var editor_inst = CKEDITOR.instances[elemid];
			editor_inst.config.contentsLanguage = cur_lang;
			editor_inst.config.contentsLangDirection = editordir;
			var editor_data = document.getElementById('transorig_'+elemid).value;
			editor_inst.setData(editor_data);
			document.getElementById('transorig_'+elemid).value = '';
		} else {
			var editor_inst = document.getElementById(elemid);
			editor_inst.dir = editordir;
			editor_inst.value = document.getElementById('transorig_'+elemid).value;
			document.getElementById('transorig_'+elemid).value = '';
		}
		beforeObj.value = cur_lang;
	} else {
		msgObj.innerHTML = mldata.waitmsg;
		btnObj.style.display = '';
		msgObj.style.display = '';

		if (iseditor == 1) {
			var editor_inst = CKEDITOR.instances[elemid];
			editor_inst.config.contentsLanguage = cur_lang;
			editor_inst.contentsLangDirection = editordir;
			if (beforeObj.value == mldata.lang) {
				var editor_data = editor_inst.getData();
				document.getElementById('transorig_'+elemid).value = editor_data;
			}
			editor_inst.setData('');
		} else {
			var editor_inst = document.getElementById(elemid);
			editor_inst.dir = editordir;
			if (beforeObj.value == mldata.lang) {
				document.getElementById('transorig_'+elemid).value = editor_inst.value;
			}
			editor_inst.value = '';
		}
		
		beforeObj.value = cur_lang;
		var edata = { 'category': mlitem.ctg, 'element': mlitem.elem, 'elid': mlitem.elid, 'language': cur_lang };
		var eurl = mlapibase+'load';
		var successfunc = function(xreply) {
			var jsonObj = JSON.parse(xreply);
			if (parseInt(jsonObj.error, 10) > 0) {
				msgObj.className = 'ml_message ml_error';
				document.getElementById('transid_'+elemid).value = 0;
				if (jsonObj.errormsg != '') {
					msgObj.innerHTML = jsonObj.errormsg;
				} else {
					msgObj.innerHTML = 'Action failed!';
				}
				return false;
			} else {
				msgObj.style.display = 'none';
				msgObj.innerHTML = '';
				document.getElementById('transid_'+elemid).value = jsonObj.trid;
				if (jsonObj.trid > 0) {
					trans_marksaved(selObj);
				} else {
					trans_markunsaved(selObj);
				}

				if (iseditor == 1) {
					editor_inst.setData(jsonObj.translation);
				} else {
					editor_inst.value = jsonObj.translation;
				}
			}
		}
		elxAjax('POST', eurl, edata, null, null, successfunc, null);
	}
}


/* SAVE TEXTAREA TRANSLATION */
function translang_edsave(mlinstance, elemid, iseditor) {
	var mldata = getMLIstance(mlinstance);
	if (mldata === false) { return false; }
	var mlitem = getMLItem(mldata.items, elemid);
	if (mlitem === false) { return false; }
	if (typeof mlapibase == "undefined") { return false; }

	var selObj = document.getElementById('transl_'+elemid);
	var cur_lang = selObj.options[selObj.selectedIndex].value;
	var msgObj = document.getElementById('transmsg_'+elemid);
	var transidObj = document.getElementById('transid_'+elemid);

	if (iseditor == 1) {
		var editor_inst = CKEDITOR.instances[elemid];
		var trtext = editor_inst.getData();
	} else {
		var editor_inst = document.getElementById(elemid);
		var trtext = editor_inst.value;
	}

	if (trtext == '') {
		msgObj.className = 'ml_message ml_error';
		msgObj.innerHTML = mldata.prtransmsg;
		msgObj.style.display = '';
		return false;
	}

	msgObj.className = 'ml_message';
	msgObj.style.display = '';
	msgObj.innerHTML = mldata.waitmsg;

	var trid = parseInt(transidObj.value, 10);
	var edata = {'trid': trid, 'category': mlitem.ctg, 'element': mlitem.elem, 'elid': mlitem.elid, 'language': cur_lang, 'translation': trtext };
	var eurl = mlapibase+'tsave';
	var successfunc = function(xreply) {
		var jsonObj = JSON.parse(xreply);
		if (parseInt(jsonObj.error, 10) > 0) {
			msgObj.className = 'ml_message ml_error';
			document.getElementById('transid_'+elemid).value = 0;
			if (jsonObj.errormsg != '') {
				msgObj.innerHTML = jsonObj.errormsg;
			} else {
				msgObj.innerHTML = 'Action failed!';
			}
			return false;
		} else {
			msgObj.className = 'ml_message ml_success';
			msgObj.innerHTML = jsonObj.successmsg;

			trans_marksaved(selObj);
			if (trid < 1) { transidObj.value = jsonObj.trid; }
		}
	}
	elxAjax('POST', eurl, edata, null, null, successfunc, null);
}


/* DELETE TRANSLATION */
function translang_eddelete(mlinstance, elemid, iseditor) {
	var mldata = getMLIstance(mlinstance);
	if (mldata === false) { return false; }
	var mlitem = getMLItem(mldata.items, elemid);
	if (mlitem === false) { return false; }
	if (typeof mlapibase == "undefined") { return false; }

	var selObj = document.getElementById('transl_'+elemid);
	var cur_lang = selObj.options[selObj.selectedIndex].value;
	var msgObj = document.getElementById('transmsg_'+elemid);
	var transidObj = document.getElementById('transid_'+elemid);
	var btnObj = document.getElementById('transb_'+elemid);

	if (isRightToLeft(mldata.rtllangs, mldata.lang) === true) {
		var editordir = 'rtl';
	} else {
		var editordir = 'ltr';
	}

	var trid = parseInt(transidObj.value, 10);
	if (trid < 1) {
		if (iseditor == 1) {
			var editor_inst = CKEDITOR.instances[elemid];
			editor_inst.config.contentsLanguage = mldata.lang;
			editor_inst.config.contentsLangDirection = editordir;
			var editor_data = document.getElementById('transorig_'+elemid).value;
			editor_inst.setData(editor_data);
			document.getElementById('transorig_'+elemid).value = '';
		} else {
			var editor_inst = document.getElementById(elemid);
			editor_inst.dir = editordir;
			editor_inst.value = document.getElementById('transorig_'+elemid).value;
			document.getElementById('transorig_'+elemid).value = '';
		}
		btnObj.style.display = 'none';
		msgObj.style.display = 'none';
		translang_switchSelector(selObj, mldata.lang);
		document.getElementById('transbef_'+elemid).value = mldata.lang;
		trans_marksaved(selObj);
		return;
	}

	msgObj.className = 'ml_message';
	msgObj.style.display = '';
	msgObj.innerHTML = mldata.waitmsg;

	var edata = {'trid': trid, 'category': mlitem.ctg, 'element': mlitem.elem, 'elid': mlitem.elid, 'language': cur_lang };
	var eurl = mlapibase+'delete';
	var successfunc = function(xreply) {
		var jsonObj = JSON.parse(xreply);
		if (parseInt(jsonObj.error, 10) > 0) {
			msgObj.className = 'ml_message ml_error';
			if (jsonObj.errormsg != '') {
				msgObj.innerHTML = jsonObj.errormsg;
			} else {
				msgObj.innerHTML = 'Action failed!';
			}
			return false;
		} else {
			msgObj.style.display = 'none';
			transidObj.value = 0;
			btnObj.style.display = 'none';
			if (iseditor == 1) {
				var editor_inst = CKEDITOR.instances[elemid];
				editor_inst.config.contentsLanguage = mldata.lang;
				editor_inst.config.contentsLangDirection = editordir;
				var editor_data = document.getElementById('transorig_'+elemid).value;
				editor_inst.setData(editor_data);
				document.getElementById('transorig_'+elemid).value = '';
			} else {
				var editor_inst = document.getElementById(elemid);
				editor_inst.dir = editordir;
				editor_inst.value = document.getElementById('transorig_'+elemid).value;
				document.getElementById('transorig_'+elemid).value = '';
			}
			translang_switchSelector(selObj, mldata.lang);
			document.getElementById('transbef_'+elemid).value = mldata.lang;
			trans_marksaved(selObj);
		}
	}
	elxAjax('POST', eurl, edata, null, null, successfunc, null);
}

/* MARK TEXTAREA UNSAVED */
function trans_marktareaunsaved(elemid) {
	var selObj = document.getElementById('transl_'+elemid);
	trans_markunsaved(selObj);
}
