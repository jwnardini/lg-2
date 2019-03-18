jQuery( document ).ready( function( $ ) {

    var _f = $( '#esdc_time_from' ),
        _t = $( '#esdc_time_to' ),
        tabs = $( '.nav-tab'),
        tabbed = $( '#tabbed' ),
        sfield = $( '#searchfield' ),
        srch = sfield.find( '#esdc-search' ),
        tracked = $.parseJSON( ESDC_JS.tracked ),
        selector = '';

    ts();
    function ts() {
        var _ts = [];
        for( var tr in tracked ) {
            _ts[tr] = 'a[href$=".' + tracked[tr] + '"]';
        }
        selector = _ts.join(',');
    }

    $(selector).click( function( e ) {
        e.preventDefault();
        var pn_splode = $(this)[0].pathname.split( '/' );
        var fn = pn_splode[ pn_splode.length-1 ];
        payload = {
            action: 'esdc_addtocount',
            filename: fn,
            cnonce: ESDC_JS.count_nonce
        };

        ajax( payload, this );
        return false;

    });
    _f.datepicker({
        dateFormat: 'yy-mm-dd',
        onSelect: function( e, f ) {
            if( f.id === "esdc_time_from" ) {
                _t.datepicker( "option", "minDate", e );
            }
        }
    });
    _t.datepicker({
        dateFormat: 'yy-mm-dd'
    });
    tabs.click( function() {
        var id = '.' + $(this).attr( 'id' );
        tabbed.find( '.esdc-container' ).removeClass( 'active' );
        tabs.removeClass( 'nav-tab-active' );
        $(this).addClass( 'nav-tab-active' );
        tabbed.find( id ).addClass( 'active' );
    });
    srch.click( function() {
        var _from = sfield.find( '#esdc_time_from' ).val(),
            _to = sfield.find( '#esdc_time_to' ).val(),
            payload = {
                action: 'esdc_search_dates',
                from: _f.val(),
                to: _t.val(),
                snonce: ESDC_JS.ds_nonce
            };
        ajax( payload );
    });
    function ajax( payload, f ) {
        $.ajax({
            url: ESDC_JS.ajax_url,
            data: payload,
            success: function( e ) {
                if( payload.action === 'esdc_search_dates' ) {
                    $( '#esdc-search-results' ).html( e );
                } else if( payload.action === 'esdc_addtocount' ) {
                    window.location = f;
                }
            }
        });
    }
});
