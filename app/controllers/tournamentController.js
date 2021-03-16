var URL = "https://mof-league.com"

if (window.location.hostname == 'localhost') {
    API_URL = "";
}

app.controller("TournamentController", function($scope, $routeParams, $http) {
    
    $scope.tournamentId = $routeParams.tournamentId;
    $scope.loadingTournament = true;
    $scope.matches = [];
    $scope.alertText = null;
    $scope.cards = [];
    
    $scope.initTournament = function() {
        $scope.loadingTournament = true;

        $http.get(API_URL + '/api/tournament/tournament.php/' + $scope.tournamentId).then( function ( response ) {
            $scope.tournamentName = response.data.tournamentName;
            $scope.matches = response.data.currentMatches;
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

    $scope.recordMatchResult = function(matchId, player1GamesWon, player2GamesWon) {
        let matchResult = {
            player1GamesWon: player1GamesWon,
            player2GamesWon: player2GamesWon
        };
        
        $http.post(API_URL + '/api/match/matchRecordResult.php/' + matchId, matchResult).then( function ( response ) {
            $scope.initTournament();
            $("#reportMatchResult").modal("hide");
        }, function ( response ) {
            $scope.alertText = response.data.error;
        }).finally(function() {

        });
    }

    $scope.revokeResult = function(matchId) {
        $http.get(API_URL + '/api/match/matchRevokeResult.php/' + matchId).then( function ( response ) {
            $scope.initTournament();
        }, function ( response ) {
            $scope.alertText = response.data.error;
        }).finally(function() {
            
        });
    }

    $scope.acceptResult = function(matchId) {
        $http.get(API_URL + '/api/match/matchAcceptResult.php/' + matchId).then( function ( response ) {
            $scope.initTournament();
        }, function ( response ) {
            $scope.alertText = response.data.error;
        }).finally(function() {
            
        });
    }

    $scope.loadCards = function() {
        if ($scope.cards.length > 0) {
            return;
        }
        
        $http.get(API_URL + '/api/tournament/tournamentCardPool.php/' + $scope.tournamentId).then( function ( response ) {
            $scope.cards = response.data.cards;
        }, function ( response ) {
            $scope.alertText = response.data.error;
        }).finally(function() {
            
        });
    }

    $scope.reportMatchResultModal = function(row) {
        $scope.selectedMatchId = row.matchId;
        $scope.selectedMatchP1Name = row.p1DisplayName;
        $scope.selectedMatchP2Name = row.p2DisplayName;

        $scope.tradesP1toP2 = [];
        $scope.tradesP2toP1 = [];
        $scope.loadCards();
        

        $("#reportMatchResult").modal("show");
    }

    $scope.addTradeP1toP2 = function(cardId, cardName) {
        $scope.tradesP1toP2.push({id: cardId, name: cardName});
    }

    $scope.addTradeP2toP1 = function(cardId, cardName) {
        $scope.tradesP2toP1.push({id: cardId, name: cardName});
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
