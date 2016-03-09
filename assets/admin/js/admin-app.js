/**
 * Create app
 *
 * @since 2.0.0
 */
var ingotApp = angular.module('ingotApp', [
    'ui.router',
    'ui.bootstrap',
    'colorpicker.module',
    'ngAria',
    'ngResource',
    'ngclipboard',
    'ngSanitize'
] )
    .run( function( $rootScope, $state ) {
        $rootScope.translate =  INGOT_TRANSLATION;
        $rootScope.partials_url = INGOT_ADMIN.partials;

		$rootScope.$on('$stateChangeSuccess', function(event, toState, toParams, fromState, fromParams){

			if( toState.name == 'clickTests' ) {
				$rootScope.main_click_tests_page = true;
			} else {
				$rootScope.main_click_tests_page = false;
			}

            if( toState.name == 'priceTests' ) {
                $rootScope.main_price_tests_page = true;
            } else {
                $rootScope.main_price_tests_page = false;
            }

		});
		$rootScope.isActiveNav = function( page ) {

			if( !$state.current.name ) { return }
			if( $state.current.name.indexOf( page ) >= 0 ) {
				return 'active';
			}

		}

    }
);

/**
 * Router
 *
 * @since 2.0.0
 */
ingotApp.config(function($stateProvider, $urlRouterProvider) {

    $urlRouterProvider.otherwise("/");
    $stateProvider
        //click tests
        .state('clickTests', {
            url: "/click-tests",
            templateUrl: INGOT_ADMIN.partials + "/click-groups.html"
        })
        .state('clickTests.list', {
            url: "/click-tests/all",
            templateUrl: INGOT_ADMIN.partials + "/list.html",
            controller: 'clickGroups'
        } )
        .state('clickTests.edit', {
            url: "/click-tests/edit/:groupID",
            templateUrl: INGOT_ADMIN.partials + "/click-group.html",
            controller: 'clickGroup'
        } )
        .state('clickTests.new', {
            url: "/click-tests/new",
            templateUrl: INGOT_ADMIN.partials + "/click-group.html",
            controller: 'clickGroup'
        } ).state('clickTests.delete', {
            url: "/click-tests/delete/:groupID",
            controller: 'clickDelete'
        }).state('clickTests.stats', {
            url: "/click-tests/stats/:groupID",
            templateUrl: INGOT_ADMIN.partials + "/click-group.stats.html",
            controller: 'clickStats'
        } )


        //other
        .state('settings', {
            url: "/settings",
            templateUrl: INGOT_ADMIN.partials + "/settings.html",
            controller: 'settings'
        } )
        .state('support', {
            url: "/support",
            templateUrl: INGOT_ADMIN.partials + "/support.html",
            controller: 'support'
        } )
        .state("otherwise",{
            url : '/',
            templateUrl: INGOT_ADMIN.partials + "/welcome.html",
            controller: 'welcome'
        });

        //price tests
        if( true == INGOT_ADMIN.dev_mode ){

            $stateProvider.state( 'priceTests', {
                url: "/price-tests",
                templateUrl: INGOT_ADMIN.partials + "/price-groups.html",
                controller: 'priceGroups'
            } )
            .state( 'priceTests.list', {
                url: "/price-tests/all",
                templateUrl: INGOT_ADMIN.partials + "/list.html",
                controller: 'priceGroups'
            } )
            .state( 'priceTests.edit', {
                url: "/price-tests/edit/:groupID",
                templateUrl: INGOT_ADMIN.partials + "/price-group.html",
                controller: 'priceGroup'
            } )
            .state( 'priceTests.new', {
                url: "/price-tests/new/",
                templateUrl: INGOT_ADMIN.partials + "/price-group.html",
                controller: 'priceGroup'
            } );
        }else {
            var coming_soon = '<div class="alert alert-info"><h1>' + INGOT_TRANSLATION.price_coming_soon + '</h1></div>';
            $stateProvider.state( 'priceTests', {
                url: "/price-tests",
                template: coming_soon
            } )
            .state( 'priceTests.list', {
                url: "/price-tests/all",
                templateUrl: INGOT_ADMIN.partials + "/price-nope.html",
            } )
            .state( 'priceTests.edit', {
                url: "/price-tests/edit/:groupID",
                templateUrl: INGOT_ADMIN.partials + "/price-nope.html",

            } )
            .state( 'priceTests.new', {
                url: "/price-tests/new/",
                templateUrl: INGOT_ADMIN.partials + "/price-nope.html",
            } );
        }



});


