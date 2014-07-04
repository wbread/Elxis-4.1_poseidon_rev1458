/**
Elxis CMS
Module Admin users
Ioannis Sannos / Elxis Team
http://www.elxis.org
*/

/* FORCE LOGOUT USER */
function adusersLogout(uid, gid, lmethod, uip, fact, rid) {
	var eurl = document.getElementById('aduserselxisaurl').innerHTML+'cpanel/forcelogout';
	var successfunc = function(data) {
        var update = new Array();
        update = data.split('|');
        if (update[0] == '1') {
        	rid = parseInt(rid);
        	//or location.reload(true);
       		var tblObj = document.getElementById('adusersonlinetbl');
       		for (var i = 1; i <= tblObj.rows.length; i++) {
				if (tblObj.rows[i].id == 'adusersrow'+rid) {
					tblObj.deleteRow(i);
					break;
				}
			}
		} else {
			alert(update[1]);
		}
	};
	var errorfunc;
	var edata = { 'uid': uid, 'gid': gid, 'lmethod': lmethod, 'ip': uip, 'fact': fact };
	elxAjax('POST', eurl, edata, null, null, successfunc, errorfunc);
}


/* BAN AN IP ADDRESS */
function adusersBanIP(uip, rid) {
	var eurl = document.getElementById('aduserselxisaurl').innerHTML+'cpanel/banip';
	var successfunc = function(data) {
        var update = new Array();
        update = data.split('|');
        if (update[0] == '1') {
        	rid = parseInt(rid);
       		var tblObj = document.getElementById('adusersonlinetbl');
       		for (var i = 1; i <= tblObj.rows.length; i++) {
				if (tblObj.rows[i].id == 'adusersrow'+rid) {
					tblObj.deleteRow(i);
					break;
				}
			}
			alert(update[1]);
		} else {
			alert(update[1]);
		}
	};
	var errorfunc;
	var edata = { 'ip': uip };

	elxAjax('POST', eurl, edata, null, null, successfunc, errorfunc);
}
