/* globals INGOT_UI */
jQuery( document ).ready( function ( $ ) {
    /**
     * Setup our variables
     */
    var session_id = INGOT_UI.session.ID;
    var ingot_id = INGOT_UI.session.ingot_ID;
    var session_nonce = INGOT_UI.session_nonce;
    var nonce = INGOT_UI.nonce;
    var api_url = INGOT_UI.api_url;


    /**
     * Track all the clicks
     */
    (function () {
        //bind to all clicks
        $( document).on('click', null, $, clickHandler);

        //the one click handler to rule them all
        function clickHandler(e) {
            var el = e.target;
            var href = el.getAttribute( 'href' );
            var id = el.getAttribute( 'data-ingot-test-id' );
            if( 'undefined' == href || null == href ) {
                return;
            }

            e.preventDefault();
            if( null != id ) {
                trackConversionClick( id, href );
            }else{
                trackOtherClick( href );
            }

        }

        //track a conversion
        function trackConversionClick( id, href ){
            var data = {
                id: id,
                ingot_session_nonce: session_nonce,
                ingot_session_ID: session_id
            };

            var url = api_url + 'variants/' + id + '/conversion?_wpnonce=' + nonce + '&ingot_session_nonce=' + session_nonce + '&ingot_session_ID=' + session_id;

            $.when(
                $.ajax({
                    url: url,
                    method: "POST",
                    data: data,
                    beforeSend: function ( xhr ) {
                        xhr.setRequestHeader( 'X-WP-Nonce', nonce );
                    }
                }).success(function( data, textStatus, jqXHR ) {
                    window.location = href;
                } ).error( function(){
                    window.location = href;
                } ).fail( function()  {
                    window.location = href;
                })
            );

        }

        //track a failed conversion
        function trackOtherClick( href ) {
            var url = api_url + 'sessions/' + session_id + '/track?_wpnonce=' + nonce + '&ingot_session_nonce=' + session_nonce + '&ingot_session_ID=' + session_id;
            var data = {
                ingot_session_nonce: session_nonce,
                click_url: href
            };
            $.when(
                $.ajax({
                    url: url,
                    method: "POST",
                    data: data,
                    beforeSend: function ( xhr ) {
                        xhr.setRequestHeader( 'X-WP-Nonce', nonce );
                    }
                }).success(function( data, textStatus, jqXHR ) {
                    window.location = href;
                } ).error( function(){
                    window.location = href;
                } ).fail( function()  {
                    window.location = href;
                })
            );
        }
    } ());

    /**
     * Ensure session is valid and update HTML if dealing with cache
     */
    (function () {

        var test_ids = [];
        var test_els = $( '.ingot-test' );
        var l = test_els.length;
        var id, el, i;
        var cookies = Cookies.get();
        var keys = Object.keys( cookies );
        var prefix = 'ingot_destination_';
        var ingot_cookies = [];
        if( 0 < keys.length ) {
            var key, variant;
            for( i = 0; i <= keys.length; i++ ){
                key = keys[ i ];
                if( undefined != key && key.startsWith( prefix ) ){
                    variant = cookies[ keys[ i ] ];
                    ingot_cookies.push({
                        g: key.replace( prefix, '' ),
                        v: variant
                    } )
                }
            }
        }
        if( 0 != l ){
            for( i = 0; i <= l; i++ ) {
                el = test_els[i];
                if ( 'undefined' != el && null != el ) {
                    id = el.getAttribute( 'data-ingot-test-id' );
                    if ( null != id ) {
                        test_ids.push( id );
                    }
                }
            }
        }


        if( 0 != test_ids.length ) {
            var url = api_url + 'sessions/' + session_id + '/tests/?ingot_session_nonce=' + session_nonce + '&ingot_session_ID=' + session_id;

            var data = {
                ingot_session_nonce: session_nonce,
                test_ids :test_ids,
                ingot_id: ingot_id,
                cookies: ingot_cookies
            };

            $.when(
                $.ajax({
                    url: url,
                    method: "POST",
                    data: data,
                    beforeSend: function ( xhr ) {
                        xhr.setRequestHeader( 'X-WP-Nonce', nonce );
                    }
                }) ).then(function( data, textStatus, jqXHR ) {
                    var new_test_data = data.tests;
                    cookieResponse( data.cookies );
                    if ( 'undefined' != new_test_data && 0 < new_test_data.length ) {
                        var test_id, test_html, existing_el;
                        $.each( new_test_data, function ( i, test ) {
                            test_id = test.ID;
                            test_html = test.html;
                            existing_el = document.getElementById( 'ingot-test-' + test_id );
                            if ( null != existing_el && '' != test_html ) {
                                existing_el.parentNode.innerHTML = test_html;
                            }
                        } );
                    }

                    ingot_id = data.ingot_ID;
                    session_id = data.session_ID;

                } );
        }else{
            cookieCheck();
        }

        function cookieResponse( r ){
            var i = 0;
            var keys;
            if( 0 < r.add.length ){
                keys = data.add.keys();
                for( i = 0; i <= r.add.length; i++ ){
                    Cookies.add( prefix + r.keys[ i ], r.add[ i ], {
                        path: data.path,
                        domain: data.domain
                    } )
                }
            }

            if( 0 < r.remove.length ){
                keys = r.remove.keys();
                for( i = 0; i <= r.remove.length; i++ ){
                    Cookies.remove( prefix + r.keys[ i ] );
                }
            }

            if( 0 < r.add.wrong_variant ) {
                keys = r.wrong_variant.keys();
                for ( i = 0; i <= r.add.length; i++ ) {
                    Cookies.remove( prefix + r.keys[ i ] );
                    Cookies.add( prefix + r.keys[ i ], r.add[ i ], {
                        path: r.path,
                        domain: r.domain
                    } )
                }
            }
        }

        function cookieCheck(){
            $.when(
                $.ajax({
                    url: api_url + 'sessions/' + session_id + '/cookies?_wpnonce=' + nonce + '&ingot_session_nonce=' + session_nonce + '&ingot_session_ID=' + session_id,
                    method: "POST",
                    data: {
                        ingot_session_nonce: session_nonce,
                        cookies: ingot_cookies
                    },
                    beforeSend: function ( xhr ) {
                        xhr.setRequestHeader( 'X-WP-Nonce', nonce );
                    }
                })
            ).then(function( response  ) {
                cookieResponse( response );
            });
        }
    } ());

    (function () {








    })();

});

