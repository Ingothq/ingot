/**
 * Created by josh on 9/14/15.
 */
jQuery( document ).ready( function ( $ ) {

    var saving = false;

    //outer wrap for ingot UI
    var outer_wrap = document.getElementById( 'ingot-outer-wrap' );

    //the all click handler
    $( document ).on( 'click', function(e) {
        var el = document.activeElement;
        var href = el.href;
        var group_id;
        group_id =  getParameterByName( 'group_id', href );
        if( 'null' !== group_id ) {
            if ( 'price' == getParameterByName( 'type', href ) ) {

                e.preventDefault();
                    var action;

                    if ( 'list' == group_id ) {
                        action = 'get_price_list_page';
                    } else {
                        action = 'get_price_group_page';
                    }

                    var data = {
                        _nonce: INGOT.admin_ajax_nonce,
                        action: action,
                        group_id: group_id
                    };


                    $( outer_wrap ).empty();
                    $( '<img/>', {
                        id: 'outer-loading-spinner',
                        src: INGOT.spinner_url,
                        alt: INGOT.spinner_alt,
                    }).appendTo( outer_wrap );
                    $.ajax( {
                        url: INGOT.admin_ajax,
                        method: "GET",
                        data: data,
                        complete: function ( r, status ) {

                            $( '#outer-loading-spinner' ).remove();
                            if( 'success' == status ) {
                                if( "0" == r.responseText ) {
                                    window.location = href;
                                }
                                $( outer_wrap ).html( r.responseText );
                                history.replaceState( {}, 'Ingot', href );
                            }

                        }

                    } );

            }else if( 'click' ==  getParameterByName( 'type', href ) ) {
                //use for click naviagations
            }

        }


        function getParameterByName(name, href ) {
            name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
            var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
                results = regex.exec( href);
            return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
        }
    });


    //hide show options based on group type
    $( '#group-type' ).change( function() {
        click_ui_hide_shows();
    });

    var click_ui_hide_shows;
    (click_ui_hide_shows = function(){
        var val = $( '#group-type' ).val();
        var all_color_divs = $.find( '.button-color, .wp-picker-container, #background-color-wrap, #button-color-wrap, .button-color-test,.button-text-test' );

        var show_divs = [];
        if ( 'link' == val ) {
            hide( $( all_color_divs ) );
        }else{
            hide( $( all_color_divs ) );

            if ( 'button' == val ){
                show( $( '#background-color-wrap' ));
                show( $( '#button-color-wrap' ) );
                show_divs = $( '#background-color-wrap, #button-color-wrap' ).children();
                $( '.background-color' ).wpColorPicker();
                $( '.button-color' ).wpColorPicker();
                hide( $( '.test-part .wp-picker-container' ) );

            }else if ( 'button_color' == val ) {
                show( $( '.test-part .wp-picker-container' ) );
                show_divs =  $( '.button-color-test-wrap, .button-color-test-wrap' ).children();

                var pickers = $( '.button-color-test .ingot-color-field' ).find();
                $.each( pickers, function( i, picker ){
                    $( picker ).wpColorPicker();
                });

            }

            $.each( show_divs, function ( i, div) {
                show( div );
            });

            $( '.wp-picker-input-wrap' ).hide();
        }
    })();


    $( document ).on( 'click', '#add-group', function(e) {

        e.preventDefault();

        $.get( INGOT.test_field, {
            _nonce: INGOT.admin_ajax_nonce
        }, function(r) {
            $( '#group-parts' ).prepend( r );
            var id = $( r ).attr( 'id' );

            $( '#remove-' + id ).on( 'click', function(e) {
                var remove = $( this ).data( 'part-id' );
                var el = document.getElementById( remove );
                if( null != el ) {
                    $( el ).remove();
                }
            });
        });


    });

    $( document ).on( 'click', '#delete-all-groups', function(e) {
        e.preventDefault();
        var url;

        if( 'click' == $( this ).attr( 'data-group-type' ) ) {
            var url = INGOT.api_url + '/test-group/1?all=true';
        }else{
            alert( 'fail' );
            return;
        }

        swal( {
                title: INGOT.are_you_sure,
                text: INGOT.delete_confirm,
                type: "warning",
                showCancelButton: true,
                confirmButtonText:INGOT.delete,
                cancelButtonText: INGOT.cancel,
                closeOnConfirm: false,
                closeOnCancel: false
            }, function ( isConfirm ) {
                if ( isConfirm ) {
                    saving = true;
                    $.ajax({
                        url:url,
                        method: "DELETE",
                        success: function( r, textStatus ) {
                            swal( INGOT.deleted, "", "success" ), function() {
                                location.reload();
                            };
                        },
                        always: function(){
                            saving = false;
                        }


                    });

                } else {
                    swal( INGOT.canceled, "", "success" );
                }
            } );
    });

    $( '.part-remove' ).each( function( i, button ){
        $( button ).on( 'click', function(e) {
            swal({
                title: INGOT.beta_error_header,
                text: INGOT.no_stats,
                type: "error",
                confirmButtonText: INGOT.close
            });
        });
    });

    $( document ).on( 'click', '.group-stats', function(e) {
        /**
        swal({
            title: INGOT.beta_error_header,
            text: INGOT.cant_remove,
            type: "error",
            confirmButtonText: INGOT.close
        });
         */
    });

    $( document ).on( 'click', '.group-delete', function(e) {
        id = $( this ).data( 'group-id' );
        var url = INGOT.api_url + '/test-group/' + id;
        saving = true;
        $.ajax({
            url:url,
            method: "DELETE",
            success: function( r, textStatus ) {
                var el = document.getElementById( 'group-' + r );
                if ( null != el ) {
                    $( el ).slideUp( "slow", function() {
                        swal({
                            title: INGOT.deleted,
                            text: '',
                            type: "success",
                            confirmButtonText: INGOT.close
                        });
                        $( el ).remove();
                    });

                }
            }, always : function() {
                saving = false;
            }
        })
    });


    /**
     * Save click test group
     */
    $( document ).on( 'submit', '#ingot-click-test', function( e) {
        saving = true;
        e.preventDefault();
        show( $( '#spinner' ) );
        clear_errors();
        var parts;
        parts = $.find( '.test-part' );

        var  id, _id, create, name, text, part_data, url, current, test, color, background_color;
        var test_ids = {};
        var tests = [];

        $.each( parts, function( i, part ){

            test = {};
            id = 0;
            _id = $( part ).attr( 'id' );
            create = false;

            if ( _id.substring(0,4) == "-ID_") {
                create = true;
            }else{
                id = _id;
            }

            text = $( '#text-'  + _id ).val();
            color = $( '#color-'  + _id ).val();
            background_color = $( '#background-color-' + _id ).val();

            test = {
                text: text,
                button_color: color,
                id: id,
                background_color: background_color
            };

            tests.push( test );
            test_ids[ i ] = id;


        });


        var group_id = $( '#test-group-id' ).val();
        url = INGOT.api_url + '/test-group/';

        if ( 0 < group_id ) {
            url = url + group_id;
        }

        var group_data = {
            type: 'click',
            name : $( '#group-name' ).val(),
            click_type: $( '#group-type' ).val(),
            order: test_ids,
            tests: tests,
            link: $( '#link' ).val(),
            background_color: $( '#background-color' ).val(),
            color: $( '#button-color' ).val()
        };

        $.ajax({
            url: url,
            data: group_data,
            method: 'POST',
            beforeSend: function ( xhr ) {
                xhr.setRequestHeader( 'X-WP-Nonce', INGOT.nonce );
            }

        } ).always(function( r, status ) {
            hide( $( '#spinner' ) );
            if( 'success' == status && 'object' == typeof r ){

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
                //history.pushState( {}, title, new_url );
                saving = false;
                window.location = new_url;
            }else{
                saving = false;
                swal({
                    title: INGOT.fail,
                    text: INGOT.fail,
                    type: "error",
                    confirmButtonText: INGOT.close
                });
            }

            saving = false;
        });


    });

    $( document ).on( 'submit', '#ingot-settings', function(e) {
        e.preventDefault();
        saving = true;
        $( '#ingot-settings-spinner' ).show().css( 'visibility', 'visible' ).attr( 'aria-hidden', 'false' );
        var data = {
            click_tracking: $( '#click_tracking' ).val(),
            anon_tracking: $( '#anon_tracking' ).val(),
            license_code: $( '#license_code' ).val(),
            _nonce: INGOT.admin_ajax_nonce,
            action: 'ingot_settings'
        };
        $.ajax({
            url: INGOT.admin_ajax,
            method: "POST",
            data: data,
            complete: function() {
                $( '#ingot-settings-spinner' ).hide().css( 'visibility', 'hidden' ).attr( 'aria-hidden', 'true' );
                saving = false;
            }

        });

    });

    /**
     * New Price Test
     */
    $( "#group-plugin" ).change(function() {
        var plugin = $( this ).val();
        var data = {
            plugin: plugin,
            _nonce: INGOT.admin_ajax_nonce,
            action: 'get_all_products'
        };

        var el = document.getElementById( 'group-product_ID-group' );
        $( el ).css( 'visibility', 'visible' ).attr( 'aria-hidden', 'false' ).show();
        $( '<img/>', {
            id: 'products-loading-spinner',
            src: INGOT.spinner_url,
            alt: INGOT.spinner_alt,
        }).appendTo( el );

        $.ajax( {
               method: 'GET',
                data: data,
                url: INGOT.admin_ajax,
                complete: function( response, status ){
                    if( 'success' == status ) {
                        var $el = $("#group-product_ID");
                        $el.empty();
                        var newOptions = JSON.parse( response.responseText );

                        $.each(newOptions, function(value,key) {
                            $el.append($("<option></option>")
                                .attr("value", value).text(key));
                        });

                    }
                    $( '#products-loading-spinner' ).remove();
                }

            }
        )
    });

    //create new group
    $( '#ingot-price-test-new' ).submit( function(e) {
        e.preventDefault();
        $( '#spinner' ).css( 'visibility', 'visible' ).attr( 'aria-hidden', 'false' ).show();
        var data = {
            group_name: $( '#group-name' ).val(),
            plugin: $( '#group-plugin' ).val(),
            type: 'price',
            product_ID: $( '#group-product_ID' ).val()
        };

        var url = INGOT.api_url + '/price-group/';

        $.when(
            $.ajax( {
                    url: url,
                    data: data,
                    method: "POST",
                    beforeSend: function ( xhr ) {
                        xhr.setRequestHeader( 'X-WP-Nonce', INGOT.nonce );
                    },
                }
            )
        ).then( function( response, textStatus, xhr )  {
                $( '#spinner' ).css( 'visibility', 'hidden' ).attr( 'aria-hidden', 'true' ).hide();
                if ( 'success' == textStatus ) {

                    var group_id = response.ID;
                    swal( {
                        title: INGOT.success,
                        text: INGOT.saved + response.group_name,
                        type: "success",
                        confirmButtonText: INGOT.close
                    } );
                    var data = {
                        _nonce: INGOT.admin_ajax_nonce,
                        action: 'get_price_group_page',
                        group_id: group_id
                    };

                    $.ajax( {
                        method: "get",
                        url: INGOT.admin_ajax,
                        data: data,
                        complete: function ( r, status ) {
                            $( '#outer-loading-spinner' ).remove();
                            if ( 'success' == status ) {
                                $( outer_wrap ).html( r.responseText );
                                var href = INGOT.price_test_group_link + '&group_id=' + response.ID
                                    history.replaceState( {}, 'Ingot', href );
                            }
                        }

                    } )
                }
        });
    });

    //price test options
    var price_test_chooser = document.getElementById( 'price-tests-chooser' );
    if( null != price_test_chooser ){
        var data = {
            group_id: $( '#test-group-id' ).val(),
            _nonce: INGOT.admin_ajax_nonce,
            action: 'get_price_tests_by_group'
        };

        $.ajax( {
                method: 'GET',
                data: data,
                url: INGOT.admin_ajax,
                complete: function( response, status ){
                    if( 'success' == status && false != response.responseText.success ) {
                        var $el = $( price_test_chooser);
                        $el.empty();
                        var newOptions = JSON.parse( response.responseText.data );
                        console.log(  newOptions );

                        $.each(newOptions, function(value,key) {
                            $el.append($("<option></option>")
                                .attr("value", value).text(key));
                        });

                    }else{
                        show( '#no-tests' );
                        $( '#price-tests-chooser' ).prop('disabled', true);
                    }
                }

            }
        );
    }

    //add a price test field price group editor
    $( document ).on( 'click', '#add-price-test', function(e){
        var data = {
            plugin: $( '#test-group-plugin' ).val(),
            _nonce: INGOT.admin_ajax_nonce,
            action: 'get_price_ab_field'
        };

        saving = true;

        $.ajax( {
                method: 'GET',
                data: data,
                url: INGOT.admin_ajax,
                complete: function( response, status ){
                    if( 'success' == status && false != response.responseText.success ) {
                        var id =  Math.random().toString(36).substring(7);
                        id = 'new_' + id;
                        $( '<div/>', {
                            id: id,
                            class: 'price-test'
                        }).appendTo('#price-tests' );
                        var new_el = document.getElementById( id );
                        new_el.innerHTML = response.responseText;



                    }else{

                    }
                },
                always: function(){
                    saving = false;
                }

            }


        );


    });

    //save price test group
    $( '#ingot-price-test-group' ).submit( function(e) {
        e.preventDefault();
        clear_errors();

        var group_id = $( '#test-group-id' ).val();
        var product_id = $( '#test-product-id' ).val();
        var tests_new = [];
        var tests_update = [];
        var test_id_update_map = [];
        var test_divs = $( "#price-tests" ).find( '.price-test' );

        var test_id, a_val, b_val, test, a_div_id, b_div_id;
        var invalid = [];
        $.each( test_divs, function ( i, div ) {
            test_id = $( div ).find( '.test-id' ).attr( 'data-test-id' );
            a_div_id = test_id + '-a';
            b_div_id = test_id + '-b';
            a_val = $( '#' + a_div_id ).val();
            b_val = $( '#' +  b_div_id ).val();
            if( a_val < -1 || a_val > 1 ){
                invalid.push( a_div_id );
                add_error( '#' + a_div_id, INGOT.invalid_price_test_range );
            }

            if( b_val < -1 || b_val > 1 ){
                add_error( '#' + b_div_id, INGOT.invalid_price_test_range );
            }


            test = {
                product_ID: product_id,
                default: {
                    a: a_val,
                    b: b_val
                },
                ID: test_id
            };


            if ( 1 == is_new( test_id ) ) {
                delete test.ID;
                tests_new.push( test );
            } else {
                tests_update.push( test );
            }

        } );

        if( 0 != invalid.length ){
            return;
        }

        var tests = [];

        var data = {
            group_name: $( '#group-name' ).val(),
            initial: $( '#initial' ).val(),
            threshold: $( '#threshold' ).val(),
            product_ID: product_id,
            plugin: $( '#test-group-plugin' ).val(),
            tests_new: tests_new,
            tests_update: tests_update,
            type: 'price'
        };

        if( 0 == data.tests_update.length ) {
            delete data.tests_update;
        }

        if( 0 == data.tests_new.length ) {
            delete data.tests_new;
        }

        var url = INGOT.api_url + '/price-group/' + group_id;
        $.when( $.ajax( {
            url: url,
            data: data,
            method: 'POST',
            beforeSend: function ( xhr ) {
                xhr.setRequestHeader( 'X-WP-Nonce', INGOT.nonce );
            },
        } ).then( function ( r ) {
            swal( INGOT.success, "", "success" ), function() {

            };
            window.location = window.location.href;

        } ) );


    });

    //disable Price testing in main admin if not enabled
    if( false == INGOT.price_tests_enabled ){
        show( $( '#price-tests-disabled' ) );
        $( '#new-price-group, #all-price-group' ).attr( 'href', '' ).removeClass( 'button-secondary' ).addClass( 'button-disabled' );
    }else{
        $( '#price-tests-disabled' ).remove();
    }



    $( document ).ajaxComplete(function() {
        if ( true != saving) {
            click_ui_hide_shows();
        }
    });


    /**FUNCTIONS**/

    function add_error( div, message ){
        var parent = $( div ).parent();
        $( div ).addClass( 'ingot-has-error' );
        $( '<div/>', {
            class: 'ingot-error'
        }).html( message ).append( parent  );
    }

    function clear_errors() {
        $( $( outer_wrap ).find( '.ingot-has-error' ), function ( i, div ) {
            $( div ).removeClass( 'ingot-has-error' );
        } );

        $( outer_wrap ).find( '.ingot-error' ).remove();
    }

    /**
     * Test if a div id represents a new item
     * @param str
     * @returns {number}
     */
    function is_new( str ){
        if ( 'string' == typeof str ) {
            if ( 'new_' == str.substring( 0, 4 ) ) {
                return 1;
            }
        } else {
            console.log( str );
        }
    }

    /**
     * Hide an element
     *
     * @param el
     */
    function hide( el ){
        $( el ).css( 'visibility', 'hidden' ).attr( 'aria-hidden', 'true' ).hide();
    }

    /**
     * Show an element
     *
     * @param el
     */
    function show( el ){
        $( el ).css( 'visibility', 'visible' ).attr( 'aria-hidden', 'false' ).show();
    }

} );
