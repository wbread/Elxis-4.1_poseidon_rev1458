/*
Elxis CMS - plugin Google Maps Links
Created by Stavros Stratakis / Ioannis Sannos
Copyright (c) 2006-2012 elxis.org
http://www.elxis.org
*/

/* ADD AUTOLINKS CODE */
function addMapAreaCode() {
	var area = document.getElementById('map_area').value;
	if (area == '') { return false; }
	var mapw = document.getElementById('map_width').value;
	var maph = document.getElementById('map_height').value;
	mapw = parseInt(mapw, 10);
	maph = parseInt(maph, 10);
	if ((mapw > 50) && (maph > 50)) {
		var pcode = '{map width="'+mapw+'" height="'+maph+'"}'+area+'{/map}';
	} else {
		var pcode = '{map}'+area+'{/map}';
	}
	addPluginCode(pcode);
}


var googlemaps = [];

/* INITIALIZE GOOGLE MAPS */
function initGoogleMaps() {
	for (var idx = 1; idx < 11; idx++) {
		if (document.getElementById('googlemap'+idx)) {
			initGoogleMap(idx);
		} else {
			break;
		}
	}
}

/* INITIALIZE GOOGLE MAP */
function initGoogleMap(idx) {
	var myOptions = {
		zoom: mapcfg.mzoom,
		mapTypeControl: mapcfg.mtypecontrol,
		mapTypeControlOptions: { style: google.maps.MapTypeControlStyle.DEFAULT },
		zoomControl: mapcfg.mzoomcontrol,
		zoomControlOptions: { style: google.maps.ZoomControlStyle.DEFAULT, position: google.maps.ControlPosition.TOP_LEFT },
		navigationControl: mapcfg.mnavcontrol,
		navigationControlOptions: { style: google.maps.NavigationControlStyle.DEFAULT, position: google.maps.ControlPosition.RIGHT },
		scaleControl: mapcfg.mscale,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	};
	if (mapcfg.mtypecontrol == true) {
		switch (mapcfg.mtypecontrolopts) {
			case 'DEFAULT': myOptions.mapTypeControlOptions = { style: google.maps.MapTypeControlStyle.DEFAULT }; break;
			case 'HORIZONTAL_BAR': myOptions.mapTypeControlOptions = { style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR }; break;
			case 'DROPDOWN_MENU': myOptions.mapTypeControlOptions = { style: google.maps.MapTypeControlStyle.DROPDOWN_MENU }; break;
			default: break;
		}
	}
	if (mapcfg.mzoomcontrol == true) {
		switch (mapcfg.mzoomcontrolopts) {
			case 'DEFAULT': myOptions.zoomControlOptions = { style: google.maps.ZoomControlStyle.DEFAULT, position: google.maps.ControlPosition.TOP_LEFT }; break;
			case 'SMALL': myOptions.zoomControlOptions = { style: google.maps.ZoomControlStyle.SMALL, position: google.maps.ControlPosition.TOP_LEFT }; break;
			case 'LARGE': myOptions.zoomControlOptions = { style: google.maps.ZoomControlStyle.LARGE, position: google.maps.ControlPosition.TOP_LEFT }; break;
			default: break;
		}
	}
	if (mapcfg.mnavcontrol == true) {
		switch (mapcfg.mnavcontrolopts) {
			case 'DEFAULT': myOptions.navigationControlOptions = { style: google.maps.NavigationControlStyle.DEFAULT, position: google.maps.ControlPosition.RIGHT }; break;
			case 'SMALL': myOptions.navigationControlOptions = { style: google.maps.NavigationControlStyle.SMALL, position: google.maps.ControlPosition.RIGHT }; break;
			case 'ANDROID': myOptions.navigationControlOptions = { style: google.maps.NavigationControlStyle.ANDROID, position: google.maps.ControlPosition.RIGHT }; break;
			case 'ZOOM_PAN': myOptions.navigationControlOptions = { style: google.maps.NavigationControlStyle.ZOOM_PAN, position: google.maps.ControlPosition.RIGHT }; break;
			default: break;
		}
	}
	switch (mapcfg.mnavcontrolopts) {
		case 'ROADMAP': myOptions.mapTypeId = google.maps.MapTypeId.ROADMAP; break;
		case 'SATELLITE': myOptions.mapTypeId = google.maps.MapTypeId.SATELLITE; break;
		case 'HYBRID': myOptions.mapTypeId = google.maps.MapTypeId.HYBRID; break;
		case 'TERRAIN': myOptions.mapTypeId = google.maps.MapTypeId.TERRAIN; break;
		default: break;
	}

	myOptions.center = new google.maps.LatLng(mapcfg.lat[idx],mapcfg.lng[idx]);

	var map = new google.maps.Map(document.getElementById('googlemap'+idx), myOptions);
	if (!getGoogleMap(idx)) {
		var mapInfo = { idx: idx, map: map, marker: null, infowindow: null };
		googlemaps.push(mapInfo);
	}
	placeMarkers(idx);
}

/* GETMAP INSTANCE */
function getGoogleMap(idx) {
	for (var i=0; i < googlemaps.length; i++) {
		if (googlemaps[i].idx == idx) { return i; }
	}
	return false;
}

/* PLACE MARKERS ON MAP */
function placeMarkers(idx) {
	var mapIndex = getGoogleMap(idx);
	if (mapIndex === false) { return; }

	googlemaps[mapIndex].marker = new google.maps.Marker({
		position: googlemaps[mapIndex].map.getCenter(),
		map: googlemaps[mapIndex].map,
		animation: google.maps.Animation.DROP
	});

	if (mapcfg.info[idx] != '') {
		googlemaps[mapIndex].infowindow = new google.maps.InfoWindow({ content: mapcfg.info[idx] });
		google.maps.event.addListener(googlemaps[mapIndex].marker, 'click', function() {
			googlemaps[mapIndex].infowindow.open(googlemaps[mapIndex].map, googlemaps[mapIndex].marker);
		});
	}
}
