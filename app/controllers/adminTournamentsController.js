var API_URL = "https://mof-league.com";

if (window.location.hostname == 'localhost') {
    API_URL = "";
}

app.controller('AdminTournamentsController', function($scope, $http, DTOptionsBuilder, DTColumnBuilder){
    $scope.dtOptions = DTOptionsBuilder.newOptions().withDOM('lfrtip');

    $scope.loadingTournaments = false;
    $scope.selectedTournamentId = -1;

    $scope.filterAccounts = {$: undefined};
    $scope.filterParticipants = {$: undefined};

    $scope.alertText = null;

    $scope.setFilterAccounts = function(){
        $scope.filterAccounts = {};
        $scope.filterAccounts['$'] = $scope.searchTextAccounts;
    }

    $scope.setFilterParticipants = function(){
        $scope.filterParticipants = {};
        $scope.filterParticipants['$'] = $scope.searchTextParticipants;
    }

    $scope.initTournaments = function() {
        $scope.loadingTournaments = true;

        $http.get(API_URL + '/api/tournament/tournaments.php').then( function ( response ) {
            $scope.result = response.data.records;
        }, function ( response ) {
            $scope.alertText = response.data.error;
        }).finally(function() {
            $scope.loadingTournaments = false;
        });
    };

    $scope.initSets = function() {
        $http.get(API_URL + '/api/cards/sets.php').then( function ( response ) {
            $scope.sets = response.data.records;
        }, function ( response ) {
            $scope.alertText = response.data.error;
        }).finally(function() {

        });
    }

    $scope.createTournament = function() {
        $http.post(API_URL + '/api/admin/createTournament.php', $scope.tournament).then( function ( response ) {
            $scope.initTournaments();
        }, function ( response ) {
            $scope.alertText = response.data.error;
        }).finally(function() {

        });
    }

    $scope.deleteSelectedTournament = function() {
        $http.get(API_URL + '/api/admin/deleteTournament.php/' + $scope.selectedTournamentId).then( function ( response ) {
            $scope.initTournaments();
            $("#deleteConfirm").modal("hide");
        }, function ( response ) {
            $scope.alertText = response.data.error;
        }).finally(function() {

        });
    }

    $scope.manageParticipantsModal = function(row) {
        $scope.selectedTournamentId = row.tournamentId;
        $scope.selectedTournamentName = row.tournamentName;

        $scope.loadAllAccounts();
        $scope.loadCurrentParticipants();

        $("#manageParticipants").modal("show");
    }

    $scope.manageRoundsModal = function(row) {
        $scope.selectedTournamentId = row.tournamentId;
        $scope.selectedTournamentName = row.tournamentName;

        $scope.loadRounds();

        $("#manageRounds").modal("show");
    }

    $scope.createRound = function() {
        $http.post(API_URL + '/api/admin/createRound.php/' + $scope.selectedTournamentId, $scope.newRound).then( function ( response ) {
            $scope.loadRounds();
            $scope.initTournaments();
        }, function ( response ) {
            $scope.alertText = response.data.error;
        }).finally(function() {

        });
    }

    $scope.removeRound = function(roundId) {
        $http.get(API_URL + '/api/admin/deleteRound.php/' + roundId).then( function ( response ) {
            $scope.loadRounds();
            $scope.initTournaments();
        }, function ( response ) {
            $scope.alertText = response.data.error;
        }).finally(function() {

        });
    }

    $scope.managePairingsModal = function(round) {
        $scope.selectedRound = round;

        $scope.loadPairings(round.roundId);
        $scope.loadCurrentParticipants();

        $("#managePairings").modal("show");
    }

    $scope.createPairing = function() {
        $http.post(API_URL + '/api/admin/createPairing.php/' + $scope.selectedRound.roundId, $scope.newPairing).then( function ( response ) {
            $scope.loadRounds();
            $scope.loadPairings($scope.selectedRound.roundId);
            $scope.initTournaments();
        }, function ( response ) {
            $scope.alertText = response.data.error;
        }).finally(function() {

        });
    }

    $scope.removePairing = function(matchId) {
        $http.get(API_URL + '/api/admin/deletePairing.php/' + matchId).then( function ( response ) {
            $scope.loadRounds();
            $scope.loadPairings($scope.selectedRound.roundId);
            $scope.initTournaments();
        }, function ( response ) {
            $scope.alertText = response.data.error;
        }).finally(function() {

        });
    }


    $scope.loadCurrentParticipants = function() {
        $http.get(API_URL + '/api/tournament/tournamentParticipants.php/' + $scope.selectedTournamentId).then( function ( response ) {
            $scope.participants = response.data.records;
        }, function ( response ) {
            $scope.alertText = response.data.error;
        }).finally(function() {

        });
    }

    $scope.loadAllAccounts = function() {
        $http.get(API_URL + '/api/tournament/tournamentAccounts.php/' + $scope.selectedTournamentId).then( function ( response ) {
            $scope.accounts = response.data.records;
        }, function ( response ) {
            $scope.alertText = response.data.error;
        }).finally(function() {

        });
    }

    $scope.loadRounds = function() {
        $http.get(API_URL + '/api/tournament/tournamentRounds.php/' + $scope.selectedTournamentId).then( function ( response ) {
            $scope.rounds = response.data.records;
        }, function ( response ) {
            $scope.alertText = response.data.error;
        }).finally(function() {

        });
    }

    $scope.loadPairings = function(roundId) {
        $http.get(API_URL + '/api/tournament/tournamentRoundPairings.php/' + roundId).then( function ( response ) {
            $scope.pairings = response.data.records;
        }, function ( response ) {
            $scope.alertText = response.data.error;
        }).finally(function() {

        });
    }

    $scope.addParticipant = function(accountId) {
        $http.get(API_URL + '/api/admin/addParticipant.php/' + $scope.selectedTournamentId + '/' + accountId).then( function ( response ) {
            $scope.loadAllAccounts();
            $scope.loadCurrentParticipants();
            $scope.initTournaments();
        }, function ( response ) {
            $scope.alertText = response.data.error;
        }).finally(function() {

        });
    }

    $scope.removeParticipant = function(accountId) {
        $http.get(API_URL + '/api/admin/removeParticipant.php/' + $scope.selectedTournamentId + '/' + accountId).then( function ( response ) {
            $scope.loadAllAccounts();
            $scope.loadCurrentParticipants();
            $scope.initTournaments();
        }, function ( response ) {
            $scope.alertText = response.data.error;
        }).finally(function() {

        });
    }

    $scope.shuffleParticipants = function() {
        $http.get(API_URL + '/api/admin/initializeParticipantRanks.php/' + $scope.selectedTournamentId).then( function ( response ) {
            $scope.loadAllAccounts();
            $scope.loadCurrentParticipants();
        }, function ( response ) {
            $scope.alertText = response.data.error;
        }).finally(function() {

        });
    }

    $scope.deleteModalTournament = function(row) {
        $scope.selectedTournamentId = row.tournamentId;
        $scope.deleteModalText = "Do you really want to delete the tournament " + row.tournamentName + "?";

        $("#deleteConfirm").modal("show");
    }

    $scope.closeAlert = function() {
        $scope.alertText = null;
    }

    $scope.initTournaments();
    $scope.initSets();

});
