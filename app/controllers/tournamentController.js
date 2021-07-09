var URL = "https://mof-league.com"

if (window.location.hostname == 'localhost') {
    API_URL = "";
}

app.controller("TournamentController", function($scope, $routeParams, $http, $window) {
    
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
            $scope.allMatches = response.data.tournamentMatches;
            $scope.rounds = response.data.rounds;
            $scope.currentRound = response.data.currentRoundId;
            $scope.numberOfRounds = response.data.numberOfRounds;
            $scope.roundsFinished = response.data.roundsFinished;
            $scope.receivedAchievements = response.data.receivedAchievements;
            $scope.receivedAchievementsPoint = response.data.receivedAchievementsPoint;
        }, function ( response ) {
            if (response.status == 401) {
                $window.location.href = '/auth/login.php';
            }

            $scope.alertText = response.data.error;
        }).finally(function() {
            $scope.loadingTournament = false;
        });
    }

    $scope.recordMatchResult = function(matchId, player1GamesWon, player2GamesWon) {
        let matchResult = {
            player1GamesWon: player1GamesWon,
            player2GamesWon: player2GamesWon,
            tradesP1toP2: $scope.tradesP1toP2,
            tradesP2toP1: $scope.tradesP2toP1,
            achievementsP1: $scope.achievementsP1,
            achievementsP2: $scope.achievementsP2,
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

    $scope.loadSelectableAchievementsP1 = function(playerId) {
        $http.get(API_URL + '/api/tournament/tournamentSelectableAchievements.php/' + $scope.tournamentId + '/' + playerId).then( function ( response ) {
            $scope.selectableAchievementsP1 = response.data.selectable;
        }, function ( response ) {
            $scope.alertText = response.data.error;
        }).finally(function() {
            
        });
    }

    $scope.loadSelectableAchievementsP2 = function(playerId) {
        $http.get(API_URL + '/api/tournament/tournamentSelectableAchievements.php/' + $scope.tournamentId + '/' + playerId).then( function ( response ) {
            $scope.selectableAchievementsP2 = response.data.selectable;
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

        $scope.achievementsP1 = [];
        $scope.achievementsP2 = [];

        $scope.loadSelectableAchievementsP1(row.playerId1);
        $scope.loadSelectableAchievementsP2(row.playerId2);

        $("#reportMatchResult").modal("show");
    }

    $scope.addTradeP1toP2 = function(cardId, cardName) {
        $scope.tradesP1toP2.push({id: cardId, name: cardName});
    }

    $scope.removeTradeCardP1 = function(idx) {
        $scope.tradesP2toP1.splice(idx, 1);
    }

    $scope.addTradeP2toP1 = function(cardId, cardName) {
        $scope.tradesP2toP1.push({id: cardId, name: cardName});
    }

    $scope.removeTradeCardP2 = function(idx) {
        $scope.tradesP1toP2.splice(idx, 1);
    }

    $scope.addAchievementToP1 = function(achievementId, achievementName) {
        $scope.achievementsP1.push({id: achievementId, name: achievementName})
    }

    $scope.removeAchievementP1 = function(idx) {
        $scope.achievementsP1.splice(idx, 1);
    }

    $scope.addAchievementToP2 = function(achievementId, achievementName) {
        $scope.achievementsP2.push({id: achievementId, name: achievementName})
    }

    $scope.removeAchievementP2 = function(idx) {
        $scope.achievementsP2.splice(idx, 1);
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
