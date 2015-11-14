/* globals INGOT_VARS */
jQuery( document ).ready( function ( $ ) {

    $( document ).on( 'click', '.ingot-click-test', function(e) {
        e.preventDefault();

        var href = $( this ).attr( 'href' );
        var test = $( this ).data( 'ingot-test-id' );
        if( 'undefined' == test) {
            window.location = href;
            return;
        }

        var data = {
            test: test,
            sequence: $( this ).data( 'ingot-sequence-id' ),
            click_nonce: $( this ).data( 'ingot-test-nonce' )
        };

        $.when(
            $.ajax({
                url: INGOT_VARS.api_url + 'test/' + test + '/click',
                method: "POST",
                data: data,
                beforeSend: function ( xhr ) {
                    xhr.setRequestHeader( 'X-WP-Nonce', INGOT_VARS.nonce );
                },
            }).success(function( data, textStatus, jqXHR ) {
                window.location = href;
            } ).error( function(){
                window.location = href;
            })
        );
    });


    $( '.ingot-click-test-text' ).each( function( i, el ) {
            //@todo this
    });
} );

