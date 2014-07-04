/* COMPONENT Extensions Manager JS by Ioannis Sannos (datahell) */


/* MARK AN ORDERING BOX AS UNSAVED */
function markOrderUnsaved(aid) {
	aid = parseInt(aid, 10);
	if (aid < 1) { return false; }
	if (!document.getElementById('orderbox'+aid)) { return false; }
	document.getElementById('orderbox'+aid).style.backgroundColor = '#FEDDDD';
}

/* SET ARTICLE ORDER */
function setModuleOrder(aid) {
	aid = parseInt(aid, 10);
	if (aid < 1) { return false; }
	if (!document.getElementById('orderbox'+aid)) { return false; }
	var ordering = document.getElementById('orderbox'+aid).value;
	ordering = parseInt(ordering, 10);
	if (isNaN(ordering)) { return false }
	if ((ordering < 1) || (ordering > 9999999)) { return false; }
	var edata = {'id': aid, 'ordering':ordering };
	var eurl = document.getElementById('extmanagerbase').innerHTML+'modules/setorder';
	var successfunc = function(xreply) {
		var rdata = new Array();
		rdata = xreply.split('|');
		var rok = parseInt(rdata[0]);
		if (rok == 1) {
			document.getElementById('orderbox'+aid).style.backgroundColor = '#FFFFFF';
		} else {
			alert(rdata[1]);
		}
	}
	elxAjax('POST', eurl, edata, null, null, successfunc, null);
}

/* LOAD POSITION MODS FOR ORDERING */
function loadposorder() {
	var pObj = document.getElementById('emoposition');
	var position = pObj.options[pObj.selectedIndex].value;

	var edata = {'position':position };
	var eurl = document.getElementById('extmanagerbase').innerHTML+'modules/positionorder';
	var successfunc = function(xreply) {
		var jsonObj = JSON.parse(xreply);
		if (parseInt(jsonObj.error, 10) > 0) {
			if (jsonObj.errormsg != '') {
				alert(jsonObj.errormsg);
			} else {
				alert('Request failed!');
			}
			return false;
		} else {
			var oObj = document.getElementById('emoordering');
			oObj.options.length = 0;
			var len = jsonObj.modules.length;
			for (var i = 0; i < len; i++) {
				var xobj = jsonObj.modules[i];
				for (var key in xobj) {
					if (i == len - 1) { var xsel = true; } else { var xsel = false; }
					oObj.options[i] = new Option(xobj[key], key, false, xsel);
    			}
			}
		}
	}
	elxAjax('POST', eurl, edata, null, null, successfunc, null);
}

/* SWITCH ACL CATEGORY */
function switchaclcat(categ, elem) {
	var cObj = document.getElementById('aclcategory');
	var acat = cObj.options[cObj.selectedIndex].value;
	if (acat == categ) {
		document.getElementById('aclelement').value = elem;
		document.getElementById('aclelement').disabled = true;
	} else {
		document.getElementById('aclelement').value = '';
		document.getElementById('aclelement').disabled = false;
	}
}

/* SWITCH ACL TYPE */
function switchacltype() {
	var aObj = document.getElementById('acltype');
	var atype = aObj.options[aObj.selectedIndex].value;
	if (atype == 'level') {
		document.getElementById('acllevel').style.display = '';
		document.getElementById('aclgroup').style.display = 'none';
		document.getElementById('acluser').style.display = 'none';
	} else if (atype == 'group') {
		document.getElementById('acllevel').style.display = 'none';
		document.getElementById('aclgroup').style.display = '';
		document.getElementById('acluser').style.display = 'none';
	} else if (atype == 'user') {
		document.getElementById('acllevel').style.display = 'none';
		document.getElementById('aclgroup').style.display = 'none';
		document.getElementById('acluser').style.display = '';
	} else {
		return false;
	}
}

/* ADD FULL ACL RULE */
function addFullACLRule(usel) {
	var categ = document.getElementById('aclcategory').options[document.getElementById('aclcategory').selectedIndex].value;
	var elem = document.getElementById('aclelement').value;
	if (elem == '') {
		alert('You must provide an ACL element!');
		return false;
	}
	var avalue = document.getElementById('aclvalue').options[document.getElementById('aclvalue').selectedIndex].value;
	avalue = parseInt(avalue, 10);

	addACLRule(categ, elem, 0, usel, avalue);
}


