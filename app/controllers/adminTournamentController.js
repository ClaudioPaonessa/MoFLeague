var URL = "https://mof-league.com"

if (window.location.hostname == 'localhost') {
    API_URL = "";
}

app.controller("AdminTournamentController", function($scope, $routeParams, $http) {
    
    $scope.tournamentId = $routeParams.tournamentId;
    $scope.loadingTournament = true;
    $scope.matches = []
    $scope.alertText = null;
    
    $scope.initTournament = function() {
        $scope.loadingTournament = true;

        $http.get(API_URL + '/api/admin/tournamentDashboard.php/' + $scope.tournamentId).then( function ( response ) {
            $scope.tournamentName = response.data.tournamentName
            $scope.matches = response.data.currentMatches;
            $scope.allMatches = response.data.tournamentMatches;
            $scope.rounds = response.data.rounds;
            $scope.currentRound = response.data.currentRoundId;
            $scope.numberOfRounds = response.data.numberOfRounds;
            $scope.roundsFinished = response.data.roundsFinished;
        }, function ( response ) {
            $scope.alertText = response.data.error;
        }).finally(function() {
            $scope.loadingTournament = false;
        });
    }

    $scope.changeRoundCompleted = function(roundId, value) {
        let completionStatus = {
            status: value
        };

        $http.post(API_URL + '/api/admin/changeRoundCompletion.php/' + roundId, completionStatus).then( function ( response ) {
            $scope.initTournament();
        }, function ( response ) {
            $scope.alertText = response.data.error;
        }).finally(function() {

        });
    }

    $scope.setActiveRound = function(roundId) {
        let completionStatus = {
            roundId: roundId
        };

        $http.post(API_URL + '/api/admin/setActiveRound.php/' + $scope.tournamentId, completionStatus).then( function ( response ) {
            $scope.initTournament();
        }, function ( response ) {
            $scope.alertText = response.data.error;
        }).finally(function() {

        });
    }

    $scope.resetActiveRound = function() {
        $http.get(API_URL + '/api/admin/resetActiveRound.php/' + $scope.tournamentId).then( function ( response ) {
            $scope.initTournament();
        }, function ( response ) {
            $scope.alertText = response.data.error;
        }).finally(function() {

        });
    }

    $scope.filterPlayedAndConfirmed = function(match) {
        return match.player1GamesWon != null && Boolean(match.resultConfirmed);
    }

    $scope.filterPlayedNotConfirmed = function(match) {
        return match.player1GamesWon != null && !Boolean(match.resultConfirmed);
    }

    $scope.filterNotPlayed = function(match) {
        return match.player1GamesWon == null;
    }

    $scope.closeAlert = function() {
        $scope.alertText = null;
    }

    $scope.initTournament();
});
