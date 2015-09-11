/** globals jQuery, INGOT */
jQuery( document ).ready( function ( $ ) {
    $( document ).on( 'click', '.ingot-click-test', function(e) {
        e.preventDefault();

        var href = $( this ).attr( 'href' );
        var test_id = $( this ).data( 'ingot-test-id' );
        var sequence_id = $( this ).data( 'ingot-sequence-id' );
        var test_type = $( this ).data( 'ingot-click-test_type' );
        var data = {
            action: 'ingot_click_test',
            ingot_test_id : test_id,
            ingot_sequence_id : sequence_id,
            ingot_test_type : test_type,
            ingot_nonce: INGOT.nonce
        };

        $.ajax({
            type: "POST",
            url: INGOT.api_url,
            data: data,
            fail: function(r) {
                console.log(r);
            },
            complete: function() {
                window.location = href;
            }
        });


    } )
} );