/* ADD ACL RULE */
function addACLRule(categ, elem, ident, usel, avalue) {
	if (typeof avalue == 'undefined') {
		avalue = 1;
		var fullacl = false;
	} else if (avalue == -1) {
		avalue = 1;
		var fullacl = false;
	} else {
		var fullacl = true;
	}

	var aObj = document.getElementById('acltype');
	var atype = aObj.options[aObj.selectedIndex].value;

	if (atype == 'level') {
		var alevel = document.getElementById('acllevel').options[document.getElementById('acllevel').selectedIndex].value;
		alevel = parseInt(alevel, 10);
		var agroup = 0;
		var auser = 0;
		if (alevel < 0) {
			alert('Please select Access Level!');
			return false;
		}
	} else if (atype == 'group') {
		var agroup = document.getElementById('aclgroup').options[document.getElementById('aclgroup').selectedIndex].value;
		agroup = parseInt(agroup, 10);
		var alevel = -1;
		var auser = 0;
		if (agroup < 1) {
			alert('Please select Group!');
			return false;
		}
	} else if (atype == 'user') {
		if (usel == true) {
			var auser = document.getElementById('acluser').options[document.getElementById('acluser').selectedIndex].value;
		} else {
			var auser = document.getElementById('acluser').value;
		}
		auser = parseInt(auser, 10);
		if (auser < 1) {
			alert('Please select User!');
			return false;
		}
		var alevel = -1;
		var agroup = 0;
	} else {
		alert('Invalid ACL type!');
		return false;
	}

	if (fullacl == true) {
		var aaction = document.getElementById('aclaction').value;
	} else {
		var aaction = document.getElementById('aclaction').options[document.getElementById('aclaction').selectedIndex].value;
	}
	if (aaction == '') {
		alert('Select an ACL action!');
		return false;
	}

	var edata = {'id':0, 'category':categ, 'element':elem, 'identity':ident, 'action':aaction, 'minlevel':alevel, 'gid':agroup, 'uid':auser, 'aclvalue':avalue };
	var eurl = document.getElementById('userbase').innerHTML+'acl/savejson';
	var successfunc = function(xreply) {
		var jsonObj = JSON.parse(xreply);
		if (parseInt(jsonObj.error, 10) > 0) {
			if (jsonObj.errormsg != '') {
				alert(jsonObj.errormsg);
			} else {
				alert('Request failed!');
			}
			return false;
		} else {
			var tbl = document.getElementById('acllist');
			var lastRow = tbl.rows.length;
			var rowclass = 'elx_tr1';
			if (lastRow > 1) {
				if (tbl.rows[lastRow - 1].className == 'elx_tr1') { rowclass = 'elx_tr0'; }
			}

			var row = tbl.insertRow(lastRow);
			row.className = rowclass;
			row.setAttribute('id', 'aclrow'+jsonObj.id);

			var idx = 0;
			if (fullacl == true) {
				var cell7 = row.insertCell(0);
				var textNode = document.createTextNode(jsonObj.category);
				cell7.appendChild(textNode);

				var cell8 = row.insertCell(1);
				var textNode = document.createTextNode(jsonObj.elementtext);
				cell8.appendChild(textNode);
				idx = 2;
			}

			var cell1 = row.insertCell(idx);
			var textNode = document.createTextNode(jsonObj.actiontext);
			cell1.appendChild(textNode);
			idx++;

			var cell2 = row.insertCell(idx);
			cell2.className = 'elx_td_center';
			var textNode = document.createTextNode(jsonObj.minleveltext);
			cell2.appendChild(textNode);
			idx++;

			var cell3 = row.insertCell(idx);
			var textNode = document.createTextNode(jsonObj.gidtext);
			cell3.appendChild(textNode);
			idx++;

			var cell4 = row.insertCell(idx);
			var textNode = document.createTextNode(jsonObj.uidtext);
			cell4.appendChild(textNode);
			idx++;

			var cell5 = row.insertCell(idx);
			cell5.className = 'elx_td_center';
			var textNode = document.createTextNode(jsonObj.aclvalue);
			cell5.appendChild(textNode);
			idx++;

			var actionstext = '<a href="javascript:void(null);" onclick="editACLRule('+jsonObj.id+')" title="Edit">';
			actionstext += '<img src="'+jsonObj.editicon+'" alt="edit" border="0" /></a> &#160; ';
			actionstext += '<a href="javascript:void(null);" onclick="deleteACLRule('+jsonObj.id+')" title="Delete">';
			actionstext += '<img src="'+jsonObj.deleteicon+'" alt="delete" border="0" /></a>';

			var cell6 = row.insertCell(idx);
			cell6.className = 'elx_td_center';
			cell6.innerHTML = actionstext;
		}
	}
	elxAjax('POST', eurl, edata, null, null, successfunc, null);
}


