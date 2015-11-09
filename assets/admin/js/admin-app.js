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

ingotApp.config(function($stateProvider, $urlRouterProvider) {

    $urlRouterProvider.otherwise("/");
    $stateProvider
        .state('clickTests', {
            url: "/clickTests",
            templateUrl: INGOT_ADMIN.partials + "/click-groups.html"
        })
        .state('clickTests.list', {
            url: "/clickTests/all",
            templateUrl: INGOT_ADMIN.partials + "/click-groups.list.html",
            controller: 'clickGroups'
        } )
        .state('clickTests.edit', {
            url: "/clickTests/edit/:groupID",
            templateUrl: INGOT_ADMIN.partials + "/click-group.html",
            controller: 'clickGroup',
            stateChangeSuccess: function() {
                alert();
            }
        } )
        .state('state2', {
            url: "/state2",
            templateUrl: INGOT_ADMIN.partials + "/state2.html"
        })
        .state('state2.list', {
            url: "/list",
            templateUrl: INGOT_ADMIN.partials + "/state2.list.html",
            controller: function($scope) {
                $scope.things = ["A", "Set", "Of", "Things"];
            }
        });


});



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

ingotApp.controller( 'clickGroup', ['$scope', '$http', '$stateParams', '$rootScope', function( $scope, $http, $stateParams, $rootScope ) {
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
        $scope.submit = function( data ){
            $http({
                method: 'POST',
                headers: {
                    'X-WP-Nonce': INGOT_ADMIN.nonce
                },
                url: INGOT_ADMIN.api + 'test-group/' + groupID + '?context=admin',
                data: $scope.group
            } ).success(function(data) {
                $scope.group = data;
                swal({
                    title: INGOT_TRANSLATION.group_saved,
                    text: '',
                    type: "success",
                    confirmButtonText: INGOT_TRANSLATION.close
                });
            } ).error(function(){
                swal({
                    title: INGOT_I10N.fail,
                    text: INGOT_I10N.sorry,
                    type: "error",
                    confirmButtonText: INGOT.close
                });
            })
        };

        $scope.HTML = function( key ) {
            $scope.myHTML =
                'I am an <code>HTML</code>string with ' +
                '<a href="#">links!</a> and other <em>stuff</em>';
        }

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

        $scope.addNewTest = function(e) {
            //make ID a random string so it will be treated as new by API
            var id = Math.random().toString(36).substring(7);
            $scope.group.tests[ id ] = {'ID':id};
        };


    }).error(function(data, status, headers, config) {
        console.log( data );
    });

    $rootScope.$on('$viewContentLoaded',
        function( event){

        });

}]);


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

ingotApp.config(['$translateProvider', function ($translateProvider) {
    //@todo not make always english
    $translateProvider
        .translations('en', INGOT_TRANSLATION)
        .preferredLanguage('en')
        .useSanitizeValueStrategy('escape');
}]);



/**
 * Hide an element
 *
 * @param el
 */
function hide( el ){
    jQuery( el ).css( 'visibility', 'hidden' ).attr( 'aria-hidden', 'true' ).hide();
}

/**
 * Show an element
 *
 * @param el
 */
function show( el ){
    jQuery( el ).css( 'visibility', 'visible' ).attr( 'aria-hidden', 'false' ).show();
}
