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
			.when('/admin_tournament/:tournamentId', {
	            templateUrl: 'templates/admintournament.html',
	            controller: 'AdminTournamentController'
	        })
			.when('/tournament/:tournamentId', {
				templateUrl: 'templates/tournament.html',
				controller: 'TournamentController'
			});
}]);

app.filter('range', function() {
	return function(input, total) {
		total = parseInt(total);
		for (var i=0; i<total; i++)
		input.push(i);
		return input;
	}
});
