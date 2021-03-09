var URL = "https://mof-league.com"

app.controller("TournamentsController", function($scope, $http) {
    
    $scope.result = []
    
    $scope.initTournaments = function() {
        $scope.loading_tournaments = true;

        $http.get(API_URL + '/api/tournament/Tournaments').then( function ( response ) {
            $scope.result = response.data.records;
        }, function ( response ) {
            // TODO: handle the error somehow
        }).finally(function() {
            $scope.loading_tournaments = false;
        });
    }

    now = function() {
        var date = new Date();
        date.setHours(0);
        date.setMinutes(0);
        date.setSeconds(0);
        return date;
    }  

    convertDate = function(d) {
        var date = new Date(d);
        date.setHours(0);
        date.setMinutes(0);
        date.setSeconds(0);

        return date;
    }

    $scope.filterRunning = function(tournament) {
        return convertDate(tournament.start_date) <= now() & convertDate(tournament.end_date) >= now();
    }
    
    $scope.filterFuture = function(tournament) {
        return convertDate(tournament.start_date) > now();
    }

    $scope.filterPast = function(tournament) {
        return convertDate(tournament.start_date) < now() & convertDate(tournament.end_date) < now();
    }

    $scope.initTournaments();
});
