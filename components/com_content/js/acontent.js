/* Component Content javascript */
/* Elxis CMS - http://www.elxis.org */
/* Ioannis Sannos (datahell) */
 
 
/* SUGGEST SEO TITLE FOR CATEGORY OR CONTENT ITEM */
function suggestContentSEO(titleid, seotitleid, updateid, contentbase, ctype) {
    var title = document.getElementById(titleid).value;
	if (title == '') {
		var titleempty = document.getElementById('lng_titleempty').innerHTML;
		document.getElementById(updateid).innerHTML = '<span style="color:#CC0000;">'+titleempty+'</span>';
		document.getElementById(updateid).style.display = 'block';
		return false;
	}

	if (ctype == 'category') {
		var eurl = contentbase+'categories/suggest';
		var catid = document.getElementById('ectcatid').value;
		catid = parseInt(catid, 10);
		var edata = { 'catid': catid, 'title': title };
	} else { //article
		var eurl = contentbase+'articles/suggest';
		var id = document.getElementById('earid').value;
		id = parseInt(id, 10);
		var edata = { 'id': id, 'title': title };
	}

	var successfunc = function(data) {
		var update = new Array();
		update = data.split('|');
		var ok = parseInt(update[0], 10);
		if (ok == 1) {
			document.getElementById(updateid).style.display = 'none';
			document.getElementById(updateid).innerHTML = '';
			document.getElementById(seotitleid).value = update[1];
		} else {
			document.getElementById(updateid).style.display = 'block';
			if (typeof update[1] == 'undefined') {
				var errormsg = 'Request failed!';
			} else {
				var errormsg = update[1];
			}
			document.getElementById(updateid).innerHTML = '<span style="color:#CC0000;">'+errormsg+'</span>';			
		}
	};

	var loading = document.getElementById('lng_wait').innerHTML;
	elxAjax('POST', eurl, edata, updateid, loading, successfunc, null);
}

/* VALIDATE CATEGORY'S OR ARTICLE'S SEO TITLE */
function validateContentSEO(seotitleid, updateid, contentbase, ctype) {
	var seotitle = document.getElementById(seotitleid).value;
	if (ctype == 'category') {
		var catid = document.getElementById('ectcatid').value;
		catid = parseInt(catid, 10);
		var eurl = contentbase+'categories/validate';
		var edata = { 'catid': catid, 'seotitle': seotitle };
	} else {
		var id = document.getElementById('earid').value;
		id = parseInt(id, 10);
		var eurl = contentbase+'articles/validate';
		var edata = { 'id': id, 'seotitle': seotitle };
	}

	var successfunc = function(data) {
		var update = new Array();
		update = data.split('|');
		var ok = parseInt(update[0], 10);
		if (typeof update[1] == 'undefined') {
			var responsemsg = 'Request failed!';
		} else {
			var responsemsg = update[1];
		}
		document.getElementById(updateid).style.display = 'block';
		if (ok == 1) {
			document.getElementById(updateid).innerHTML = '<span style="color:#008000;">'+responsemsg+'</span>';
		} else {
			document.getElementById(updateid).innerHTML = '<span style="color:#CC0000;">'+responsemsg+'</span>';			
		}
	};

	var loading = document.getElementById('lng_wait').innerHTML;
	elxAjax('POST', eurl, edata, updateid, loading, successfunc, null);
}

/* MARK AN ORDERING BOX AS UNSAVED */
function markOrderUnsaved(aid) {
	aid = parseInt(aid, 10);
	if (aid < 1) { return false; }
	if (!document.getElementById('orderbox'+aid)) { return false; }
	document.getElementById('orderbox'+aid).style.backgroundColor = '#FEDDDD';
}

/* SET ARTICLE ORDER */
function setArticleOrder(aid) {
	aid = parseInt(aid, 10);
	if (aid < 1) { return false; }
	if (!document.getElementById('orderbox'+aid)) { return false; }
	var ordering = document.getElementById('orderbox'+aid).value;
	ordering = parseInt(ordering, 10);
	if (isNaN(ordering)) { return false }
	if ((ordering < 1) || (ordering > 9999999)) { return false; }
	var edata = {'id': aid, 'ordering':ordering };
	var eurl = document.getElementById('acontentbase').innerHTML+'articles/setorder';
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

/* DELETE COMMENT (AJAX) */
function deleteComment(cid) {
	cid = parseInt(cid, 10);
	if (isNaN(cid)) { return false }
	if (cid < 1) { return false; }
	var edata = {'id': cid };
	var eurl = document.getElementById('acontentbase').innerHTML+'articles/deletecomment';
	var successfunc = function(xreply) {
		var rdata = new Array();
		rdata = xreply.split('|');
		var rok = parseInt(rdata[0]);
		if (rok == 1) {
			var i = document.getElementById('comment_row'+cid).rowIndex;
			document.getElementById('comments_table').deleteRow(i);
		} else {
			document.getElementById('delicon'+cid).src = document.getElementById('acontentwarnicon').innerHTML;
			alert(rdata[1]);
		}
	}

	document.getElementById('delicon'+cid).src = document.getElementById('acontentloadicon').innerHTML;
	elxAjax('POST', eurl, edata, null, null, successfunc, null);
}

/* PUBLISH COMMENT (AJAX) */
function publishComment(cid) {
	cid = parseInt(cid, 10);
	if (isNaN(cid)) { return false }
	if (cid < 1) { return false; }
	var edata = {'id': cid };
	var eurl = document.getElementById('acontentbase').innerHTML+'articles/publishcomment';
	var successfunc = function(xreply) {
		var rdata = new Array();
		rdata = xreply.split('|');
		var rok = parseInt(rdata[0]);
		if (rok == 1) {
			document.getElementById('pubicon'+cid).src = document.getElementById('acontentpubicon').innerHTML;
		} else {
			document.getElementById('pubicon'+cid).src = document.getElementById('acontentwarnicon').innerHTML;
			alert(rdata[1]);
		}
	}

	document.getElementById('pubicon'+cid).src = document.getElementById('acontentloadicon').innerHTML;
	elxAjax('POST', eurl, edata, null, null, successfunc, null);
}
