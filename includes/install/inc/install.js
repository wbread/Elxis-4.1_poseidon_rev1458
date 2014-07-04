/**
Package			Elxis CMS
Subpackage		Installer / JS
Author			Elxis Team ( http://www.elxis.org )
Copyright		(c) 2006-2012 Elxis Team (http://www.elxis.org). All rights reserved.
License			Elxis Public License ( http://www.elxis.org/elxis-public-license.html
Date			2012-08-25 20:02:10
Description 	Javascript for Elxis installer
*/

function eiFocusLang(lname, lng) {
	if (lname != '') {
		document.getElementById('ei_curlang').innerHTML = lname;
		document.getElementById('eiflag_'+lng).style.opacity = '1.0';
	}
}

function eiFadeLang(lname, lng, dlng) {
	if (lname != '') {
		document.getElementById('ei_curlang').innerHTML = lname;
		document.getElementById('eiflag_'+lng).style.opacity = '0.6';
		document.getElementById('eiflag_'+dlng).style.opacity = '1.0';
	}
}

function eiAgreeTerms() {
	var aObj = document.getElementById('gotostep3a');
	if (document.getElementById('licagree').checked == 1) {
		aObj.href = document.getElementById('gotostep3u').innerHTML.replace('&amp;', '&'); 
		aObj.className = 'ei_abutton';
	} else {
		aObj.href = 'javascript:void(null);';
		aObj.className = 'ei_abutton_off';
	}
}

function eiMakeKey() {
	var chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz';
	var seckey = '';
	for (var i=0; i<16; i++) {
		var rnum = Math.floor(Math.random() * chars.length);
		seckey += chars.substring(rnum,rnum+1);
	}
	document.getElementById('cfg_encrypt_key').value = seckey;
}

function eiToggleFTP(useftp) {
	useftp = parseInt(useftp, 10);
	if (useftp == 1) {
		document.getElementById('ftp_details').style.visibility = 'visible';
		document.getElementById('ftp_details').style.display = '';
	} else {
		document.getElementById('ftp_details').style.visibility = 'hidden';
		document.getElementById('ftp_details').style.display = 'none';
	}
}

function eiCheckFTP() {
	var eurl = document.getElementById('ei_baseurl').innerHTML+'/includes/install/inc/tools.php';
	var etype = 'POST';

	document.getElementById('ftpresponse').className = 'elx_sminfo';
	document.getElementById('ftpresponse').innerHTML = 'Please wait...';
	document.getElementById('ftpresponse').style.display = '';

	var successfunc = function(xreply) {
    	try {
        	var jsonObj = JSON.parse(xreply);
    	} catch (e) {
    		document.getElementById('ftpresponse').className = 'elx_smerror';
    		document.getElementById('ftpresponse').innerHTML = 'Could not complete your request.';
        	return false;
    	}

		if (parseInt(jsonObj.success, 10) == 1) {
			if (jsonObj.message != '') {
    			document.getElementById('ftpresponse').className = 'elx_smwarning';
				document.getElementById('ftpresponse').innerHTML = jsonObj.message;
			} else {
    			document.getElementById('ftpresponse').className = 'elx_smsuccess';
				document.getElementById('ftpresponse').innerHTML = 'The FTP settings are correct.';
			}
		} else {
    		document.getElementById('ftpresponse').className = 'elx_smerror';
			document.getElementById('ftpresponse').innerHTML = jsonObj.message;
		}
	}

	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		document.getElementById('ftpresponse').className = 'elx_smerror';
		document.getElementById('ftpresponse').innerHTML = errorThrown;
	}

	var rnd = Math.floor((Math.random()*100)+1);
	var edata = {
		'rnd': rnd,
		'action': 'checkftp',
		'fho': document.getElementById('cfg_ftp_host').value,
		'fpo': parseInt(document.getElementById('cfg_ftp_port').value, 10),
		'fus': document.getElementById('cfg_ftp_user').value,
		'fpa': document.getElementById('cfg_ftp_pass').value,
		'fro': document.getElementById('cfg_ftp_root').value
	};

	elxAjax(etype, eurl, edata, 'ftpresponse', '', successfunc, errorfunc);
}


