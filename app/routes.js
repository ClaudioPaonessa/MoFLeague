var app =  angular.module('mfoApp', ['ngRoute', 'datatables']);
app.config(['$routeProvider',
    function($routeProvider) {
        $routeProvider.
	        when('/', {
	            templateUrl: 'templates/home.html',
	            controller: 'TournamentsController'
	        })
			.when('/admin_sets', {
	            templateUrl: 'templates/admin_sets.html',
	            controller: 'AdminSetsController'
	        })
			.when('/admin_tournaments', {
	            templateUrl: 'templates/admin_tournaments.html',
	            controller: 'AdminTournamentsController'
	        })
			.when('/tournament/:tournament_id', {
				templateUrl: 'templates/tournament.html',
				controller: 'TournamentController'
			});
}]);
