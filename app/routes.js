var app =  angular.module('mfoApp', ['ngRoute', 'datatables']);
app.config(['$routeProvider',
    function($routeProvider) {
        $routeProvider.
	        when('/', {
	            templateUrl: 'templates/home.html',
	            controller: 'TournamentsController'
	        })
			.when('/admin_sets', {
	            templateUrl: 'templates/adminsets.html',
	            controller: 'AdminSetsController'
	        })
			.when('/admin_tournaments', {
	            templateUrl: 'templates/admintournaments.html',
	            controller: 'AdminTournamentsController'
	        })
			.when('/tournament/:tournamentId', {
				templateUrl: 'templates/tournament.html',
				controller: 'TournamentController'
			});
}]);
