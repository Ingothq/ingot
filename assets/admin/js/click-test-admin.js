/**
 * Created by josh on 9/14/15.
 */
jQuery( document ).ready( function ( $ ) {

    $( '#group-type' ).change( function() {
        var val = $( '#group-type' ).val();
        if ( 'text' != val ) {
            $( '#selector-wrap' ).hide();
        }else{
            $( '#selector-wrap' ).show();
        }
    });

    $( '#add-group' ).on( 'click', function(e) {

        e.preventDefault();

        $.get( INGOT.test_field, {}, function(r) {
            $( '#group-parts' ).prepend( r );
            var id = $( r ).attr( 'id' );
            console.log( id );

            $( '#remove-' + id ).on( 'click', function(e) {
                var remove = $( this ).data( 'part-id' );
                var el = document.getElementById( remove );
                if( null != el ) {
                    $( el ).remove();
                }
            });
        });





    });

    $( '.part-remove' ).each( function( i, button ){
        console.log( button );
        $( button ).on( 'click', function(e) {
            swal({
                title: INGOT.beta_error_header,
                text: INGOT.cant_remove,
                type: "error",
                confirmButtonText: INGOT.close
            });
        });
    });



    $( document ).on( 'submit', '#ingot-click-test', function( e) {
        e.preventDefault();
        $( '#spinner' ).show();


        var parts;
        parts = $( '.test-part' );

        var test_endpoint = INGOT.api_url + '/test/';


        var  id, _id, create, name, text, part_data, url;
        var test_ids = {};

        $.each( parts, function( i, part ){
            id = 0;
            _id = $( part ).attr( 'id' );
            create = false;

            if ( _id.substring(0,4) == "-ID_") {
                create = true;
            }else{
                id = _id;
            }

            name = $( '#name-'  + _id ).val();
            text = $( '#text-'  + _id ).val();

            part_data = {
                name: name,
                text: text,
                id: id
            };

            console.log( part_data );

            if ( create ) {
                url = test_endpoint;
            }else{
                url = test_endpoint + id;
            }

            $.ajax( {
                url: url,
                async: false,
                method: 'POST',
                beforeSend: function ( xhr ) {
                    xhr.setRequestHeader( 'X-WP-Nonce', INGOT.nonce );
                },
                data: part_data
            } ).always(function( r, status) {
                console.log( status );
                if( 'object' == typeof r){
                    console.log( r );
                    $( '#name-'  + _id ).attr( 'id', 'name-' + r.ID );
                    $( '#text-'  + _id ).attr( 'id', 'value-' + r.ID );
                    $( part ).attr( 'id', r.ID );
                    $( '#part-hidden-id-' + _id ).val( r.ID );
                    $( '#part-hidden-id-' + _id ).attr( 'id', 'part-hidden-id-' + r.ID );

                    test_ids[ i ] = r.ID;
                }
            });


        });

        var group_id = $( '#test-group-id' ).val();
        url = INGOT.api_url + '/test-group/';
        if ( group_id ) {
            url = url + group_id;
        }

        var group_data = {
            name : $( '#group-name' ).val(),
            type: $( '#group-type' ).val(),
            order: test_ids,
            selector: $( '#selector' ).val(),
            initial: $( '#intial' ).val(),
            threshold: $( '#threshold' ).val(),
            link: $( '#link' ).val()
        };


        $.ajax({
            url: url,
            data: group_data,
            method: 'POST',
            beforeSend: function ( xhr ) {
                xhr.setRequestHeader( 'X-WP-Nonce', INGOT.nonce );
            }

        } ).always(function( r, status ) {
            if( 'success' == status ){

                $( '#test-group-id' ).val( r.ID );
                var href = window.location.href.split('?')[0];
                var title = INGOT.test_group_page_title + r.name;
                var new_url = href + '?page=ingot&group=' + r.ID;
                $( '#spinner' ).hide();
                swal({
                    title: INGOT.success,
                    text: INGOT.saved + r.name,
                    type: "success",
                    confirmButtonText: INGOT.close
                });
                history.pushState( {}, title, new_url );
            }else{
                $( '#spinner' ).hide();
                swal({
                    title: INGOT.fail,
                    text: INGOT.fail,
                    type: "error",
                    confirmButtonText: INGOT.close
                });
            }
        });




    });
} );
