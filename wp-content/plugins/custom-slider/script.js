var $j = jQuery.noConflict();
$j(document).ready( function( $ ) {

    if ( $( document ).find( '#custom-slider li' ).length > 1 ) {
        $( '#custom-slider' ).cycle({
            fx:     'fade',
            speed:  'slow',
            timeout: 0,
            next:   '#custom-slider-next',
            prev:   '#custom-slider-prev'
        });
    }

});