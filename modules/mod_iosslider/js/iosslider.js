/*
IOS Slider is a modified lite version of Coda Slider v2.0 by Niall Doherty
Author: Ioannis Sannos (http://www.isopensource.com

Original copyright notes:
jQuery Coda-Slider v2.0 - http://www.ndoherty.biz/coda-slider
Copyright (c) 2009 Niall Doherty
This plugin available for use in all personal or commercial projects under both MIT and GPL licenses.
*/

$(function(){
	$('.iosslider').children('.iospanel').hide().end().prepend('<p class="loading">Loading...<br /><img src="/modules/mod_iosslider/css/ajax-loader.gif" alt="loading..." /></p>');
});

var sliderCount = 1;

$.fn.iosSlider = function(settings) {
	settings = $.extend({
		autoHeight: true,
		autoHeightEaseDuration: 1000,
		autoHeightEaseFunction: "easeInOutExpo",
		autoSlide: false,
		autoSlideInterval: 6000,
		autoSlideStopWhenClicked: true,
		crossLinking: true,
		externalTriggerSelector: "a.iosslider_trig",
		firstPanelToLoad: 1,
		slideEaseDuration: 1000,
		slideEaseFunction: "easeInOutExpo",
		slideIdx: 1,
		vertical: false
	}, settings);

	return this.each(function(){
		var slider = $(this);
		var panelWidth = slider.find(".iospanel").width();
		var panelCount = slider.find(".iospanel").size();
		var panelContainerWidth = panelWidth*panelCount;
		var navClicks = 0;
		$('.iospanel', slider).wrapAll('<div class="iospanel-container"></div>');
		$(".iospanel-container", slider).css({ width: panelContainerWidth });
		if (settings.crossLinking && location.hash && parseInt(location.hash.slice(1)) <= panelCount) {
			var currentPanel = parseInt(location.hash.slice(1));
			var offset = - (panelWidth*(currentPanel - 1));
			$('.iospanel-container', slider).css({ marginLeft: offset });
		} else if (settings.firstPanelToLoad != 1 && settings.firstPanelToLoad <= panelCount) { 
			var currentPanel = settings.firstPanelToLoad;
			var offset = - (panelWidth*(currentPanel - 1));
			$('.iospanel-container', slider).css({ marginLeft: offset });
		} else { 
			var currentPanel = 1;
		};

		$(settings.externalTriggerSelector).each(function() {
			if (sliderCount == parseInt($(this).attr("rel").slice(10))) {//rel="iosslider-"
				$(this).bind("click", function() {
					navClicks++;
					targetPanel = parseInt($(this).attr("id").slice(16));
					offset = - (panelWidth*(targetPanel - 1));
					alterPanelHeight(targetPanel - 1);
					currentPanel = targetPanel;
					iosMarkCurrent(settings.slideIdx, currentPanel);
					$('.iospanel-container', slider).animate({ marginLeft: offset }, settings.slideEaseDuration, settings.slideEaseFunction);
					if (!settings.crossLinking) { return false };
				});
			};
		});

		if (settings.crossLinking && location.hash && parseInt(location.hash.slice(1)) <= panelCount) {
			iosMarkCurrent(settings.slideIdx, location.hash.slice(1));
		} else if (settings.firstPanelToLoad != 1 && settings.firstPanelToLoad <= panelCount) {
			iosMarkCurrent(settings.slideIdx, settings.firstPanelToLoad);
		} else {
			iosMarkCurrent(settings.slideIdx, 1);
		};

		if (settings.autoHeight) { panelHeight = $('.iospanel:eq(' + (currentPanel - 1) + ')', slider).height(); slider.css({ height: panelHeight }); };
		if (settings.autoSlide) { slider.ready(function() { setTimeout(autoSlide,settings.autoSlideInterval); }); };

		function iosMarkCurrent(slc, current) {
			current = parseInt(current);
			if (current < 1) { current  = 1; }
			slc = parseInt(slc);
			if (slc < 1) { slc  = 1; }
			if (settings.vertical == true) {
				$('ul.iosslider_vnav').find('li a').each(function() { if ($(this).attr('rel') == 'iosslider-'+slc) { $(this).removeClass('iosslcurrent'); } });
			} else {
				$('ul.iosslider_nav').find('li a').each(function() { if ($(this).attr('rel') == 'iosslider-'+slc) { $(this).removeClass('iosslcurrent'); } });
			}
			var aidx = 'iosslider_trig'+slc+'_'+current;
			$('#'+aidx).addClass('iosslcurrent');
		};

		function alterPanelHeight(x) {
			if (settings.autoHeight) {
				panelHeight = $('.iospanel:eq(' + x + ')', slider).height()
				slider.animate({ height: panelHeight }, settings.autoHeightEaseDuration, settings.autoHeightEaseFunction);
			};
		};

		function autoSlide() {
			if (navClicks == 0 || !settings.autoSlideStopWhenClicked) {
				if (currentPanel == panelCount) {
					var offset = 0;
					currentPanel = 1;
				} else {
					var offset = - (panelWidth*currentPanel);
					currentPanel += 1;
				};
				alterPanelHeight(currentPanel - 1);
				iosMarkCurrent(settings.slideIdx, currentPanel);
				$('.iospanel-container', slider).animate({ marginLeft: offset }, settings.slideEaseDuration, settings.slideEaseFunction);
				setTimeout(autoSlide,settings.autoSlideInterval);
			};
		};

		$('.iospanel', slider).show().end().find("p.loading").remove();
		slider.removeClass("preload");
		sliderCount++;
	});
};