/* EDIT ACL RULE */
function editACLRule(id) {
	id = parseInt(id, 10);
	var frlink = document.getElementById('userbase').innerHTML+'acl/edit.html?id='+id+'&amp;lck=1';
	$.colorbox({iframe:true, width:740, height:520, href:frlink});
}

/* DELETE ACL RULE */
function deleteACLRule(aid) {
	aid = parseInt(aid, 10);
	if (aid < 1) {
		alert('Please select an ACL rule!');
		return false;
	}

	var edata = {'ids': aid};
	var eurl = document.getElementById('userbase').innerHTML+'acl/deleteacl';
	var successfunc = function(xreply) {
		var rdata = new Array();
		rdata = xreply.split('|');
		var rok = parseInt(rdata[0], 10);
		if (rok == 1) {
			var tbl = document.getElementById('acllist');
			var rowCount = tbl.rows.length;
           	for (var i=0; i<rowCount; i++) {
               	var row = tbl.rows[i];
               	if (tbl.rows[i].id == 'aclrow'+aid) {
               		tbl.deleteRow(i);
               		break;
              	}
          	}
		} else {
			alert(rdata[1]);
		}
	}
	elxAjax('POST', eurl, edata, null, null, successfunc, null);
}

/* AJAX UPLOADER */
function uploadElxisExtension() {
	var ipackage = document.getElementById('extman_package').value;
	if (ipackage == '') {
		var msg = document.getElementById('extman_lng_nopack').innerHTML;
		alert(msg);
		return false;
	}

	var ifiletype = ipackage.substring(ipackage.lastIndexOf('.')+1, ipackage.length).toLowerCase();
	if (ifiletype != 'zip') {
		document.getElementById('extman_package').value = '';
		var msg = document.getElementById('extman_lng_mustzip').innerHTML;
		alert(msg);
		return false;
	}

	var lng_wait = document.getElementById('extman_lng_wait').innerHTML;
	document.getElementById('extm_instsub').innerHTML = lng_wait;
	document.getElementById('extman_response').innerHTML = '';
	document.getElementById('extman_response').style.display = '';
	document.getElementById('extman_loading').style.display = '';
	return true;
}

