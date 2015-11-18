/* globals INGOT_VARS */
jQuery( document ).ready( function ( $ ) {

    $( document ).on( 'click', '.ingot-click-test', function(e) {
        e.preventDefault();

        var href = $( this ).attr( 'href' );
        var test = $( this ).data( 'ingot-test-id' );
        if( 'undefined' == test) {
            return;
        }

        var data = {
            test: test,
            sequence: $( this ).data( 'ingot-sequence-id' ),
            click_nonce: $( this ).data( 'ingot-test-nonce' )
        };

        $.when(
            $.ajax({
                url: INGOT_VARS.api_url + 'test/' + test + '/click?_wpnonce=' + INGOT_VARS.nonce,
                method: "POST",
                data: data,
                beforeSend: function ( xhr ) {
                    xhr.setRequestHeader( 'X-WP-Nonce', INGOT_VARS.nonce );
                },
            }).then(function( data, textStatus, jqXHR ) {
                window.location = href;
            })
        );
    });


    $( '.ingot-click-test-text' ).each( function( i, el ) {
            //@todo this
    });
} );