/**Click Tests */

/**
 * Controller for click groups list
 *
 * @since 0.2.0
 */
ingotApp.controller( 'clickGroups', ['$scope', '$http', 'groupsFactory', '$sce', function( $scope, $http,  groupsFactory, $sce ) {

    var page_limit = 10;

    $scope.description = $sce.trustAsHtml( INGOT_TRANSLATION.descriptions.click );

    groupsFactory.query( {
            page: 1,
            limit: page_limit,
            context: 'admin',
            type: 'click'
        }, function ( res ) {
        if ( res.data.indexOf( 'No matching' ) > -1 ) {
            $scope.groups = {};
            return;

        };

        $scope.groups = JSON.parse( res.data );
        var total_groups = parseInt( res.headers[ 'x-ingot-total' ] );
        var total_pages = total_groups / page_limit;
        $scope.total_pages = new Array( Math.round( total_pages ) );
        $scope.groups.shortcode = [];
    } );

    $scope.paginate = function( page, $event ) {

	    if( jQuery('.paginate a.active').length ) {
            jQuery('.paginate a.active').toggleClass('active');
        }

	    jQuery( $event.currentTarget ).toggleClass('active');

	    page = page + 1;
        groupsFactory.query({
            page: page, limit: page_limit, context: 'admin'
        }, function(res){
			if( res.data.indexOf('No matching groups found.') >= 0 ) {
                return;
            }
		    $scope.groups = JSON.parse( res.data );
	    });
    };

    $scope.enter = function( id ) {
        setTimeout( function () {
            jQuery( '#shortcode-pre-' + id ).hide().attr( 'aria-hidden', 'true' ).css( 'visibility', 'hidden' );
            jQuery( '#shortcode-copy-' + id ).show().attr( 'aria-hidden', 'false' ).css( 'visibility', 'visible' );
        }, 350 );
    };

    $scope.exit = function( id ){
        setTimeout( function() {
            jQuery( '#shortcode-copy-' + id ).hide().attr( 'aria-hidden', 'true' ).css( 'visibility', 'hidden' );
            jQuery('#shortcode-pre-' + id ).show().attr( 'aria-hidden', 'false' ).css( 'visibility', 'visible' );
        }, 350 );
    };

}]);

/**
 * Controller for deleting a group
 *
 * @since 0.2.0
 */
ingotApp.controller( 'clickDelete', ['$scope', '$http', '$stateParams', '$state', function( $scope, $http, $stateParams, $state ){
    var groupID = $stateParams.groupID;
    if( 'undefined' == groupID ) {
        swal({
            title: INGOT_TRANSLATION.fail,
            text: INGOT_TRANSLATION.sorry,
            type: "error",
            confirmButtonText: INGOT_TRANSLATION.close
        });
        $state.go('clickTests.list' );
    }else{
        swal( {
            title: INGOT_TRANSLATION.are_you_sure,
            text: INGOT_TRANSLATION.delete_confirm,
            type: "warning",
            showCancelButton: true,
            confirmButtonText:INGOT_TRANSLATION.delete,
            cancelButtonText: INGOT_TRANSLATION.cancel,
            closeOnConfirm: false,
            closeOnCancel: false
        }, function ( isConfirm ) {
            if ( isConfirm ) {
                $http({
                    url: INGOT_ADMIN.api + 'groups/' + groupID + '?_wpnonce=' + INGOT_ADMIN.nonce,
                    method:'DELETE',
                    headers: {
                        'X-WP-Nonce': INGOT_ADMIN.nonce
                    }
                } ).then(
                    function successCallback() {
                        swal( INGOT_TRANSLATION.deleted, "", "success" );
                        $scope.group = {};
                        $state.go('clickTests.list' );
                    }, function errorCallback( response ) {
                        var data = response.data;
                        var text = INGOT_TRANSLATION.sorry;
                        if( _.isObject( data ) && typeof( data.message ) !== 'undefined' ){
                            text = data.message;
                        }
                        $state.go('clickTests.list' );
                        swal({
                            title: INGOT_TRANSLATION.fail,
                            text: text,
                            type: "error",
                            confirmButtonText: INGOT_TRANSLATION.close
                        });
                    }
                );

            } else{
                swal( INGOT_TRANSLATION.canceled, "", "success" );
                $state.go('clickTests.list' );
            }
        } );
    }
}]);