/* ELXIS INSTALLER RESPONSE */
function installerResponse() {
	var lng_upinst = document.getElementById('extman_lng_upinstall').innerHTML;
	document.getElementById('extm_instsub').innerHTML = lng_upinst;
	document.getElementById('extman_loading').style.display = 'none';
	document.getElementById('extman_package').value = '';

	var xreply = frames['extman_uptarget'].document.getElementsByTagName("body")[0].innerHTML;
	var jsonObj = JSON.parse(xreply);
	if (parseInt(jsonObj.error, 10) == 1) {
		if (jsonObj.errormsg != '') {
			document.getElementById('extman_response').innerHTML = '<span class="elx_smerror">'+jsonObj.errormsg+'</span>';
		} else {
			document.getElementById('extman_response').innerHTML = '<span class="elx_smerror">Request failed!</span>';
		}
		return false;
	} else if (parseInt(jsonObj.confirmup, 10) == 1) {
		var lng_aboutupdate = document.getElementById('extman_lng_aboutupdate').innerHTML;
		var responsetxt = lng_aboutupdate.replace(/X1/gi, jsonObj.exttype+' <strong>'+jsonObj.extension+'</strong>');
		responsetxt = responsetxt.replace(/X2/gi, '<strong>'+jsonObj.curversion+'</strong>');
		responsetxt = responsetxt.replace(/X3/gi, '<strong>'+jsonObj.version+'</strong>');
		responsetxt += '<br />';

		var len = jsonObj.warnings.length;
		for (var i = 0; i < len; i++) {
			if (i == 0) {
				var lng_syswarns = document.getElementById('extman_lng_syswarns').innerHTML;
				responsetxt += '<span class="extman_warntitle">'+lng_syswarns+'</span><br />';
			}
			var n = i + 1;
			responsetxt += '<strong>'+n+'.</strong> '+jsonObj.warnings[i]+'<br />';
		}

		var lng_continst = document.getElementById('extman_lng_continst').innerHTML;
		responsetxt += '<br /><a href="javascript:void(null);" class="extman_a" onclick="continueInstall(\'update\', \''+jsonObj.ufolder+'\')">'+lng_continst+'</a>';
		document.getElementById('extman_response').innerHTML = responsetxt;
		return false;
	} else if (parseInt(jsonObj.confirmin, 10) == 1) {
		var lng_aboutinstall = document.getElementById('extman_lng_aboutinstall').innerHTML;
		var responsetxt = lng_aboutinstall.replace(/X1/gi, jsonObj.exttype+' <strong>'+jsonObj.extension+'</strong>');
		responsetxt = responsetxt.replace(/X2/gi, '<strong>'+jsonObj.version+'</strong>');
		responsetxt += '<br />';

		var len = jsonObj.warnings.length;
		for (var i = 0; i < len; i++) {
			if (i == 0) {
				var lng_syswarns = document.getElementById('extman_lng_syswarns').innerHTML;
				responsetxt += '<span class="extman_warntitle">'+lng_syswarns+'</span><br />';
			}
			var n = i + 1;
			responsetxt += '<strong>'+n+'.</strong> '+jsonObj.warnings[i]+'<br />';
		}

		var lng_continst = document.getElementById('extman_lng_continst').innerHTML;
		responsetxt += '<br /><a href="javascript:void(null);" class="extman_a" onclick="continueInstall(\'install\', \''+jsonObj.ufolder+'\')">'+lng_continst+'</a>';
		document.getElementById('extman_response').innerHTML = responsetxt;
		return false;
	} else {
		var lng_insuccess = document.getElementById('extman_lng_insuccess').innerHTML;
		var responsetxt = lng_insuccess.replace(/X1/gi, jsonObj.exttype+' <strong>'+jsonObj.extension+'</strong>');
		responsetxt = responsetxt.replace(/X2/gi, '<strong>'+jsonObj.version+'</strong>');
		responsetxt += '<br />';

		var len = jsonObj.warnings.length;
		for (var i = 0; i < len; i++) {
			if (i == 0) {
				var lng_syswarns = document.getElementById('extman_lng_syswarns').innerHTML;
				responsetxt += '<span class="extman_warntitle">'+lng_syswarns+'</span><br />';
			}
			var n = i + 1;
			responsetxt += '<strong>'+n+'.</strong> '+jsonObj.warnings[i]+'<br />';
		}

		document.getElementById('extman_response').innerHTML = responsetxt;
		return false;
	}
}

