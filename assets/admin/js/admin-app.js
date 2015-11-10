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
    'ngAria'
] )
    .run( function() {

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
ingotApp.controller( 'clickGroups', ['$scope', '$http', function( $scope, $http ) {
    $http({
        method: 'GET',
        url: INGOT_ADMIN.api + 'test-group'

    }).success( function( data, status, headers, config ) {
        console.log( data );
        $scope.groups = data;
    }).error(function(data, status, headers, config) {
        console.log( data );
    });
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
                    url: INGOT_ADMIN.api + 'test-group/' + groupID,
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
ingotApp.controller( 'clickGroup', ['$scope', '$http', '$stateParams', '$rootScope', '$state', function( $scope, $http, $stateParams, $rootScope, $state ) {
    if( 'clickTests.new' == $state.current.name ) {
        $scope.group = {
            click_type_options : INGOT_ADMIN.click_type_options
        };
    }else{
        var groupID = $stateParams.groupID;
        $http({
            method: 'GET',
            url: INGOT_ADMIN.api + 'test-group/' + groupID + '?context=admin'

        }).success( function( data, status, headers, config ) {

            $scope.group = data;
            $scope.isButtonColor = function() {
                if( 'button_color' == $scope.group.click_type ) {
                    return true;
                }
            };
            $scope.isButton =function() {
                if( 'button' == $scope.group.click_type ) {
                    return true;
                }
            };

            $scope.isLink = function() {
                if( 'link' == $scope.group.click_type ) {
                    return true;
                }
            };
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

        $scope.submit = function( data ){
            var url;
            if( 'clickTests.new' == $state.current.name ) {
                url =INGOT_ADMIN.api + 'test-group/?context=admin';
            }else{
                url = INGOT_ADMIN.api + 'test-group/' + groupID + '?context=admin';
            }

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
                    $state.go('clickTests.edit' ).toParams({
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
    $http({
        method: 'GET',
        url: INGOT_ADMIN.api + 'price-group'

    }).success( function( data, status, headers, config ) {
        console.log( data );
        $scope.groups = data;
    }).error(function(data, status, headers, config) {
        console.log( data );
    });
}]);

//controller for creating/editing a price group
ingotApp.controller( 'priceGroup', ['$scope', '$http', '$stateParams', '$rootScope', '$state', function( $scope, $http, $stateParams, $rootScope, $state ) {
    if( 'priceGroup.new' == $state.current.name ) {
        $scope.group = {
            price_type_options : INGOT_ADMIN.price_type_options
        };
    }else{
        var groupID = $stateParams.groupID;
        $http({
            method: 'GET',
            url: INGOT_ADMIN.api + 'price-group/' + groupID + '?context=admin'

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
   //@todo wtf are we doing here?
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
    var url =  INGOT_ADMIN.api + 'settings';
    $http({
        method: 'GET',
        url:url,
        headers: {
            'X-WP-Nonce': INGOT_ADMIN.nonce
        },

    }).success( function( data, status, headers, config ) {
        $scope.settings = prepare( data );
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
            $scope.settings = prepare( data );
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

    function prepare( data ){
        jQuery.each( data, function( i, setting ) {
            if( 1 == setting ) {
                data.setting == 'on';
            }
        });

        return data;
    }
}]);

/**
 * Click Factory
 *
 * @since 2.0.0
 *
 * @todo make work, use, etc.
 */
ingotApp.factory('click',function($resource){
    return $resource(INGOT_ADMIN.api + 'group/:ID',{
        ID:'@id'
    },{
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
    });
});
