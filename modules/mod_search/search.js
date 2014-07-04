/*
Elxis CMS - module Search
Copyright (c) 2006-2012 elxis.org
*/
function msearch_pick(engine){
	var baseact = document.getElementById('msearch_abase').innerHTML;
	var baseurl = document.getElementById('msearch_ubase').innerHTML;
	document.getElementById('fmmodsearch').action = baseact+''+engine+'.html';
	document.getElementById('msearch_icon').src = baseurl+'/components/com_search/engines/'+engine+'/'+engine+'.png';
}

function msearch_clear(ison) {
	var textel = document.getElementById('msearchq');
	var sear = document.getElementById('msearch_sear').innerHTML;
	if (ison == 1) {
		if (textel.value == sear) { textel.value = ''; }
		textel.className = 'elx_modsearch_input_on';
	} else {
		textel.className = 'elx_modsearch_input';
		if (textel.value == '') { textel.value = sear; }
	}
}