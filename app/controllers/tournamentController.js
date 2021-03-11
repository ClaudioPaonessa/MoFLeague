var URL = "https://mof-league.com"

app.controller("TournamentController", function($scope, $routeParams, $http) {
    
    var tournamentId = $routeParams.tournamentId;
    $scope.loadingTournament = true;
    $scope.matches = []
    
    $scope.initTournament = function() {
        $scope.loadingTournament = true;

        $http.get(API_URL + '/api/tournament/tournament.php/' + tournamentId).then( function ( response ) {
            $scope.tournamentName = response.data.tournamentName
            $scope.matches = response.data.currentMatches;
            $scope.currentRound = response.data.currentRoundId;
        }, function ( response ) {
            // TODO: handle the error somehow
        }).finally(function() {
            $scope.loadingTournament = false;
        });
    }

    $scope.recordMatchResult = function(matchId, player1GamesWon, player2GamesWon) {
        let matchResult = {
            player1GamesWon: player1GamesWon,
            player2GamesWon: player2GamesWon
        };
        
        $http.post(API_URL + '/api/match/matchRecordResult.php/' + matchId, matchResult).then( function ( response ) {
            $scope.initTournament();
        }, function ( response ) {
            // TODO: handle the error somehow
        }).finally(function() {

        });
    }

    $scope.revokeResult = function(matchId) {
        $http.get(API_URL + '/api/match/matchRevokeResult.php/' + matchId).then( function ( response ) {
            $scope.initTournament();
        }, function ( response ) {
            // TODO: handle the error somehow
        }).finally(function() {
            
        });
    }

    $scope.acceptResult = function(matchId) {
        $http.get(API_URL + '/api/match/matchAcceptResult.php/' + matchId).then( function ( response ) {
            $scope.initTournament();
        }, function ( response ) {
            // TODO: handle the error somehow
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

    $scope.initTournament();
});
