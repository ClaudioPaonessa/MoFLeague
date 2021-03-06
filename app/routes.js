var app =  angular.module('mfoApp', ['ngRoute', 'datatables', 'angular.filter', 'ui.bootstrap']);

app.config(['$routeProvider',
    function($routeProvider) {
        $routeProvider.
			when('/', {
				templateUrl: 'templates/home.html',
				controller: 'HomeController'
			})
	        .when('/tournaments', {
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
			.when('/admin_ranking/:tournamentId', {
				templateUrl: 'templates/adminranking.html',
				controller: 'AdminRankingController'
			})
			.when('/tournament/:tournamentId', {
				templateUrl: 'templates/tournament.html',
				controller: 'TournamentController'
			})
			.when('/ranking/:tournamentId', {
				templateUrl: 'templates/ranking.html',
				controller: 'RankingController'
			})
			.when('/rules', {
				templateUrl: 'templates/rules.html',
				controller: 'RulesController'
			})
			.when('/pool/:tournamentId', {
				templateUrl: 'templates/pool.html',
				controller: 'PoolController'
			})
			.when('/participantPool/:tournamentId', {
				templateUrl: 'templates/participantPool.html',
				controller: 'ParticipantPoolController'
			})
			.when('/profile/:accountId', {
				templateUrl: 'templates/profile.html',
				controller: 'ProfileController'
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
