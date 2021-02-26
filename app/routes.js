var app =  angular.module('mfoApp', ['ngRoute', 'datatables']);
app.config(['$routeProvider',
    function($routeProvider) {
        $routeProvider.
	        when('/', {
	            templateUrl: 'templates/home.html',
	            controller: 'TournamentsController'
	        })
			.when('/admin', {
	            templateUrl: 'templates/admin.html',
	            controller: 'AdminController'
	        });
}]);