/**
 * Controller for creating/editing a click group
 *
 * @since 0.2.0
 */
ingotApp.controller( 'clickGroup', ['$scope', '$http', '$stateParams', '$rootScope', '$state', 'groupsFactory', function( $scope, $http, $stateParams, $rootScope, $state,  groupsFactory ) {
    var is_new = false;
    $scope.group_step = 1;
    $scope.new_group = false;
    $scope.click_type_options = INGOT_ADMIN.click_type_options;
    $scope.destinations = INGOT_ADMIN.destinations;
    $scope.pages = {};

    if( 'clickTests.new' == $state.current.name ) {
        is_new = true;
        $scope.new_group = true;
        $scope.group = {
            type: 'click',
            click_type_options : INGOT_ADMIN.click_type_options,
            variants: {},
            meta: {},
            name: ''
        };
    } else {
        $scope.group_step = 3;
        var groupID = $stateParams.groupID;
        groupsFactory.get({
            id: groupID
        }, function(res){
            if( 'array' == typeof res.meta ) {
                function toObject( arr) {
                    var rv = {};
                    for (var i = 0; i < arr.length; ++i ){
                        if (arr[i] !== undefined) rv[i] = arr[i];
                    }
                    return rv;
                }
                res.meta = toObject( res.meta );
            }
	        $scope.group = res;
            $scope.choose_group_type( $scope.group.sub_type );

		}, function(data, status, headers, config) {
            swal({
                title: INGOT_TRANSLATION.fail,
                text: INGOT_TRANSLATION.sorry,
                type: "error",
                confirmButtonText: INGOT_TRANSLATION.close
            });
        })
    }

    $scope.buttonStyle = function( id, type ) {
        var css = {};
	    if( type == 'button_color' ) {
            if ( $scope.group.variants[ id ] && $scope.group.variants[ id ].meta && $scope.group.variants[ id ].meta.background_color ) {
                css[ 'background-color' ] = $scope.group.variants[ id ].meta.background_color;
            }

            if ( $scope.group.variants[ id ] && $scope.group.variants[ id ].meta && $scope.group.variants[ id ].meta.color ) {
                css[ 'color' ] = $scope.group.variants[ id ].meta.color;
            }

            return css;
        } else {
            css = {};
            if ( $scope.group.meta && $scope.group.meta.background_color ) {
                css[ 'background-color' ] = $scope.group.meta.background_color;
            }

            if ( $scope.group.meta && $scope.group.meta.color ) {
                css[ 'color' ] = $scope.group.meta.color;
            }

            return css;

        }
    };

    $scope.removeTest = function( index ) {
	    delete $scope.group.variants[ index ];
	    return false;
    };

    $scope.submit = function( data ){
        var url;
        if( 'clickTests.new' == $state.current.name ) {
            url =INGOT_ADMIN.api + 'groups/?context=admin';
        }else{
            url = INGOT_ADMIN.api + 'groups/' + groupID + '?context=admin';
        }

        url +='&_wpnonce=' + INGOT_ADMIN.nonce;

        $http({
            method: 'POST',
            headers: {
                'X-WP-Nonce': INGOT_ADMIN.nonce
            },
            url: url,
            data: $scope.group
        } ).then(
            function successCallback( response ) {

                $scope.group = response.data;
                $scope.pages = {};

                if( true === is_new ) {
                    is_new = false;
                    $state.go( 'clickTests.edit', {
                        groupID: $scope.group.ID
                    } );
                }
                swal({
                    title: INGOT_TRANSLATION.group_saved,
                    text: '',
                    type: "success",
                    confirmButtonText: INGOT_TRANSLATION.close
                });
            }, function errorCallback( response ) {
                var data = response.data;
                var text = INGOT_TRANSLATION.sorry;
                if( _.isObject( data ) && typeof( data.message ) !== 'undefined' ){
                    text = data.message;
                }

                swal({
                    title: INGOT_TRANSLATION.fail,
                    text: text,
                    type: "error",
                    confirmButtonText: INGOT_TRANSLATION.close
                });
            }
        );
    };

    //add tests to click group
    $scope.addNewTest = function(e) {
        //make ID a random string so it will be treated as new by API
        var id = 'ingot_' + Math.random().toString(36).substring(7);
        if( jQuery.isEmptyObject( $scope.group.variants ) ) {
            $scope.group.variants = {};
        }

        $scope.group.variants[ id ] = {
            ID : id
        };
    };

    $scope.change_step = function( step ) {

        if ( _.isUndefined( $scope.group.name ) || _.isEmpty( $scope.group.name ) ) {
            swal( {
                title: INGOT_TRANSLATION.group.must_name,
                text: '',
                type: "error",
                confirmButtonText: INGOT_TRANSLATION.close
            } );
            $scope.group_step  = 1;
        } else if ( 2 != step && ( _.isUndefined( $scope.group.sub_type ) || _.isEmpty( $scope.group.sub_type ) ) ) {
            swal( {
                title: INGOT_TRANSLATION.group.must_type,
                text: '',
                type: "error",
                confirmButtonText: INGOT_TRANSLATION.close
            } );

            $scope.group_step = 2;
        } else {
            $scope.group_step = step;
        }


    };

    $scope.choose_group_type = function( type ) {
        $scope.group.sub_type = type;
    };

    $scope.has_type = function() {
        if( !$scope.group ) {
            return;
        }

        if( 'undefined' == $scope.group.sub_type || null == $scope.group.sub_type ){
            return false;
        }

        return true;
    };

    $scope.is_page = function(){
        if ( 'undefined' != $scope.group.meta.destination ) {
            if ( 'page' == $scope.group.meta.destination ) {
                return true;
            }
        }
    };

    $scope.is_hook = function(){
        if ( 'undefined' != $scope.group.meta.destination ) {
            if ( 'hook' == $scope.group.meta.destination ) {
                return true;
            }
        }
    };

    //set select option for destination type
    $scope.destinationSelected = function( destination ){
        if( 'undefined' == $scope.group.meta.destination || 'undefined' == destination.value ){
            return false;
        }
        if( destination.value == $scope.group.meta.destination ){
            return true;
        }
    };

    //show the right description on change of #destination
    $scope.destinationDescription = function(){
        if( !_.isUndefined( $scope.group.meta.destination ) && _.has( INGOT_ADMIN.destinations, $scope.group.meta.destination ) ){
            var description = INGOT_ADMIN.destinations[ $scope.group.meta.destination ];
            var el = document.getElementById( 'destination-description' );
            if ( null != el  ) {
                el.innerHTML = description.description;
            }

        }

    };

    //do live search on the group meta page
    $scope.$watch( 'group.meta.page', function( newValue, oldValue ) {
        if ( newValue != oldValue  ) {
            setTimeout( function() {
                    $http( {
                        url: INGOT_ADMIN.api + 'settings/page-search?search=' + newValue + '&_wpnonce=' + INGOT_ADMIN.nonce + '&context=admin',
                        method: 'GET',
                        headers: {
                            'X-WP-Nonce': INGOT_ADMIN.nonce
                        }
                    } ).then(
                        function successCallback( response ) {
                            $scope.pages = response.data;
                        }
                    );
            }, 500 );
        }
    });

    //hide/show page search results logic
    $scope.showPageSearch = function() {
        return _.isEmpty( $scope.pages );
    };


}]);

