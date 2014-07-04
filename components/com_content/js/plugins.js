/* Component Content javascript */
/* Elxis CMS - http://www.elxis.org */
/* Ioannis Sannos (datahell) */

/* LOAD PLUGIN */
function loadPlugin(fn) {
	var selObj = document.getElementById('plugin');
	var plugid = selObj.options[selObj.selectedIndex].value;
	plugid = parseInt(plugid, 10);
	if (plugid < 1) {
		if (document.getElementById('plug_load')) { document.getElementById('plug_load').innerHTML = ''; }
		return false;
	}
	var loadingtext = document.getElementById('lng_wait').innerHTML;
	var edata = {'id':plugid, 'fn':fn, 'task':'load'};
	var eurl = document.getElementById('plugbase').innerHTML;
	var successfunc = function(xreply) {
		loadPluginHead(plugid, eurl);
		document.getElementById('plug_load').innerHTML = xreply;
		pluginTabs();
	}
	elxAjax('POST', eurl, edata, 'plug_load', loadingtext, successfunc, null);
}


/* LOAD PLUGIN HEAD ELEMENTS */
function loadPluginHead(plugid, eurl) {
	var rnd = Math.random();
	var e2data = {'id':plugid, 'task':'head', 'rnd':rnd };
	var success2func = function(x2reply) {
		var jsonObj = JSON.parse(x2reply);
		if (parseInt(jsonObj.error, 10) > 0) {
			return false;
		} else {
			var len = jsonObj.css.length;
			if (len > 0) {
				for (var i = 0; i < len; i++) {
					var selem = document.createElement('style');
					selem.type = 'text/css';
					selem.src = jsonObj.css[i];
					selem.media = 'all';
					document.getElementsByTagName('head')[0].appendChild(selem);
				}
			}
			var len2 = jsonObj.js.length;
			if (len2 > 0) {
				for (var i = 0; i < len2; i++) {
					var s2elem = document.createElement('script');
					s2elem.type = 'text/javascript';
					s2elem.src = jsonObj.js[i];
					document.getElementsByTagName('head')[0].appendChild(s2elem);
				}
			}
		}
	}
	elxAjax('POST', eurl, e2data, null, null, success2func, null);
}

/* INITIALIZE TABS IN AJAX CONTENT */
function pluginTabs() {
	if (!document.getElementById('tab_plugins_1')) { return false; }
	$(".tab_content").hide();
	$("ul.tabs li:first").addClass("active").show();
	$(".tab_content:first").show();
	$("ul.tabs li").click(function() {
		$("ul.tabs li").removeClass("active");
		$(this).addClass("active");
		$(".tab_content").hide();
		var activeTab = $(this).find("a").attr("href");
		$(activeTab).fadeIn();
		return false;
	});
}

/* ADD PLUGIN CODE IN COMPOSER INPUT BOX */
function addPluginCode(pcode) {
	if (pcode == '') {
		alert('Plugin code can not be empty!');
		return false;
	}
	var pObj = document.getElementById('plugincode');
	pObj.value = pcode;
	pObj.className = 'plug_inputtexton';
	setTimeout('stopPCodehigh()', 1000);
}

/* STOP PLUGIN CODE INPUT TEXT HIGHLIGHT */
function stopPCodehigh() {
	document.getElementById('plugincode').className = 'plug_inputtext';
}

/* IMPORT PLUGIN CODE IN EDITOR */
function plugImportCode(fn) {
	var pcode = document.getElementById('plugincode').value;
	if (pcode == '') {
		alert('Plugin code can not be empty!');
		return false;
	}
	if (window.opener) {
		window.opener.CKEDITOR.tools.callFunction(fn,pcode);
		window.close();
	} else {
		alert('Editor instance was not found!');
	}
}
