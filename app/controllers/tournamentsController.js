var URL = "https://mof-league.com"

app.controller("TournamentsController", function($scope, $http) {
    
    $scope.result = []
    $scope.alertText = null;
    
    $scope.initTournaments = function() {
        $scope.loadingTournaments = true;

        $http.get(API_URL + '/api/tournament/Tournaments').then( function ( response ) {
            $scope.result = response.data.records;
        }, function ( response ) {
            $scope.alertText = response.data.error;
        }).finally(function() {
            $scope.loadingTournaments = false;
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
        return convertDate(tournament.startDate) <= now() & convertDate(tournament.endDate) >= now();
    }
    
    $scope.filterFuture = function(tournament) {
        return convertDate(tournament.startDate) > now();
    }

    $scope.filterPast = function(tournament) {
        return convertDate(tournament.startDate) < now() & convertDate(tournament.endDate) < now();
    }

    $scope.closeAlert = function() {
        $scope.alertText = null;
    }

    $scope.initTournaments();
});
