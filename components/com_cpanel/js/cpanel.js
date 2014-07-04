/* Component CPANEL javascript */
/* Elxis CMS - http://www.elxis.org */

function cpCheckFTP() {
	var eurl = document.getElementById('cfgajurl').value;
	var etype= 'POST';
	var eloadelement = 'ftpresponse';
	var eloadtext = document.getElementById('cfgajwait').value;
	document.getElementById(eloadelement).className = 'elx_info';
	var successfunc = function(data) {
		document.getElementById(eloadelement).style.display = '';
        var update = new Array();
        update = data.split('|');
        if (update[1]== '1') {
			document.getElementById(eloadelement).className = 'elx_notice';
            document.getElementById(eloadelement).innerHTML = update[2];
		} else {
		  document.getElementById(eloadelement).className = 'elx_error';
		  document.getElementById(eloadelement).innerHTML = data;
		}
	};
	var errorfunc;
	var edata = {
		'fho': document.getElementById('cfgftp_host').value,
		'fpo': parseInt(document.getElementById('cfgftp_port').value),
		'fus': document.getElementById('cfgftp_user').value,
		'fpa': document.getElementById('cfgftp_pass').value,
		'fro': document.getElementById('cfgftp_root').value
	};

	elxAjax(etype, eurl, edata, eloadelement, eloadtext, successfunc, errorfunc);
}

/* EDIT ROUTE */
function editroute(rtype, rbase) {
	var curl = document.getElementById('routebaseurl').innerHTML;
	curl += 'edit.html?rtype='+rtype+'&rbase='+rbase;
	$.colorbox({top:'160px', width:'600px', height:'350px', href:curl, iframe:true});
}

/* ADD ROUTE */
function addroute(task, grid) { editroute('new', ''); }

/* DELETE ROUTE */
function deleteroute(rtype, rbase) {
	var eurl = document.getElementById('routebaseurl').innerHTML+'delete';
	var successfunc = function(data) {
		var update = new Array();
		update = data.split('|');
		if (update[0] == '0') {
			alert(update[1]);
		} else {
			if (typeof jQuery != 'undefined') { $("#routes").flexReload(); }
		}
	};
	var errorfunc;
	var edata = { 'rtype': rtype, 'rbase': rbase };
	elxAjax('POST', eurl, edata, null, null, successfunc, errorfunc);
}

/* CPANEL STATISTICS */
function cpstats_setyear(diff) {
	diff = parseInt(diff, 10);
	var yObj = document.getElementById('cpstats_year');
	var yr = yObj.innerHTML;
	yr = parseInt(yr, 10);
	var newyear = yr + diff;
	if (newyear < 2011) { return false; }
	var dat = new Date();
	var curyear = dat.getFullYear(); 
	if (newyear > curyear) { return false; }
	yObj.innerHTML = newyear;
}
function cpshowstats(mn) {
	var yObj = document.getElementById('cpstats_year');
	var yr = yObj.innerHTML;
	yr = parseInt(yr, 10);
	var dtlink = document.getElementById('cpstats_link').innerHTML;
	if (mn == 0) {
		dtlink = dtlink+'?dt='+yr;
	} else {
		dtlink = dtlink+'?dt='+yr+''+mn;
	}
	window.location.replace(dtlink);
}