function eiCheckDB() {
	var eurl = document.getElementById('ei_baseurl').innerHTML+'/includes/install/inc/tools.php';
	var etype = 'POST';

	document.getElementById('dbresponse').className = 'elx_sminfo';
	document.getElementById('dbresponse').innerHTML = 'Please wait...';
	document.getElementById('dbresponse').style.display = '';

	var successfunc = function(xreply) {
		if (xreply == 'OK') {
    		document.getElementById('dbresponse').className = 'elx_smsuccess';
			document.getElementById('dbresponse').innerHTML = 'The database settings are correct.';
		} else {
			var rmsg = 'Could not connect to database!';
			if (xreply.substr(0,4) == 'msg:') { rmsg = xreply.substr(4); }
    		document.getElementById('dbresponse').className = 'elx_smerror';
			document.getElementById('dbresponse').innerHTML = rmsg;
		}
	}

	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		document.getElementById('dbresponse').className = 'elx_smerror';
		document.getElementById('dbresponse').innerHTML = errorThrown;
	}

	var dbtObj = document.getElementById('cfg_db_type');
    var dbtype = dbtObj.options[dbtObj.selectedIndex].value;
	var rnd = Math.floor((Math.random()*100)+1);

	var edata = {
		'rnd': rnd,
		'action': 'checkdb',
		'dty': dbtype,
		'dho': document.getElementById('cfg_db_host').value,
		'dpo': parseInt(document.getElementById('cfg_db_port').value, 10),
		'dna': document.getElementById('cfg_db_name').value,
		'dpr': document.getElementById('cfg_db_prefix').value,
		'dus': document.getElementById('cfg_db_user').value,
		'dpa': document.getElementById('cfg_db_pass').value,
		'dds': document.getElementById('cfg_db_dsn').value,
		'dsc': document.getElementById('cfg_db_scheme').value
	};

	elxAjax(etype, eurl, edata, 'dbresponse', '', successfunc, errorfunc);
}


function eiTrim(str) {
	return str.replace(/^\s+|\s+$/g,'');
}


function eiValidateConfig() {
	var sname = eiTrim(document.getElementById('cfg_sitename').value);
	if (sname == '') {
		alert('Site name can not be empty!');
		elxFocus('cfg_sitename');
		return false;
	}

	var surl = eiTrim(document.getElementById('cfg_url').value);
	if (surl == '') {
		alert('Site URL can not be empty!');
		elxFocus('cfg_url');
		return false;
	}

	var pattern = /(http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
	if (!pattern.test(surl)) {
		alert('Provided site URL is invalid!');
		elxFocus('cfg_url');
		return false;
	}

	var ekey = eiTrim(document.getElementById('cfg_encrypt_key').value);
	if (ekey == '') {
		alert('Encryption key can not be empty!');
		elxFocus('cfg_encrypt_key');
		return false;
	}
	if (ekey.length != 16) {
		alert('The encryption key should have 16 latin alphanumeric characters!');
		elxFocus('cfg_encrypt_key');
		return false;
	}

	var dbtObj = document.getElementById('cfg_db_type');
    var dbtype = dbtObj.options[dbtObj.selectedIndex].value;
	if (dbtype == '') {
		alert('You must select a database type!');
		elxFocus('cfg_db_type');
		return false;
	}

	var dbhost = eiTrim(document.getElementById('cfg_db_host').value);
	if (dbhost == '') {
		alert('Database host can not be empty!');
		elxFocus('cfg_db_host');
		return false;
	}

	var dbname = eiTrim(document.getElementById('cfg_db_name').value);
	if (dbname == '') {
		alert('Database name can not be empty!');
		elxFocus('cfg_db_name');
		return false;
	}

	var dbprefix = eiTrim(document.getElementById('cfg_db_prefix').value);
	if (dbprefix == '') {
		alert('Database tables prefix can not be empty!');
		elxFocus('cfg_db_prefix');
		return false;
	}

	var dbscheme = eiTrim(document.getElementById('cfg_db_scheme').value);
	var dbdsn = eiTrim(document.getElementById('cfg_db_dsn').value);

	if (((dbtype == 'sqlite') || (dbtype == 'sqlite2')) && (dbscheme == '')) {
		alert('A schema file is required for '+dbtype);
		elxFocus('cfg_db_scheme');
		return false;
	}

	if ((dbdsn == '') && (dbscheme == '')) {
		var dbuser = eiTrim(document.getElementById('cfg_db_user').value);
		var dbpass = eiTrim(document.getElementById('cfg_db_pass').value);
		if (dbuser == '') {
			alert('You must provide a database username!');
			elxFocus('cfg_db_user');
			return false;
		}
		if (dbpass == '') {
			alert('You must provide a database password!');
			elxFocus('cfg_db_pass');
			return false;
		}
	}

	if (document.getElementById('cfg_ftp1').checked) {
		var fhost = eiTrim(document.getElementById('cfg_ftp_host').value);
		if (fhost == '') {
			alert('FTP host can not be empty!');
			elxFocus('cfg_ftp_host');
			return false;
		}

		var fport = eiTrim(document.getElementById('cfg_ftp_port').value);
		fport = parseInt(fport, 10);
		if (fport < 1) { document.getElementById('cfg_ftp_port').value = 21; }

		var froot = eiTrim(document.getElementById('cfg_ftp_root').value);
		if (froot == '') { froot = '/'; }

		var fuser = eiTrim(document.getElementById('cfg_ftp_user').value);
		if (fuser == '') {
			alert('You must provide an FTP username!');
			elxFocus('cfg_ftp_user');
			return false;
		}
		var fpass = eiTrim(document.getElementById('cfg_ftp_pass').value);
		if (fpass == '') {
			alert('You must provide an FTP password!');
			elxFocus('cfg_ftp_pass');
			return false;
		}
	}

	return true;
}


function eiMakeUname() {
	var eurl = document.getElementById('ei_baseurl').innerHTML+'/includes/install/inc/tools.php';
	var etype = 'POST';
	var curname = eiTrim(document.getElementById('u_uname').value);
	var curlang = document.getElementById('langfamily').value;

	var successfunc = function(xreply) {
    	try {
        	var jsonObj = JSON.parse(xreply);
    	} catch (e) {
    		alert('Elxis could not complete your request due to an error.');
        	return false;
    	}

		if (parseInt(jsonObj.success, 10) == 1) {
			if (jsonObj.uname != '') {
    			document.getElementById('u_uname').value = jsonObj.uname;
			} else {
    			alert('Request failed! Elxis can not propose a username.');
			}
		} else {
			if (jsonObj.message != '') {
				alert(jsonObj.message);
			} else {
				alert('Elxis could not complete your request due to an error.');
			}
		}
	}

	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) { alert(errorThrown); }

	var rnd = Math.floor((Math.random()*100)+1);
	var edata = {
		'rnd': rnd,
		'action': 'makeuname',
		'curname': curname,
		'curlang': curlang
	};

	elxAjax(etype, eurl, edata, '', '', successfunc, errorfunc);
}


