var URL = "https://mof-league.com"

app.controller("TournamentController", function($scope, $routeParams, $http) {
    
    var tournamentId = $routeParams.tournament_id;
    $scope.loading_tournament = true;
    $scope.matches = []
    
    $scope.initTournament = function() {
        $scope.loading_tournament = true;

        $http.get(API_URL + '/api/tournament/Tournament.php/' + tournamentId).then( function ( response ) {
            $scope.tournamentName = response.data.tournamentName
            $scope.matches = response.data.currentMatches;
            $scope.currentRound = response.data.currentRoundId;
        }, function ( response ) {
            // TODO: handle the error somehow
        }).finally(function() {
            $scope.loading_tournament = false;
        });
    }

    $scope.recordMatchResult = function(matchId, player1GamesWon, player2GamesWon) {
        let matchResult = {
            player1GamesWon: player1GamesWon,
            player2GamesWon: player2GamesWon
        };
        
        $http.post(API_URL + '/api/match/MatchRecordResult.php/' + matchId, matchResult).then( function ( response ) {
            $scope.initTournament();
        }, function ( response ) {
            // TODO: handle the error somehow
        }).finally(function() {

        });
    }

    $scope.revokeResult = function(matchId) {
        $http.get(API_URL + '/api/match/MatchRevokeResult.php/' + matchId).then( function ( response ) {
            $scope.initTournament();
        }, function ( response ) {
            // TODO: handle the error somehow
        }).finally(function() {
            
        });
    }

    $scope.acceptResult = function(matchId) {
        $http.get(API_URL + '/api/match/MatchAcceptResult.php/' + matchId).then( function ( response ) {
            $scope.initTournament();
        }, function ( response ) {
            // TODO: handle the error somehow
        }).finally(function() {
            
        });
    }

    $scope.filterPlayedAndConfirmed = function(match) {
        return match.player_1_games_won != null && Boolean(match.result_confirmed);
    }

    $scope.filterPlayedNotConfirmed = function(match) {
        return match.player_1_games_won != null && !Boolean(match.result_confirmed);
    }

    $scope.filterNotPlayed = function(match) {
        return match.player_1_games_won == null;
    }

    $scope.initTournament();
});