/* CONTINUE INSTALL OR UPDATE */
function continueInstall(ctask, ufolder) {
	var lng_wait = document.getElementById('extman_lng_wait').innerHTML;
	document.getElementById('extman_response').innerHTML = '';
	document.getElementById('extman_response').style.display = '';
	document.getElementById('extman_loading').style.display = '';

	var edata = {'ufolder': ufolder};
	if (ctask == 'update') {
		var eurl = document.getElementById('extman_baseurl').innerHTML+'install/cupdate';
	} else {
		var eurl = document.getElementById('extman_baseurl').innerHTML+'install/cinstall';
	}
	var successfunc = function(xreply) {
		var jsonObj = JSON.parse(xreply);
		document.getElementById('extman_loading').style.display = 'none';
		if (parseInt(jsonObj.error, 10) == 1) {
			if (jsonObj.errormsg != '') {
				document.getElementById('extman_response').innerHTML = '<span class="elx_smerror">'+jsonObj.errormsg+'</span>';
			} else {
				document.getElementById('extman_response').innerHTML = '<span class="elx_smerror">Request failed!</span>';
			}
		} else {
			var lng_insuccess = document.getElementById('extman_lng_insuccess').innerHTML;
			var responsetxt = lng_insuccess.replace(/X1/gi, jsonObj.exttype+' <strong>'+jsonObj.extension+'</strong>');
			responsetxt = responsetxt.replace(/X2/gi, '<strong>'+jsonObj.version+'</strong>');
			responsetxt += '<br />';

			var len = jsonObj.warnings.length;
			for (var i = 0; i < len; i++) {
				if (i == 0) {
					var lng_syswarns = document.getElementById('extman_lng_syswarns').innerHTML;
					responsetxt += '<span class="extman_warntitle">'+lng_syswarns+'</span><br />';
				}
				var n = i + 1;
				responsetxt += '<strong>'+n+'.</strong> '+jsonObj.warnings[i]+'<br />';
			}
			document.getElementById('extman_response').innerHTML = responsetxt;
		}
	}
	elxAjax('POST', eurl, edata, null, null, successfunc, null);
}

/* AJAX SYNCHRONIZER */
function syncElxisExtension() {
	var sObj = document.getElementById('extman_extension');
	var sextension = sObj.options[sObj.selectedIndex].value;
	if (sextension == '') {
		var msg = document.getElementById('extman_lng_noext').innerHTML;
		document.getElementById('extman_response').innerHTML = '<span class="elx_smerror">'+msg+'</span>';
		document.getElementById('extman_response').style.display = '';
		return false;
	}

	var tkn = document.getElementById('extman_token').value;
	var lng_wait = document.getElementById('extman_lng_wait').innerHTML;
	var lng_synchro = document.getElementById('extman_lng_synchronize').innerHTML;
	document.getElementById('extm_syncsub').innerHTML = lng_wait;
	document.getElementById('extman_response').innerHTML = '';
	document.getElementById('extman_response').style.display = '';
	document.getElementById('extman_loading').style.display = '';

	var edata = { 'extension':sextension, 'token':tkn };
	var eurl = document.getElementById('extman_syncurl').innerHTML;
	var successfunc = function(xreply) {
		document.getElementById('extman_loading').style.display = 'none';
		document.getElementById('extman_extension').selectedIndex = 0;
		document.getElementById('extm_syncsub').innerHTML = lng_synchro;
		var jsonObj = JSON.parse(xreply);
		if (parseInt(jsonObj.error, 10) > 0) {
			if (jsonObj.errormsg != '') {
				document.getElementById('extman_response').innerHTML = '<span class="elx_smerror">'+jsonObj.errormsg+'</span>';
			} else {
				document.getElementById('extman_response').innerHTML = '<span class="elx_smerror">Request failed 1!</span>';
			}
		} else {
			var lng_synsuccess = document.getElementById('extman_lng_synsuccess').innerHTML;
			var responsetxt = lng_synsuccess.replace(/X1/gi, jsonObj.exttype+' <strong>'+jsonObj.extension+'</strong>');
			responsetxt = responsetxt.replace(/X2/gi, '<strong>'+jsonObj.version+'</strong>');
			document.getElementById('extman_response').innerHTML = responsetxt;
		}
	}

	var errorfunc = function(req, sts, err) {
		document.getElementById('extman_loading').style.display = 'none';
		document.getElementById('extman_extension').selectedIndex = 0;
		document.getElementById('extm_syncsub').innerHTML = lng_synchro;
		document.getElementById('extman_response').innerHTML = '<span class="elx_smerror">'+err+'</span>';
	}
	elxAjax('POST', eurl, edata, null, null, successfunc, errorfunc);
}
