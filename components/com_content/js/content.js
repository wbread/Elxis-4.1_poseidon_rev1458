/**
* @package		Elxis
* @copyright	Copyright (c) 2006-2013 elxis.org (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

/* INITIALIZE AJAX CALL */
function elxContentAjax() { 
	var xmlhttp;
	try {
		xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
	} catch(e) {
		try {
			xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		} catch(e) {
			xmlhttp = null;
		}
	}
	if (!xmlhttp && typeof XMLHttpRequest!="undefined") {
		xmlhttp = new XMLHttpRequest();
	}
	return xmlhttp;
}


/* PUBLISH COMMENT (AJAX) */
function elxPublishComment(id) {
	var toolsurl = document.getElementById('elxcontools').innerHTML;
	var rhttp = elxContentAjax();
    var rnd = Math.random();
    try {
        rhttp.open('POST', toolsurl);
        rhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        rhttp.setRequestHeader('charset', 'utf-8');
        rhttp.onreadystatechange = function () {
			if (rhttp.readyState == 4) {
				if (rhttp.status != 200) { alert('Error, please retry'); return false; }
				var rdata = new Array();
				rdata = rhttp.responseText.split('|');
				var nstat = parseInt(rdata[0]);
				if (nstat == 1) {
					var msgbox = 'elx_comment_message_'+id;
					var publink = 'elx_comment_publish_'+id;
					document.getElementById(msgbox).className  = 'elx_comment_message';
					document.getElementById(publink).style.display = 'none';
				} else {
					alert(rdata[1]);
				}
			}
		};
        rhttp.send('act=pubcomment&id='+id+'&rnd='+rnd);
    }
    catch(e){}
    finally{}
}


/* DELETE COMMENT (AJAX) */
function elxDeleteComment(id) {
	var toolsurl = document.getElementById('elxcontools').innerHTML;
	var rhttp = elxContentAjax();
    var rnd = Math.random();
    try {
        rhttp.open('POST', toolsurl);
        rhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        rhttp.setRequestHeader('charset', 'utf-8');
        rhttp.onreadystatechange = function () {
			if (rhttp.readyState == 4) {
				if (rhttp.status != 200) { alert('Error, please retry'); return false; }
				var rdata = new Array();
				rdata = rhttp.responseText.split('|');
				var nstat = parseInt(rdata[0]);
				if (nstat == 1) {
					var libox = 'elx_comment_'+id;	
					var comli = document.getElementById(libox);
					var comul = document.getElementById('elx_comments_list');
					comul.removeChild(comli);
				} else {
					alert(rdata[1]);
				}
			}
		};
        rhttp.send('act=delcomment&id='+id+'&rnd='+rnd);
    }
    catch(e){}
    finally{}
}

