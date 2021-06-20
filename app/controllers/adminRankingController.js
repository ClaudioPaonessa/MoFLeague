var URL = "https://mof-league.com"

if (window.location.hostname == 'localhost') {
    API_URL = "";
}

app.controller("AdminRankingController", function($scope, $routeParams, $http, $window) {
    
    $scope.tournamentId = $routeParams.tournamentId;
    $scope.loadingRanking = true;
    $scope.alertText = null;
    
    $scope.initRanking = function() {
        $scope.loadingRanking = true;

        $http.get(API_URL + '/api/admin/tournamentRankingAdmin.php/' + $scope.tournamentId).then( function ( response ) {
            $scope.tournamentName = response.data.tournamentName;
            $scope.liveRanking = response.data.liveRanking;
            $scope.initialRanking = response.data.initialRanking;
            $scope.liveRoundName = response.data.liveRoundName;
            $scope.completedRounds = response.data.completedRounds;
        }, function ( response ) {
            if (response.status == 401) {
                $window.location.href = '/auth/login.php';
            }

            $scope.alertText = response.data.error;
        }).finally(function() {
            $scope.loadingRanking = false;
        });
    }

    $scope.closeAlert = function() {
        $scope.alertText = null;
    }

    $scope.initRanking();
});