/**
 * Controller for group stats
 *
 * @since 0.3.0
 */
ingotApp.controller( 'clickStats', ['$scope', '$rootScope', '$http', '$stateParams', '$state', 'groupsFactory', function( $scope, $rootScope, $http, $stateParams, $state,  groupsFactory ) {

    var groupID = $stateParams.groupID;
    $scope.no_stats = INGOT_TRANSLATION.no_stats;
    $scope.group_id = groupID;

    if ( 'undefined' == groupID ) {

        swal( {
            title: INGOT_TRANSLATION.fail,
            text: INGOT_TRANSLATION.sorry,
            type: "error",
            confirmButtonText: INGOT_TRANSLATION.close
        } );
        $state.go( 'clickTests.list' );

    } else {

        $http({
            url: INGOT_ADMIN.api + 'groups/' + groupID + '/stats?_wpnonce=' + INGOT_ADMIN.nonce + '&context=admin',
            method:'GET',
            headers: {
                'X-WP-Nonce': INGOT_ADMIN.nonce
            }
        } ).then(
            function successCallback( response ){
                var data = response.data;
                $scope.stats_exist = true;
                if( ! Object.keys( data.variants ).length ) {
                    $scope.stats_exist = false;
                    return;
                }

                $scope.stats = data;
                $scope.chart_data = {
                    labels: [],
                    datasets: [
                        {
                            label: [ INGOT_TRANSLATION.stats.c_rate ],
                            fillColor: "rgba(220,220,220,0.5)",
                            strokeColor: "rgba(220,220,220,0.8)",
                            highlightFill: "rgba(220,220,220,0.75)",
                            highlightStroke: "rgba(220,220,220,1)",
                            data: []
                        }
                    ]
                };

                angular.forEach( $scope.stats.variants, function( variant, i ) {
                    var name;

                    if( ! _.isUndefined( variant.name ) && !_.isEmpty( variant.name ) && ' ' != variant.name ) {
                        name = variant.name;
                    }else{
                        name = INGOT_TRANSLATION.stats.variant + ' ' + i;
                    }

                    $scope.chart_data.labels.push( name );
                    var rate = Math.round( variant.conversion_rate * 100 );
                    $scope.chart_data.datasets[0].data.push( rate );
                });


                $scope.stats.group.conversion_rate = Math.round( $scope.stats.group.conversion_rate * 100 );
                $scope.setChart( $scope.stats.group.average_conversion_rate * 100 );

        });

    }

    Chart.types.Bar.extend({
        name: 'BarOverlay',
        draw: function (ease) {

            // First draw the main chart
            Chart.types.Bar.prototype.draw.apply(this);

            var ctx = this.chart.ctx;
            var barWidth = this.scale.calculateBarWidth(this.datasets.length);

            for (var i = 0; i < this.options.verticalOverlayAtBar.length; ++i) {

                var overlayBar = this.options.verticalOverlayAtBar[i];

                // I'm hard-coding this to only work with the first dataset, and using a Y value that I know is maximum
                var x = this.scale.calculateBarX(this.datasets.length, 0, overlayBar);
                var y = this.scale.calculateY(overlayBar);

                var bar_base = this.scale.endPoint;

                ctx.beginPath();
                ctx.lineWidth = 2;
                ctx.strokeStyle = 'rgba(255, 0, 0, 1.0)';
                ctx.moveTo(100, y);
                ctx.lineTo(jQuery('#ingotChart').outerWidth(), y);
                ctx.stroke();
                ctx.font = "14px Arial";
                ctx.fillStyle = 'black';
                ctx.fillText( $rootScope.translate.stats.g_avg_c_rate + '(' + Math.round( overlayBar * 100  ) / 100 + '%)', jQuery('#ingotChart').outerWidth(), y - 10 )
            }
            ctx.closePath();
        }
    });

    $scope.setChart = function( avg ) {
        var ctx = document.getElementById("ingotChart").getContext("2d");
        setTimeout(function(){
            var ingot_chart = new Chart(ctx).BarOverlay( $scope.chart_data, {
                scaleLabel: "          <%=value%>%",
                responsive: true,
                barValueSpacing: 10,
                verticalOverlayAtBar: [ avg ]
            } );
        }, 100);

    }

}]);

