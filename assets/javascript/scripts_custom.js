$(document).ready(function(){	
	
    'use strict';

    var ULTRA_SETTINGS = window.ULTRA_SETTINGS || {};

    // Tooltips & Popovers
    ULTRA_SETTINGS.tooltipsPopovers = function () {

        // Tooltips
		$('[rel="tooltip"]').each(function () {
            var animate = $(this).attr("data-animate");
            var colorclass = $(this).attr("data-color-class");
            $(this).tooltip({
                template: '<div class="tooltip ' + animate + ' ' + colorclass + '"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>'
            });
        });

		// Popovers
        $('[rel="popover"]').each(function () {
            var animate = $(this).attr("data-animate");
            var colorclass = $(this).attr("data-color-class");
            $(this).popover({
                template: '<div class="popover ' + animate + ' ' + colorclass + '"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
            });
        });
		
    };

    // Inicializar o script do Tooltips & Popovers
	ULTRA_SETTINGS.tooltipsPopovers();

	// Inicializar menu
	new gnMenu( document.getElementById( 'gn-menupbl' ) );
	
	// fade in #back-top
	jQuery(function () {
		jQuery(window).scroll(function () {
			if (jQuery(this).scrollTop() > 100) {
				jQuery('#back-top').fadeIn();
			} else {
				jQuery('#back-top').fadeOut();
			}
		});

		// scroll body to 0px on click
		jQuery('#back-top a').click(function () {
			jQuery('body,html').stop(false, false).animate({
				scrollTop: 0
			}, 800);
			return false;
		});
	});
	// Final - fade in #back-top
	
});