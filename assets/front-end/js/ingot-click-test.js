/* globals INGOT_UI */
jQuery( document ).ready( function ( $ ) {

    var session_id = INGOT_UI.session.ID;
    var ingot_id = INGOT_UI.session.ingot_ID;
    var session_nonce = INGOT_UI.session_nonce;
    var nonce = INGOT_UI.nonce;

    /**
     * Track click tests
     *
     * @since 0.0.x
     */
    $( document ).on( 'click', '.ingot-click-test', function(e) {
        var href = $( this ).attr( 'href' );
        var id = $( this ).data( 'ingot-test-id' );
        console.log( href );
        console.log( id );
        if( 'undefined' !== href && 'undefined' != id ) {
            e.preventDefault();

            var data = {
                id: id,
                ingot_session_nonce: session_nonce,
                ingot_session_ID: session_id
            };

            var url = INGOT_UI.api_url + 'variants/' + id + '/conversion?_wpnonce=' + nonce + '&ingot_session_nonce=' + session_nonce + '&ingot_session_ID=' + session_id;

            $.when(
                $.ajax({
                    url: url,
                    method: "POST",
                    data: data,
                    beforeSend: function ( xhr ) {
                        xhr.setRequestHeader( 'X-WP-Nonce', nonce );
                    },
                }).success(function( data, textStatus, jqXHR ) {
                    window.location = href;
                } ).error( function(){
                    window.location = href;
                } ).fail( function()  {
                    window.location = href;
                })
            );

        }




    });

    /**
     * Double check our tests are correct -- IE not cached versions
     *
     * @since 0.3.0
     */
    $( window ).load( function () {
        return;
        if( null == INGOT_VARS.session.ID ) {
            return;
        }

        var tests, test_list, url;
        var the_tests = [];
        tests = $( '.ingot-click-test' );
        $.each( tests, function( i, test ) {
            the_tests.push( $( test ).attr( 'data-ingot-test-id' ) );
        });

        console.log( the_tests );
        if( tests.length > 0 ) {
            test_list =  the_tests.join(",");
            url = INGOT_VARS.api_url + 'sessions/' + session_id + '/session?_wpnonce=' + INGOT_VARS.nonce + '&ingot_session_nonce=' + INGOT_VARS.session_nonce + '&test_ids=' + test_list + '&ingot_session_ID=' + session_id;
            $.when(
                $.ajax({
                    url: url,
                    method: "GET",
                    beforeSend: function ( xhr ) {
                        xhr.setRequestHeader( 'X-WP-Nonce', INGOT_VARS.nonce );
                    },
                }).success(function( data, textStatus, jqXHR ) {
                    ingot_id = data.ingot_ID;
                    session_id = data.session_ID;
                    if( 'undefined' != data.tests && 'object' == typeof  data.tests && ! $.isEmptyObject( data.tests ) ) {
                        $.each( data.tests, function( i, test ){
                            var id = test.ID;
                            var html = test.html;
                            var html_id = 'ingot-test-' + id;
                            var el = document.getElementById( html_id );
                            if( null != el ) {
                                el.innerHTML = html;
                            }

                        });
                    }
                } )
            );
        }

    } );

    /**
     * Track all clicks by session
     *
     * @since 0.3.0
     */
    $(document).click(function(e) {
        return;
        e.preventDefault();

        if( 'undefined' != e.target.href && ! $( e.target ).hasClass( '.ingot-click-test' ) ) {
            var url = INGOT_VARS.api_url + 'sessions/' + session_id + '?_wpnonce=' + INGOT_VARS.nonce + '&ingot_session_nonce=' + INGOT_VARS.session_nonce + '&click_url=' + encodeURIComponent( e.target.href );

            $.ajax({
                url: url,
                method: "POST",
                beforeSend: function ( xhr ) {
                    xhr.setRequestHeader( 'X-WP-Nonce', INGOT_VARS.nonce );
                },
            }).success(function( data, textStatus, jqXHR ) {
                window.location = e.target.href;
            } ).error( function(){
                window.location = e.target.href;
            } ).fail( function()  {
                window.location = e.target.href;
            })
        }else{
            alert( 0);
            if( 'undefined' != e.target.href ){
                window.location = e.target.href;
            }
        }
    });
} );