/** Price Tests*/

/**
 * Controller for price groups list
 *
 * @since 2.0.0
 */
ingotApp.controller( 'priceGroups', ['$scope', '$http', 'groupsFactory', function( $scope, $http, groupsFactory ) {
    var page_limit = 10;
    $http({
        url: INGOT_ADMIN.api + 'products/plugins?context=list&_wpnonce=' + INGOT_ADMIN.nonce,
        method: 'GET',
        headers: {
            'X-WP-Nonce': INGOT_ADMIN.nonce
        }

    }).then(
        function successCallback( data, status, headers, config ) {
            $scope.plugins = response.data;
            $scope.possible = false;
            angular.forEach( data, function ( value, key ) {
                if ( false == $scope.possible ) {
                    if ( true == value.active ) {
                        $scope.possible = true;
                    }
                }
            } );
        }, function errorCallback(response) {
            console.log( response );
        }
    );

    groupsFactory.query({page: 1, limit: page_limit, context: 'admin', type: 'price' }, function(res){
		$scope.total_pages = false;
		$scope.groups = JSON.parse( res.data );
		if( res.headers['x-ingot-total'] ) {
			var total_groups = parseInt( res.headers['x-ingot-total'] );
			total_pages = total_groups / page_limit;
			$scope.total_pages = new Array( Math.round( total_pages ) );
		}

    });

    $scope.paginate = function( page, $event ) {
        if( jQuery('.paginate a.active').length ) {
            jQuery('.paginate a.active').toggleClass('active');
        }

        jQuery( $event.currentTarget ).toggleClass('active');

        page = page + 1;
        groupsFactory.query({
            page: page, limit: page_limit, context: 'admin'
        }, function(res){
            if( res.data.indexOf('No matching groups found.') >= 0 ) {
                return;
            }

            $scope.groups = JSON.parse( res.data );
        });
    }

}]);