/* POST COMMENT (AJAX) */
function elxPostComment(isbbcode) {
	if (typeof isbbcode == 'undefined') {
		var bbcode = 0;
	} else {
		var bbcode = parseInt(isbbcode, 10);
		if (isNaN(bbcode)) {
			bbcode = 0;
		} else if (bbcode != 1) {
			bbcode = 0;
		}
	}

	var toolsurl = document.getElementById('elxcontools').innerHTML;
	var rhttp = elxContentAjax();
    var rnd = Math.random();
	var author = document.getElementById('pcomauthor').value;
	author = author.replace(/['"]/g,'');
	var email = document.getElementById('pcomemail').value;
	email = email.replace(/['"]/g,'');
	if (bbcode == 1) {
		var message = CKEDITOR.instances.pcommessage.getData();
	} else {
		var message = document.getElementById('pcommessage').value;
	}
	message = message.replace(/['"]/g,'');
	var id = parseInt(document.getElementById('pcomid').value);
	var comseccode = parseInt(document.getElementById('pcomcomseccode').value);
	var token = document.getElementById('pcomtoken').value;
    try {
        rhttp.open('POST', toolsurl);
        rhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        rhttp.setRequestHeader('charset', 'utf-8');
        rhttp.onreadystatechange = function () {
			if (rhttp.readyState == 4) {
				if (rhttp.status != 200) { alert('Error, please retry'); return false; }
				if (rhttp.responseText.indexOf('<eresponse>') > -1) { //xml response
					var response = rhttp.responseXML.documentElement;
					var rauthor = response.getElementsByTagName('author')[0].firstChild.nodeValue;
					var created = response.getElementsByTagName('created')[0].firstChild.nodeValue;
					var rcomid = parseInt(response.getElementsByTagName('comid')[0].firstChild.nodeValue);
					var dirl = response.getElementsByTagName('dirl')[0].firstChild.nodeValue;
					var dirr = response.getElementsByTagName('dirr')[0].firstChild.nodeValue;
					var avatar = response.getElementsByTagName('avatar')[0].firstChild.nodeValue;
					var message = response.getElementsByTagName('message')[0].firstChild.nodeValue;

					var ihtml = '<div style="margin:0; padding:0; text-align:center; width:68px; float:'+dirl+';">';
					ihtml += '<img src="'+avatar+'" class="elx_comment_avatar" alt="avatar" width="50" title="'+rauthor+'" border="0" />';
					ihtml += '<div class="elx_comment_actions"></div>';
					ihtml += '</div>';
					ihtml += '<div style="margin-'+dirl+':70px;">';
					ihtml += '<div>';
					ihtml += '<div class="elx_comment_author">'+rauthor+'</div>';
					ihtml += '<div class="elx_comment_date">'+created+'</div>';
					ihtml += '</div>';
					ihtml += '<div style="clear:'+dirr+';"></div>';
					ihtml += '<div class="elx_comment_message" id="elx_comment_message_'+rcomid+'">'+message+'</div>';
					ihtml += '</div>';
					ihtml += '<div style="clear:both;"></div>';

					var liObj = document.createElement('li');
					liObj.setAttribute('id', 'elx_comment_'+rcomid);
					liObj.innerHTML = ihtml;
					document.getElementById('elx_comments_list').appendChild(liObj);

					document.getElementById('pcomauthor').value = '';
					document.getElementById('pcomemail').value = '';
					document.getElementById('pcommessage').value = '';
					document.getElementById('pcomcomseccode').value = '';
					if (bbcode == 1) {
						CKEDITOR.instances.pcommessage.setData();
					}
				} else {
					var rdata = new Array();
					rdata = rhttp.responseText.split('|');
					var nstat = parseInt(rdata[0]);
					if (nstat == 1) {
						document.getElementById('pcomauthor').value = '';
						document.getElementById('pcomemail').value = '';
						document.getElementById('pcommessage').value = '';
						document.getElementById('pcomcomseccode').value = '';
						if (bbcode == 1) {
							CKEDITOR.instances.pcommessage.setData();
						}
						alert(rdata[1]);
					} else {
						alert(rdata[1]);
					}
				}
				return false;
			}
		};
        rhttp.send('act=postcomment&id='+id+'&rnd='+rnd+'&author='+author+'&email='+email+'&comseccode='+comseccode+'&token='+token+'&message='+message);
    }
    catch(e){}
    finally{}
}

/* POST COMMENT VIA BBCODE EDITOR (AJAX) */
function elxPostBBcodeComment() {
	elxPostComment(1);
}

/* 
HIGHLIGHT WORDS 
Original JavaScript code by Chirp Internet: www.chirp.com.au
*/
function textHighlight(id) {
	var targetNode = document.getElementById(id) || document.body;
	var skipTags = new RegExp("^(?:EM|SCRIPT|FORM|SPAN)$");
	var colors = ["#ff6", "#a0ffff", "#9f9", "#f99", "#f6f"];
	var wordColor = [];
	var colorIdx = 0;
	var matchRegex = "";
	this.setRegex = function(input) {
		input = input.replace(/^\\u([^\w]+|[^\w])+$/g, "").replace(/\\u([^\w'-])+/g, "|");
		matchRegex = new RegExp("(" + input + ")","i");
	}

	this.getRegex = function() {
		return matchRegex.toString().replace(/^\/\\b\(|\)\\b\/i$/g, "").replace(/\|/g, " ");
	}

	this.hiliteWords = function(node) {
		if ((typeof node == 'undefined') || !node) { return; }
		if (!matchRegex) return;
		if (skipTags.test(node.nodeName)) return;
		if (node.hasChildNodes()) {
			for(var i=0; i < node.childNodes.length; i++) { this.hiliteWords(node.childNodes[i]); }
		}
		if(node.nodeType == 3) {
			if((nv = node.nodeValue) && (regs = matchRegex.exec(nv))) {
				if(!wordColor[regs[0].toLowerCase()]) { wordColor[regs[0].toLowerCase()] = colors[colorIdx++ % colors.length]; }
				var match = document.createElement('EM');
        		match.appendChild(document.createTextNode(regs[0]));
        		match.style.backgroundColor = wordColor[regs[0].toLowerCase()];
        		match.style.fontStyle = "inherit";
        		match.style.color = "#000";
        		var after = node.splitText(regs.index);
        		after.nodeValue = after.nodeValue.substring(regs[0].length);
        		node.parentNode.insertBefore(match, after);
			}
		}
	};

	this.remove = function() {
		var arr = document.getElementsByTagName('EM');
		while(arr.length && (el = arr[0])) { el.parentNode.replaceChild(el.firstChild, el); }
	};

	this.apply = function(input) {
		if ((typeof input == 'undefined') || !input) { return; }
    	this.remove();
    	this.setRegex(input);
    	this.hiliteWords(targetNode);
	};
}
