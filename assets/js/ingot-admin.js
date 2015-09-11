
jQuery( document ).ready( function ( $ ) {
    $( '#ingot-test_config-test_type' ).change( function() {
        test_type();
    });

    var test_type;
    (test_type = function(){
        var val = $( '#ingot-test_config-test_type' ).val();

        if ( 'click' != val ) {
            hide( '.ingot-click-test-test_type' );
        }else{
            show( '.ingot-click-test-test_type' );
        }

        if ( 'price' != val ) {
            hide( '.ingot-price-test-test_type' );
        }else{
            show( '.ingot-price-test-test_type' );
        }
    })();

    $( '#ingot-test_config-click_type' ).change( function() {
        click_type();
    });

    var click_type;
    (click_type = function(){
        var val = $( '#ingot-test_config-click_type' ).val();
        hide( '.ingot-test_config-click_type-desc' );
        show( '#ingot-click-type-desc-' + val );

        if( 'text' == val ) {
            show( '#ingot-test_config-click_target-wrap' );
        }else{
            hide( '#ingot-test_config-click_target-wrap' );
        }
    })();

    function hide( el ) {
        $( el ).css( 'visibility', 'none' ).attr( 'aria-hidden', 'true' );
    }

    function show( el ){
        $( el ).css( 'visibility', 'visible' ).attr( 'aria-hidden', 'false' );
    }
} );
