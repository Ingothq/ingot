/**
 * Create app
 *
 * @since 2.0.0
 */
var ingotApp = angular.module('ingotApp', [
    'ui.router',
    'ui.bootstrap',
    'colorpicker.module',
    'pascalprecht.translate',
    'ngAria',
    'ngResource'
] )
    .run( function( $rootScope, $state ) {
		
		$rootScope.$on('$stateChangeSuccess', function(event, toState, toParams, fromState, fromParams){
			
			if( toState.name == 'clickTests' ) {
				$rootScope.main_click_tests_page = true;
			} else {
				$rootScope.main_click_tests_page = false;
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
            templateUrl: INGOT_ADMIN.partials + "/click-groups.list.html",
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
        })
        //price tests
        .state('priceTests', {
            url: "/price-tests",
            templateUrl: INGOT_ADMIN.partials + "/price-groups.html",
            controller: 'priceGroups'
        })
        .state('priceTests.list', {
            url: "/price-tests/all",
            templateUrl: INGOT_ADMIN.partials + "/price-groups.list.html",
            controller: 'priceGroups'
        } )
        .state('priceTests.edit', {
            url: "/price-tests/edit/:groupID",
            templateUrl: INGOT_ADMIN.partials + "/price-group.html",
            controller: 'priceGroup'
        } )
        .state('priceTests.new', {
            url: "/price-tests/new/",
            templateUrl: INGOT_ADMIN.partials + "/price-group.html",
            controller: 'priceGroup'
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
        })



});

/**
 * Translation
 *
 * @since 2.0.0
 */
ingotApp.config(['$translateProvider', function ($translateProvider) {
    //@todo not make always english
    $translateProvider
        .translations('en', INGOT_TRANSLATION)
        .preferredLanguage('en')
        .useSanitizeValueStrategy('escape');
}]);


/**
 * Click Tests
 *
 * @since 2.0.0
 */
//Controller for click groups list
ingotApp.controller( 'clickGroups', ['$scope', '$http', 'clickGroups', function( $scope, $http, clickGroups ) {
    
    var page_limit = 10;
    
    clickGroups.query({page: 1, limit: page_limit, context: 'admin'}, function(res){
	    
	    $scope.groups = JSON.parse( res.data );
	    
	    var total_groups = parseInt( res.headers['x-ingot-total'] );
	    total_pages = total_groups / page_limit;
	    $scope.total_pages = new Array( Math.round( total_pages ) );

    });
    
    $scope.paginate = function( page, $event ) {

	    if( jQuery('.paginate a.active').length ) {
            jQuery('.paginate a.active').toggleClass('active');
        }
        
	    jQuery( $event.currentTarget ).toggleClass('active');
	    
	    page = page + 1;
		clickGroups.query({page: page, limit: page_limit, context: 'admin'}, function(res){
			if( res.data.indexOf('No matching groups found.') >= 0 ) { return; }
		    $scope.groups = JSON.parse( res.data );
	    });   
    }
    
}]);

//controller for deleting a group
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
                    url: INGOT_ADMIN.api + 'test-group/' + groupID + '&_wp_rest_nonce=' + INGOT_ADMIN.nonce,
                    method:'DELETE',
                    headers: {
                        'X-WP-Nonce': INGOT_ADMIN.nonce
                    }
                } ).success( function(){
                    swal( INGOT_TRANSLATION.deleted, "", "success" );
                    $scope.group = {};
                    $state.go('clickTests.list' );
                } ).error( function( data ) {
                    console.log( data );
                    $state.go('clickTests.list' );
                    swal({
                        title: INGOT_TRANSLATION.fail,
                        text: INGOT_TRANSLATION.sorry,
                        type: "error",
                        confirmButtonText: INGOT_TRANSLATION.close
                    });
                });

            } else{
                swal( INGOT_TRANSLATION.canceled, "", "success" );
                $state.go('clickTests.list' );
            }
        } );
    }

}]);

