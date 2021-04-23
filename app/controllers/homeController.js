var URL = "https://mof-league.com"

if (window.location.hostname == 'localhost') {
    API_URL = "";
}

app.controller("HomeController", function($scope, $http, $window) {
    
    $scope.result = []
    $scope.alertText = null;
    
    $scope.initTournaments = function() {
        $http.get(API_URL + '/api/tournament/tournaments').then( function ( response ) {
            $scope.result = response.data.records;

            var redirected = false;

            // redirect to tournament if participating
            $scope.result.forEach(function(tournament) {
                if ($scope.filterRunning(tournament) && tournament.youParticipate) {
                    $window.location.href = '/#!/tournament/' + tournament.tournamentId;
                    redirected = true;
                }
            });

            if (!redirected) {
                $window.location.href = '/#!/tournaments';
            }
        }, function ( response ) {
            if (response.status == 401) {
                $window.location.href = '/auth/login.php';
            }

            $scope.alertText = response.data.error;
        }).finally(function() {
            
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

    $scope.closeAlert = function() {
        $scope.alertText = null;
    }

    $scope.initTournaments();
});
