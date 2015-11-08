var ingotApp = angular.module( 'ingotApp', [
    'ngRoute'
] ).config(function($routeProvider,$locationProvider) {
    $locationProvider.html5Mode(false).hashPrefix('!');
    $routeProvider
        .when('/click-groups', {
            templateUrl :INGOT_ADMIN.partials + 'click-groups.html',
            controller  : 'clickGroups'
        } ).when('/click-groups/:groupID', {
            templateUrl :INGOT_ADMIN.partials + 'click-group.html',
            controller  : 'clickGroups'
        });


}).run(function( $location, $scope ) {
    console.log( $location.path() );
});


ingotApp.controller( 'clickGroups', ['$scope', '$http', function( $scope, $http ) {
    $http({
        method: 'GET',
        url: INGOT_ADMIN.api_url + 'test-group'

    }).success( function( data, status, headers, config ) {
            console.log( $scope.api );
            console.log( data );
            $scope.groups = data;
        }).error(function(data, status, headers, config) {
        console.log( data );
    });
}]);

ingotApp.controller( 'clickGroup', ['$scope', '$http', '$location', '$routeParams', function( $scope, $http, $routeParams ) {
    $http({
        method: 'GET',
        url: INGOT_ADMIN.api_url + 'test-group/' + '/' + $routeParams.groupID + '?context=admin'

    }).success( function( data, status, headers, config ) {
        console.log( data );
        $scope.group = data;
    }).error(function(data, status, headers, config) {
        console.log( data );
    });
}]);