//controller for creating/editing a click group
ingotApp.controller( 'clickGroup', ['$scope', '$http', '$stateParams', '$rootScope', '$state', 'clickGroups', function( $scope, $http, $stateParams, $rootScope, $state, clickGroups ) {
    if( 'clickTests.new' == $state.current.name ) {
        $scope.group = {
            click_type_options : INGOT_ADMIN.click_type_options,
            tests: {}
        };
    } else {
        var groupID = $stateParams.groupID;


		clickGroups.get({id: groupID}, function(res){
	       $scope.group = res;
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

        $scope.submit = function( data ){
            var url;
            if( 'clickTests.new' == $state.current.name ) {
                url =INGOT_ADMIN.api + 'test-group/?context=admin';
            }else{
                url = INGOT_ADMIN.api + 'test-group/' + groupID + '?context=admin';
            }

            url +='&_wp_rest_nonce=' + INGOT_ADMIN.nonce;

            $http({
                method: 'POST',
                headers: {
                    'X-WP-Nonce': INGOT_ADMIN.nonce
                },
                url: url,
                data: $scope.group
            } ).success(function(data) {
                $scope.group = data;
                if( 'clickTests.new' == $state.current.name ) {

                    $state.go('clickTests.edit', { groupID: data.ID } );
                }
                swal({
                    title: INGOT_TRANSLATION.group_saved,
                    text: '',
                    type: "success",
                    confirmButtonText: INGOT_TRANSLATION.close
                });
            } ).error(function(){
                swal({
                    title: INGOT_TRANSLATION.fail,
                    text: INGOT_TRANSLATION.sorry,
                    type: "error",
                    confirmButtonText: INGOT_TRANSLATION.close
                });
            })
        };

        $scope.addNewTest = function(e) {
            //make ID a random string so it will be treated as new by API
            var id = Math.random().toString(36).substring(7);
            $scope.group.tests[ id ] = {'ID':id};
        };


}]);

/**
 * Price Tests
 *
 * @since 2.0.0
 */
//Controller for price groups list
ingotApp.controller( 'priceGroups', ['$scope', '$http', function( $scope, $http ) {
    swal({
        title: INGOT_TRANSLATION.beta_error_header,
        text: '',
        type: "error",
        confirmButtonText: INGOT_TRANSLATION.close
    });

    return;

    $http({
        method: 'GET',
        url: INGOT_ADMIN.api + 'price-group&_wp_rest_nonce=' + INGOT_ADMIN.nonce

    }).success( function( data, status, headers, config ) {
        console.log( data );
        $scope.groups = data;
    }).error(function(data, status, headers, config) {
        console.log( data );
    });
}]);

//controller for creating/editing a price group
ingotApp.controller( 'priceGroup', ['$scope', '$http', '$stateParams', '$rootScope', '$state', function( $scope, $http, $stateParams, $rootScope, $state ) {
    swal({
        title: INGOT_TRANSLATION.beta_error_header,
        text: '',
        type: "error",
        confirmButtonText: INGOT_TRANSLATION.close
    });

    return;


    if( 'priceGroup.new' == $state.current.name ) {
        $scope.group = {
            price_type_options : INGOT_ADMIN.price_type_options
        };
    }else{
        var groupID = $stateParams.groupID;
        $http({
            method: 'GET',
            url: INGOT_ADMIN.api + 'price-group/' + groupID + '?context=admin&_wp_rest_nonce=' + INGOT_ADMIN.nonce

        }).success( function( data, status, headers, config ) {

            $scope.group = data;

        }).error(function(data, status, headers, config) {
            console.log( data );
            swal({
                title: INGOT_TRANSLATION.fail,
                text: INGOT_TRANSLATION.sorry,
                type: "error",
                confirmButtonText: INGOT_TRANSLATION.close
            });
        });
    }

    $scope.submit = function( ){
        var url;
        if( 'priceGroup.new' == $state.current.name ) {
            url =INGOT_ADMIN.api + 'price-group/?context=admin';
        }else{
            url = INGOT_ADMIN.api + 'price-group/' + groupID + '?context=admin';
        }

        url += '&_wp_rest_nonce=' + INGOT_ADMIN.nonce;

        $http({
            method: 'POST',
            headers: {
                'X-WP-Nonce': INGOT_ADMIN.nonce
            },
            url: url,
            data: $scope.group
        } ).success(function(data) {
            $scope.group = data;
            if( 'priceGroup.new' == $state.current.name ) {
                $state.go('priceGroup.edit' ).toParams({
                    groupID: data.ID
                });
            }
            swal({
                title: INGOT_TRANSLATION.group_saved,
                text: '',
                type: "success",
                confirmButtonText: INGOT_TRANSLATION.close
            });
        } ).error(function(){
            swal({
                title: INGOT_TRANSLATION.fail,
                text: INGOT_TRANSLATION.sorry,
                type: "error",
                confirmButtonText: INGOT_TRANSLATION.close
            });
        })
    };

}]);

/**
 * Support Controller
 *
 * @since 0.2.0
 */
ingotApp.controller( 'support', ['$scope', '$http', function( $scope, $http ) {
    swal({
        title: INGOT_TRANSLATION.beta_error_header,
        text: '',
        type: "error",
        confirmButtonText: INGOT_TRANSLATION.close
    });
}]);

/**
 * Welcome Page Controller
 *
 * @since 0.2.0
 */
ingotApp.controller( 'welcome', ['$scope', '$http', function( $scope, $http ) {
    $scope.welcome = INGOT_TRANSLATION.welcome;
}]);


/**
 * Settings Controller
 *
 * @since 0.2.0
 */
ingotApp.controller( 'settings', ['$scope', '$http', function( $scope, $http ) {
    var url =  INGOT_ADMIN.api + 'settings&_wp_rest_nonce=' + INGOT_ADMIN.nonce;
    $http({
        method: 'GET',
        url:url,
        headers: {
            'X-WP-Nonce': INGOT_ADMIN.nonce
        },

    }).success( function( data, status, headers, config ) {
        $scope.settings = data;
    }).error(function( data ){
        console.log( data );
    });

    $scope.submit = function(){
        $http({
            method: 'POST',
            headers: {
                'X-WP-Nonce': INGOT_ADMIN.nonce
            },
            url: url,
            data: $scope.settings
        } ).success(function(data) {
            $scope.settings =  data;
            swal({
                title: INGOT_TRANSLATION.settings_saved,
                text: '',
                type: "success",
                confirmButtonText: INGOT_TRANSLATION.close
            });
        } ).error(function(){
            swal({
                title: INGOT_TRANSLATION.fail,
                text: INGOT_TRANSLATION.sorry,
                type: "error",
                confirmButtonText: INGOT_TRANSLATION.close
            });
        })
    };

}]);

/**
 * Test Groups Factory
 *
 */
ingotApp.factory( 'clickGroups', function( $resource ) {

	return $resource( INGOT_ADMIN.api + 'test-group/:id', {
		id: '@id',
        _wp_rest_nonce: INGOT_ADMIN.nonce,
        context: 'admin'
	},{
		'query' : {
			transformResponse: function( data, headers ) {
				var response = {
					data: data,
					headers: headers()
				}
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
