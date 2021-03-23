var URL = "https://mof-league.com"

if (window.location.hostname == 'localhost') {
    API_URL = "";
}

app.controller("MatchesHistoryController", function($scope, $http) {
    
    $scope.loadingMatches = true;
    $scope.matches = [];
    $scope.alertText = null;
    
    $scope.initRanking = function() {
        $scope.loadingMatches = true;

        $http.get(API_URL + '/api/history/matches.php').then( function ( response ) {
            $scope.matches = response.data.matches;
        }, function ( response ) {
            $scope.alertText = response.data.error;
        }).finally(function() {
            $scope.loadingMatches = false;
        });
    }

    $scope.closeAlert = function() {
        $scope.alertText = null;
    }

    $scope.initRanking();
});
