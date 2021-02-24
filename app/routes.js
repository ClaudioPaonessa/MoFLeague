var app =  angular.module('mfoApp', ['ngRoute']);
app.config(['$routeProvider',
    function($routeProvider) {
        $routeProvider.
	        when('/', {
	            templateUrl: 'templates/home.html',
	            controller: 'TournamentsController'
	        })
            .when('/pool', {
	            templateUrl: 'templates/pool.html',
	            controller: 'PoolController'
	        });
}]);
