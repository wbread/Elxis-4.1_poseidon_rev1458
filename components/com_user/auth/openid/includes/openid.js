/**
@package: Elxis CMS
@subpackage: Authentication / OpenId
@author: Ioannis Sannos (Elxis Team)
@date: 2012-03-14 21:34:00
*/

var prObj = {"providers": [
	{'oname': 'openid', 'otitle': 'OpenId', 'userinput': 1, 'ourl': 'http://{uname}'},
	{'oname': 'aol',  'otitle': 'AOL',  'userinput': 1, 'ourl': 'http://openid.aol.com/{uname}'},
	{'oname': 'google',  'otitle': 'Google',  'userinput': 0, 'ourl': 'https://www.google.com/accounts/o8/id'},
	{'oname': 'yahoo',  'otitle': 'Yahoo',  'userinput': 0, 'ourl': 'http://yahoo.com/'},
	{'oname': 'flickr',  'otitle': 'Flickr',  'userinput': 1, 'ourl': 'http://www.flickr.com/{uname}/'},
	{'oname': 'myspace',  'otitle': 'My Space',  'userinput': 1, 'ourl': 'http://www.myspace.com/{uname}'},
	{'oname': 'blogger',  'otitle': 'Blogger',  'userinput': 1, 'ourl': 'http://{uname}.blogspot.com/'},
	{'oname': 'technorati',  'otitle': 'Technorati',  'userinput': 1, 'ourl': 'http://technorati.com/people/technorati/{uname}/'},
	{'oname': 'livejournal',  'otitle': 'Live Journal',  'userinput': 1, 'ourl': 'http://{uname}.livejournal.com/'},
	{'oname': 'verisign',  'otitle': 'Verisign',  'userinput': 1, 'ourl': 'http://{uname}.pip.verisignlabs.com/'},
	{'oname': 'wordpress',  'otitle': 'Wordpress',  'userinput': 1, 'ourl': 'http://{uname}.wordpress.com'},
	{'oname': 'myopenid',  'otitle': 'MyOpenId',  'userinput': 1, 'ourl': 'http://{uname}.myopenid.com/'},
	{'oname': 'claimid',  'otitle': 'ClaimId',  'userinput': 1, 'ourl': 'http://claimid.com/{uname}'},
	{'oname': 'myvidoop',  'otitle': 'MyVidoop',  'userinput': 1, 'ourl': 'http://{uname}.myvidoop.com/'}			
]};

/* SET OPENID PROVIDER */
function setoidProvider(provider) {
	var pname = '';
	var ourl = '';
	for (i=0; i < prObj.providers.length; i++) {
		pname = prObj.providers[i].oname;
		if (pname == provider) {
			var lng_logwith = document.getElementById('lng_openid_loginwith').innerHTML;
			var opendesc = lng_logwith.replace('zzz', prObj.providers[i].otitle);
			document.getElementById('openid_provider').value = pname;
			document.getElementById('open_'+pname).style.backgroundColor = '#FFFF99';
			document.getElementById('open_label').innerHTML = opendesc;
			if (prObj.providers[i].userinput == 0) {
				document.getElementById('openid_identifier').style.display = 'none';
				document.getElementById('lblock').innerHTML = prObj.providers[i].ourl;
				document.getElementById('rblock').innerHTML = '';
				document.fmopenid.submit();
			} else {
				document.getElementById('openid_identifier').style.display = '';
				ourl = prObj.providers[i].ourl;
				var parts = ourl.split('{uname}');
				document.getElementById('lblock').innerHTML = parts[0];
				if (parts.length == 2) {
					document.getElementById('rblock').innerHTML = parts[1];
				} else {
					document.getElementById('rblock').innerHTML = '';
				}
				if (pname == 'openid') {
					document.getElementById('openid_identifier').style.width = '240px';
				} else {
					document.getElementById('openid_identifier').style.width = '140px';
				}
			}
		} else {
			var boxObj = document.getElementById('open_'+pname).style.backgroundColor = 'transparent';
		}
	}
}


/* VALIDATE FORM */
function elxformvalopenid() {
	var provider = document.getElementById('openid_provider').value;
	if (provider == '') {
		alert('No provider selected!');
		return false;
	}

	var found = 0;
	for (i=0; i < prObj.providers.length; i++) {
		pname = prObj.providers[i].oname;
		if (pname == provider) {
			found = 1;
			if (prObj.providers[i].userinput == 1) {
				if (document.getElementById('openid_identifier').value == '') {
					var lng_empty = document.getElementById('lng_openid_reqfempty').innerHTML;
					alert(lng_empty);
					elxFocus('openid_identifier');
					return false;
				}
			} else {
				document.getElementById('openid_identifier').value = '';
			}
			break;
		}
	}

	if (found == 0) {
		alert('Invalid OpenID provider!');
		return false;
	}
	return true;
}
