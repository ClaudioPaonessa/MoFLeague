var URL = "https://mof-league.com"

if (window.location.hostname == 'localhost') {
    API_URL = "";
}

app.controller("TournamentsController", function($scope, $http, $window) {
    
    $scope.result = []
    $scope.alertText = null;
    
    $scope.initTournaments = function() {
        $scope.loadingTournaments = true;

        $http.get(API_URL + '/api/tournament/tournaments').then( function ( response ) {
            $scope.result = response.data.records;
        }, function ( response ) {
            if (response.status == 401) {
                $window.location.href = '/auth/login.php';
            }

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
        date.setUTCMilliseconds(0);
        return date.getTime();
    }  

    convertDate = function(d) {
        var date = new Date(d);
        date.setHours(0);
        date.setMinutes(0);
        date.setSeconds(0);

        return date.getTime();
    }

    $scope.filterRunning = function(tournament) {
        return tournament.uncompletedRounds > 0 && convertDate(tournament.startDate) <= now();
    }
    
    $scope.filterFuture = function(tournament) {
        return (tournament.uncompletedRounds > 0 && convertDate(tournament.startDate) > now()) || tournament.roundCount == 0;
    }

    $scope.filterPast = function(tournament) {
        return tournament.uncompletedRounds == 0;
    }

    $scope.closeAlert = function() {
        $scope.alertText = null;
    }

    $scope.initTournaments();
});
