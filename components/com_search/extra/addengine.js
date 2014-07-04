/*
Elxis CMS - OpenSearch
Copyright (c) 2006-2012 elxis.org
Author: Ioannis Sannos
*/
function installSearchEngine(osd) {
	if (window.external && ("AddSearchProvider" in window.external)) {
		window.external.AddSearchProvider(osd);
	} else {
		alert("Your browser does not support OpenSearch!");
	}
}