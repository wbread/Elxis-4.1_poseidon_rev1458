/**
* @package		Elxis / Component Content
* @copyright	Copyright (c) 2006-2012 elxis.org (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Ioannis Sannos ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

/* INIT SORTABLE */
$(function() {
	$('.fpboxes, .lay100, .lay160, .lay240, .lay320').sortable({
		connectWith: '.fpboxes, .lay100, .lay160, .lay240, .lay320',
		cursor: 'move',
		opacity:0.6,
		delay: 200,
		tolerance: 'pointer', 
		placeholder: 'layholder',
		forcePlaceholderSize: true,
		stop: function(event, ui) {
			var sortorder='';
			$('.lay100, .lay160, .lay240, .lay320').each(function() { /* dont save "fpboxes" */
				var itemorder = $(this).sortable('toArray');
				var columnId = $(this).attr('id');
				sortorder += columnId+'='+itemorder.toString()+'!';
			});
			document.getElementById('fp_saves').innerHTML = sortorder;
		}
	}).disableSelection();

	applyWidth();
});


/* CALCULATE COLUMNS WIDTH */
function fpcalculateCols(col) {
	var lObj = document.getElementById('fpwleft');
	var cObj = document.getElementById('fpwcenter');
	var rObj = document.getElementById('fpwright');
	var wl = lObj.options[lObj.selectedIndex].value;
	var wc = cObj.options[cObj.selectedIndex].value;
	var wr = rObj.options[rObj.selectedIndex].value;
	wl = parseInt(wl, 10);
	wc = parseInt(wc, 10);
	wr = parseInt(wr, 10);

	if (col == 1) {
		if (wl == 100) {
			var nc = 0;
			var nr = 0;
		} else if (wr == 0) {
			var nr = 0;
			var nc = 100 - wl;
			if (nc < 0) { nc = 0; }
		} else if (wc == 0) {
			var nc = 0;
			var nr = 100 - wl;
			if ( nr < 0) { nr = 0; }
		} else {
			var nr = ((100 - wl) * wr) / (wc + wr);
			nr = Math.round(nr);
			var nc = (nr * wc) / wr;
			nc = Math.round(nc);
			var modus = nc % 5;
			if (modus > 0) { nc = nc + 5 - modus; }
			nr = 100 - wl - nc;
		}

		for(i=0;i < cObj.length; i++) {
			if (cObj.options[i].value == nc) { cObj.selectedIndex = i; break; }
		}

		for(i=0;i < rObj.length; i++) {
			if (rObj.options[i].value == nr) { rObj.selectedIndex = i; break; }
		}
	} else if (col == 2) {
		var nr = 100 - (wl + wc);
		if (nr < 0) { nr = 0; }
		for(i=0;i < rObj.length; i++) {
			if (rObj.options[i].value == nr) {
				rObj.selectedIndex = i;
				break;
			}
		}
	}
}


/* CHANGE COLUMNS WIDTH */
function applyWidth() {
	var wl = document.getElementById('fpwleft').options[document.getElementById('fpwleft').selectedIndex].value;
	var wc = document.getElementById('fpwcenter').options[document.getElementById('fpwcenter').selectedIndex].value;
	var wr = document.getElementById('fpwright').options[document.getElementById('fpwright').selectedIndex].value;
	wl = parseInt(wl, 10);
	wc = parseInt(wc, 10);
	wr = parseInt(wr, 10);

	if (wl + wc + wr != 100) {
		var alertmsg = document.getElementById('fp_lng_w100').innerHTML;
		alert(alertmsg);
		return false;
	}

	if (wl == 0) {
		document.getElementById('fpleftcol').style.display = 'none';
	} else {
		var w = wl * 8;
		document.getElementById('fpleftcol').style.display = '';
		document.getElementById('fpleftcol').style.width = w+'px';
	}

	if (wc == 0) {
		document.getElementById('fpmidcol').style.display = 'none';
	} else {
		var w = wc * 8;
		document.getElementById('fpmidcol').style.display = '';
		document.getElementById('fpmidcol').style.width = w+'px';

		var w1 = ((2 * w) / 3) - 10;
		w1 =  parseInt(w1, 10);
		$('.lay320').each(function() {
			$(this).width(w1);
		});

		var w2 = (w / 2) - 10;
		w2 =  parseInt(w2, 10);
		$('.lay240').each(function() {
			$(this).width(w2);
		});

		var w3 = (w / 3) - 10;
		w3 =  parseInt(w3, 10);
		$('.lay160').each(function(){
			$(this).width(w3);
		});
	}

	if (wr == 0) {
		document.getElementById('fprightcol').style.display = 'none';
	} else {
		var w = wr * 8;
		document.getElementById('fprightcol').style.display = '';
		document.getElementById('fprightcol').style.width = w+'px';
	}
}


/* SAVE LAYOUT */
function saveLayout() {
	var wl = document.getElementById('fpwleft').options[document.getElementById('fpwleft').selectedIndex].value;
	var wc = document.getElementById('fpwcenter').options[document.getElementById('fpwcenter').selectedIndex].value;
	var wr = document.getElementById('fpwright').options[document.getElementById('fpwright').selectedIndex].value;
	wl = parseInt(wl, 10);
	wc = parseInt(wc, 10);
	wr = parseInt(wr, 10);

	if (wl + wc + wr != 100) {
		var alertmsg = document.getElementById('fp_lng_w100').innerHTML;
		alert(alertmsg);
		return false;
	}

	var edata = { 'wl': wl, 'wc': wc, 'wr': wr };
	var cellstxt = document.getElementById('fp_saves').innerHTML;
	var parts = cellstxt.split('!');
	for (i=0; i < parts.length; i++) {
		if (parts[i] == '') { continue; }
		var vals = parts[i].split('=');
		var name = vals[0].replace('lay', 'c');
		edata[name] = vals[1];
	}
	var eurl = document.getElementById('fp_saveurl').innerHTML;

	var msgObj = document.getElementById('fp_message');
	msgObj.innerHTML = document.getElementById('fp_lng_wait').innerHTML;
	msgObj.className = 'fpmessage';
	msgObj.style.display = 'block';

	var successfunc = function(xreply) {
		var rdata = new Array();
		rdata = xreply.split('|');
		var rok = parseInt(rdata[0]);
		if (rok == 1) {
			msgObj.innerHTML = rdata[1];
			msgObj.className = 'fpmessage-suc';
		} else {
			msgObj.innerHTML = rdata[1];
			msgObj.className = 'fpmessage-err';
		}
	}
	elxAjax('POST', eurl, edata, null, null, successfunc, null);					
}
