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
    //bind to all clicks

    $( document).on('click', null, $, clickHandler);

    //the one click handler to rule them all
    function clickHandler(e) {
        var el = e.target;
        var href = el.getAttribute( 'href' );
        var id = el.getAttribute( 'data-ingot-test-id' );
        if( 'undefined' != href ) {
            e.preventDefault();
            if( null != id ) {
                console.log( 'convert' );
                trackConversionClick( id, href );
            }else{
                console.log( 'notconvert' );
                trackOtherClick( href );
            }
        }

    }

    //tracl a conversion
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



} );

