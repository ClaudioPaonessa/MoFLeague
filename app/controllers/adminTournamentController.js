var URL = "https://mof-league.com"

if (window.location.hostname == 'localhost') {
    API_URL = "";
}

app.controller("AdminTournamentController", function($scope, $routeParams, $http) {
    
    $scope.tournamentId = $routeParams.tournamentId;
    $scope.loadingTournament = true;
    $scope.matches = [];
    $scope.participantPacks = [];
    $scope.newPack = [];
    $scope.alertText = null;

    $scope.isActive = false;
    
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
            $scope.poolStats = response.data.poolStats;
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

    $scope.changeTradesNextRound = function(roundId, value) {
        let completionStatus = {
            status: value
        };

        $http.post(API_URL + '/api/admin/changeTradesNextRound.php/' + roundId, completionStatus).then( function ( response ) {
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

    $scope.hideRoundRanking = function(roundId) {
        $http.get(API_URL + '/api/admin/hideRoundRanking.php/' + roundId).then( function ( response ) {
            $scope.initTournament();
        }, function ( response ) {
            $scope.alertText = response.data.error;
        }).finally(function() {

        });
    }

    $scope.displayRoundRanking = function(roundId) {
        $http.get(API_URL + '/api/admin/displayRoundRanking.php/' + roundId).then( function ( response ) {
            $scope.initTournament();
        }, function ( response ) {
            $scope.alertText = response.data.error;
        }).finally(function() {

        });
    }

    $scope.loadPacksForRound = function() {
        $scope.loadingTournament = true;

        $http.get(API_URL + '/api/admin/getPacksForRound.php/' + $scope.selectedRound).then( function ( response ) {
            $scope.participantPacks = response.data.participantPacks
        }, function ( response ) {
            $scope.alertText = response.data.error;
        }).finally(function() {
            $scope.loadingTournament = false;
        });
    }

    $scope.addPack = function(accountId, idx) {
        let packInfo = {
            packString: $scope.newPack.packString[idx]
        };

        $http.post(API_URL + '/api/admin/addPack.php/' + $scope.selectedRound + '/' + accountId, packInfo).then( function ( response ) {
            $scope.loadPacksForRound();
        }, function ( response ) {
            $scope.alertText = response.data.error;
        }).finally(function() {

        });
    }

    $scope.addPacksModal = function(round) {
        $scope.selectedRound = round.roundId;
        $scope.loadPacksForRound();

        $("#addPacks").modal("show");
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
