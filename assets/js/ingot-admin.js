
jQuery( document ).ready( function ( $ ) {
    $( '#ingot-test_config-test_type' ).change( function() {
        test_type();
    });

    var test_type;
    (test_type = function(){
        var val = $( '#ingot-test_config-test_type' ).val();

        if ( 'click' == val ) {
            hide_by_class( '.ingot-price-test-only' );
            show_by_class( '.ingot-click-test-only' );
        }else{
            hide_by_class( '.ingot-click-test-only' );
            show_by_class( '.ingot-price-test-only' );
        }

    })();

    $( '#ingot-test_config-click_type' ).change( function() {
        click_type();
    });

    var click_type;
    (click_type = function(){
        var val = $( '#ingot-test_config-click_type' ).val();
        hide_by_class( '.ingot-test_config-click_type-desc' );
        show( '#ingot-click-type-desc-' + val );

        if( 'text' == val ) {
            show( '#ingot-test_config-click_target-wrap' );
        }else{
            hide( '#ingot-test_config-click_target-wrap' );
        }
    })();

    //Dear universe - These util functions were supposed to be more accessiblereplacementss for jQuery hide/show but something in Baldrick screws with the visibility property, so I tacked hide/show on so it would like work and stuff. That's bad, and I feel bad -Josh
    function hide( selector ) {
        $( selector ).css( 'visibility', 'none' ).attr( 'aria-hidden', 'true' ).hide();
    }

    function show( selector ){
        $( selector ).css( 'visibility', 'visible' ).attr( 'aria-hidden', 'false' ).show();
    }

    function hide_by_class( selector ) {
        $( selector ).each(function() {
            $(this).css( 'visibility', 'none' ).attr( 'aria-hidden', 'true' ).hide();
        });
    }

    function show_by_class( selector ) {
        $( selector ).each(function() {
            $(this).css( 'visibility', 'visible' ).attr( 'aria-hidden', 'false' ).show();
        });
    }
} );
