var app =  angular.module('mfoApp',['ngRoute']);
app.config(['$routeProvider',
    function($routeProvider) {
        $routeProvider.
	        when('/', {
	            templateUrl: 'templates/login.html',
	            controller: 'AccountController'
	        })
            .when('/register', {
	            templateUrl: 'templates/register.html',
	            controller: 'AccountController'
	        });
}]);
