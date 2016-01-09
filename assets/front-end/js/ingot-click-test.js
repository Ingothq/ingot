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
                ingot_id: ingot_id
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

                    var new_test_data = data.tests;

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

                } ).error( function(){

                } ).fail( function()  {

                })
            );
        }
    } ());

});