/**
 * Controller for creating/editing a price group
 *
 * @since 0.2.0
 */
ingotApp.controller( 'priceGroup', ['$scope', '$http', '$stateParams', '$rootScope', '$state', 'groupsFactory', function( $scope, $http, $stateParams, $rootScope, $state, groupsFactory ) {
    var newGroup = false;
    if ( 'priceTests.new' == $state.current.name ) {
        newGroup = true;
    }

    $http({
        url: INGOT_ADMIN.api + 'products/plugins?_wpnonce=' + INGOT_ADMIN.nonce,
        method: 'GET',
        headers: {
            'X-WP-Nonce': INGOT_ADMIN.nonce
        }

    }).then(
        function successCallback( response ) {
            $scope.plugins = response.data;
        }
    );

    if( newGroup ) {
        $scope.group = {
            type: 'price',
            price_type_options : INGOT_ADMIN.price_type_options
        };
    }else{
        var groupID = $stateParams.groupID;
        groupsFactory.get({
            id: groupID,
        }, function(res){
	        if( res[0] != 'N' && res[1] != 'o' ) {
		        $scope.group = res;
	            $scope.products();
	        }
        }, function(data, status, headers, config) {
            console.log( data );
            swal({
                title: INGOT_TRANSLATION.fail,
                text: INGOT_TRANSLATION.sorry,
                type: "error",
                confirmButtonText: INGOT_TRANSLATION.close
            });
        })
    }

    $scope.getBasePrice = function(){
        var ID = $scope.group.meta.product_ID;

        $http({
            url: INGOT_ADMIN.api + 'products/price/' + ID + '?plugin=' + $scope.group.sub_type + '&_wpnonce=' + INGOT_ADMIN.nonce,
            method: 'GET',
            headers: {
                'X-WP-Nonce': INGOT_ADMIN.nonce
            }

        }).then(
            function successCallback ( response ) {
                $scope.basePrice = parseFloat( response.data.price );
            }
        )

    };

    $scope.submit = function( ){
        angular.forEach( $scope.group.variants, function( value, key ) {
	        value.default = parseFloat( value.default / 100 );
        });

        $scope.group.type = 'price';

        groupsFactory.save( $scope.group, function ( res ) {
            console.log( res );

            if ( newGroup ) {
                alert( res.ID );
                $state.go( 'priceTests.edit', {
                    groupID: res.ID
                } );
            }
            swal( {
                title: INGOT_TRANSLATION.group_saved,
                text: '',
                type: "success",
                confirmButtonText: INGOT_TRANSLATION.close
            } );
        }, function ( error ) {
            console.log( error );
            swal( {
                title: INGOT_TRANSLATION.fail,
                text: INGOT_TRANSLATION.sorry,
                type: "error",
                confirmButtonText: INGOT_TRANSLATION.close
            } );
        } )

    };

    $scope.addNewTest = function(e) {
        //make ID a random string so it will be treated as new by API
        var id = 'ingot_' + Math.random().toString(36).substring(7);
        if( !$scope.group.variants ) {
            $scope.group.variants = [];
        }
        $scope.group.variants.push({ 'ID': id, default: 0, price: $scope.basePrice });

		setTimeout( function() {
			jQuery(".slider-" + id).slider({
				value:0,
				min: -99,
				max: 99,
				slide: function( event, ui ) {
                    var index = jQuery(event.target).data( 'index' );
                    var value = ui.value;
					$scope.group.variants[ index ].default = value;
					jQuery(".slider-" + id + "-val").html( value + '%' );

                    if ( 0 == parseFloat( value ) ) {
                        $scope.group.variants[ index ].price = $scope.basePrice;
                    }else{
                        $scope.group.variants[ index ].price = $scope.basePrice * ( value / 100 );
                    }

                    $scope.group.variants[ index ].price.meta = value;

				}

			});
			$scope.$apply();
		}, 50 );

    };

    $scope.products = function() {
        if( 'string' != typeof $scope.group.sub_type ){
            $scope.products = {};
        }
        $http({
            url: INGOT_ADMIN.api + 'products?plugin=' + $scope.group.sub_type + '&_wpnonce=' + INGOT_ADMIN.nonce,
            method: 'GET',
            headers: {
                'X-WP-Nonce': INGOT_ADMIN.nonce
            }

        }).then(
            function successCallback( response, status, headers, config ) {
                $scope.products = response.data;
            }
        )
    }

}]);

