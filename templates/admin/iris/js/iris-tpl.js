//functions AFTER PAGE LOAD
$(document).ready(function(){
	//removable message boxes
	$('.elx_delay-close').livequery(function(){ $(this).delay(6000).fadeOut(2500); });
	$('.elx_close').livequery(function(){ $(this).prepend('<span class="elx_hide"></span>'); });
    $('.elx_hide').live('click', function(){ $(this).parent().fadeOut(600); });
});

$(document).ready(function(){
	$(".iris_colorboxclose").click(function(){ $.colorbox.close(); });
});
