/*
Elxis CMS - plugin Youtube Video
Created by Stavros Stratakis
Copyright (c) 2006-2012 elxis.org
http://www.elxis.org
*/

/* ADD VIDEO ID */
function addYTVideoID() {
	var videoid = document.getElementById('youtube_videoid').value;
    if (videoid == '') {return false;}
	var pcode = '{youtube}'+videoid+'{/youtube}';
	addPluginCode(pcode);
}
