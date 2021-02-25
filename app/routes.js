var app =  angular.module('mfoApp', ['ngRoute']);
app.config(['$routeProvider',
    function($routeProvider) {
        $routeProvider.
	        when('/', {
	            templateUrl: 'templates/home.html',
	            controller: 'TournamentsController'
	        })
            .when('/cards', {
	            templateUrl: 'templates/cards.html',
	            controller: 'CardsController'
	        });
}]);