function eiValidateUser() {
	var ufname = eiTrim(document.getElementById('u_firstname').value);
	if (ufname == '') {
		alert('Please fill in your first name!');
		elxFocus('u_firstname');
		return false;
	}
	if (ufname.length < 3) {
		alert('Your first name is too short!');
		elxFocus('u_firstname');
		return false;
	}

	var ulname = eiTrim(document.getElementById('u_lastname').value);
	if (ulname == '') {
		alert('Please fill in your last name!');
		elxFocus('u_lastname');
		return false;
	}
	if (ulname.length < 3) {
		alert('Your last name is too short!');
		elxFocus('u_lastname');
		return false;
	}

	var uemail = eiTrim(document.getElementById('u_email').value);
	if (uemail == '') {
		alert('Please fill in your email!');
		elxFocus('u_email');
		return false;
	}

	if (elxValidateEmail(uemail, false) == false) {
		alert('Please fill in a valid email address!');
		elxFocus('u_email');
		return false;
	}

	var uuname = eiTrim(document.getElementById('u_uname').value);
	var upword = eiTrim(document.getElementById('u_pword').value);
	var upword2 = eiTrim(document.getElementById('u_pword2').value);
	if (uuname == '') {
		alert('Please provide a username!');
		elxFocus('u_uname');
		return false;
	}
	if (upword == '') {
		alert('Please provide a password!');
		elxFocus('u_pword');
		return false;
	}
	if (upword2 == '') {
		alert('Please confirm the password!');
		elxFocus('u_pword2');
		return false;
	}
	var regex = /^[0-9A-Za-z_]+$/;
	if (!regex.test(uuname)){
		alert('Username may contain only latin alphanumeric characters and underscore!');
		elxFocus('u_uname');
		return false;
	}
	if (uuname.length < 4) {
		alert('Username is too short!');
		elxFocus('u_uname');
		return false;
	}
	if (!regex.test(upword)){
		alert('Password may contain only latin alphanumeric characters and underscore!');
		elxFocus('u_pword');
		return false;
	}
	if (upword.length < 8) {
		alert('Password should be at least 8 characters long!');
		elxFocus('u_pword');
		return false;
	}
	if (upword != upword2) {
		alert('Passwords do not match!');
		elxFocus('u_pword2');
		return false;
	}
	return true;
}


function eiFocusElxis(isact) {
	if (isact == 1) {
		var img = document.getElementById('elxisbaseurlx').innerHTML+'/includes/install/css/elxis_footer_on.png';
	} else {
		var img = document.getElementById('elxisbaseurlx').innerHTML+'/includes/install/css/elxis_footer_off.png';
		
	}
	document.getElementById('elxisfooterlogo').src = img;
}