/**
 * Support Controller
 *
 * @since 0.2.0
 */
ingotApp.controller( 'support', ['$scope', '$http', function( $scope, $http ) {
}]);

/**
 * Welcome Page Controller
 *
 * @since 0.2.0
 */
ingotApp.controller( 'welcome', ['$scope', '$http', function( $scope, $http ) {
    $scope.welcome = INGOT_TRANSLATION.welcome;

    //temporary for #61, #62 should make this uneed
    var url =  INGOT_ADMIN.api + 'settings?context=admin&_wpnonce=' + INGOT_ADMIN.nonce;
    $http({
        method: 'GET',
        url:url,
        headers: {
            'X-WP-Nonce': INGOT_ADMIN.nonce
        }

    }).then(
        function successCallback( data, status, headers, config ) {
            $scope.welcome.settings = data;
        }
    );

}]);


/**
 * Settings Controller
 *
 * @since 0.2.0
 */
ingotApp.controller( 'settings', ['$scope', '$http', function( $scope, $http ) {
    var url =  INGOT_ADMIN.api + 'settings?context=admin&_wpnonce=' + INGOT_ADMIN.nonce;
    $http({
        method: 'GET',
        url:url,
        headers: {
            'X-WP-Nonce': INGOT_ADMIN.nonce
        }

    }).then(
        function successCallback( response ) {
            $scope.settings = response.data;
        }, function errorCallback( response ) {
            var text = INGOT_TRANSLATION.sorry;
            if ( _.isObject( response ) && typeof( response.data.message ) !== 'undefined' ) {
                swal( {
                    title: INGOT_TRANSLATION.fail,
                    text: response.data.message,
                    type: "error",
                    confirmButtonText: INGOT_TRANSLATION.close
                } );
            }
        }
    );

    $scope.submit = function(){
        $http({
            method: 'POST',
            headers: {
                'X-WP-Nonce': INGOT_ADMIN.nonce
            },
            url: url,
            data: $scope.settings
        } ).then(
            function successCallback( response ) {
                $scope.settings =  response.data;
                    swal({
                        title: INGOT_TRANSLATION.settings_saved,
                        text: '',
                        type: "success",
                        confirmButtonText: INGOT_TRANSLATION.close
                    });
            }, function errorCallback( response ) {
                var data = response.data;
                var text = INGOT_TRANSLATION.sorry;
                if( _.isObject( data ) && typeof( data.message ) !== 'undefined' ){
                    text = data.message;
                }
                swal({
                    title: INGOT_TRANSLATION.fail,
                    text: text,
                    type: "error",
                    confirmButtonText: INGOT_TRANSLATION.close
                });

        })
    };

}]);

/**
 * Groups Factory
 *
 * @since 1.1.0
 * @since 0.2.0 as clickGroups
 */
ingotApp.factory( 'groupsFactory', function( $resource ) {

	return $resource( INGOT_ADMIN.api + 'groups/:id', {
		id: '@id',
        _wpnonce: INGOT_ADMIN.nonce,
        context: 'admin'
	},{
		'query' : {
			transformResponse: function( data, headers ) {
				var response = {
					data: data,
					headers: headers()
				};
				return response;
			}
		},
        'update':{
            method:'PUT',
            headers: {
                'X-WP-Nonce': INGOT_ADMIN.nonce
            }
        },
        'post':{
            method:'POST',
            headers: {
                'X-WP-Nonce': INGOT_ADMIN.nonce
            }
        },
        'save':{
            method:'POST',
            headers: {
                'X-WP-Nonce': INGOT_ADMIN.nonce
            }
        },
        'delete':{
            method:'DELETE',
            headers: {
                'X-WP-Nonce': INGOT_ADMIN.nonce
            }
        }
    })

});
