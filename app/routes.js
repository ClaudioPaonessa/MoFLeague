var app =  angular.module('mfoApp',['ngRoute']);
app.config(['$routeProvider',
    function($routeProvider) {
        $routeProvider.
	        when('/', {
                url='index.html',
	            templateUrl: 'templates/register.html',
	            controller: 'AccountController'
	        })
            .when('/home', {
                url='app.html',
	            templateUrl: 'templates/start.html',
	            controller: 'AccountController'
	        });
}]);
