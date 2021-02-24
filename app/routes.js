var app =  angular.module('mfoApp',['ngRoute']);
app.config(['$routeProvider',
    function($routeProvider) {
        $routeProvider.
	        when('/', {
	            templateUrl: 'templates/register.html',
	            controller: 'AccountController'
	        });
}]);
