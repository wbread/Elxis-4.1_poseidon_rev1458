jQuery.fn.initMenu = function() {
    return this.each(function(){
        var theMenu = $(this).get(0);
        $('.elx_acitem', this).hide();
        $('li.elx_acexpand > .elx_acitem', this).show();
        $('li.elx_acexpand > .elx_acitem', this).prev().addClass('active');
        $('li a', this).click(
            function(e) {
                e.stopImmediatePropagation();
                var theElement = $(this).next();
                var parent = this.parentNode.parentNode;
                if($(parent).hasClass('elx_noaccordion')) {
                    if(theElement[0] === undefined) {
                        window.location.href = this.href;
                    }
                    $(theElement).slideToggle('normal', function() {
                        if ($(this).is(':visible')) {
                            $(this).prev().addClass('active');
                        }
                        else {
                            $(this).prev().removeClass('active');
                        }
                    });
                    return false;
                }
                else {
                    if(theElement.hasClass('elx_acitem') && theElement.is(':visible')) {
                        if($(parent).hasClass('elx_accollapsible')) {
                            $('.elx_acitem:visible', parent).first().slideUp('normal',
                            function() {
                                $(this).prev().removeClass('active');
                            }
                        );
                        return false;
                    }
                    return false;
                }
                if(theElement.hasClass('elx_acitem') && !theElement.is(':visible')) {
                    $('.elx_acitem:visible', parent).first().slideUp('normal', function() {
                        $(this).prev().removeClass('active');
                    });
                    theElement.slideDown('normal', function() {
                        $(this).prev().addClass('active');
                    });
                    return false;
                }
            }
        }
    );
});
};


//functions AFTER PAGE LOAD
$(document).ready(function() {
    $('.elx_accordion').initMenu();
});//DOM END
