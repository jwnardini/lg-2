(function($) {
    $(document).on('facetwp-loaded', function() {
        $('.facetwp-checkbox').each(function() {
            $(this).attr('role', 'checkbox');
            $(this).attr('aria-checked', $(this).hasClass('checked') ? 'true' : 'false');
            $(this).attr('tabindex', 0);

            $(this).bind( 'keydown', function( e ) {
                if( e.keyCode == 32 || e.keyCode == 13 ) {
                    e.stopPropagation();
                    e.preventDefault();

                    LG.LAST_FACETWP_CHECKED = $(this).data( 'value' );
                    
                    $(this).click();
                }
            } );
        });


        $('.facetwp-page').each(function() {
            $(this).attr('tabindex', 0);
            $(this).bind( 'keydown', function( e ) {

                if( e.keyCode == 13 ) {
                    e.stopPropagation();
                    e.preventDefault();
                    
                    $(this).click();
                }
            } );
        } );

        if ( LG.LAST_FACETWP_CHECKED !== null ) {
            $( '[data-value="' + LG.LAST_FACETWP_CHECKED + '"]' ).focus();
        }
    });
})(jQuery);