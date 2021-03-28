var URL = "https://mof-league.com"

if (window.location.hostname == 'localhost') {
    API_URL = "";
}

app.controller("RankingController", function($scope, $routeParams, $http) {
    
    $scope.tournamentId = $routeParams.tournamentId;
    $scope.loadingRanking = true;
    $scope.ranking = [];
    $scope.alertText = null;
    
    $scope.initRanking = function() {
        $scope.loadingRanking = true;

        $http.get(API_URL + '/api/tournament/tournamentRanking.php/' + $scope.tournamentId).then( function ( response ) {
            $scope.tournamentName = response.data.tournamentName;
            $scope.liveRanking = response.data.liveRanking;
            $scope.initialRanking = response.data.initialRanking;
            $scope.liveRoundName = response.data.liveRoundName;
            $scope.completedRounds = response.data.completedRounds;
        }, function ( response ) {
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
