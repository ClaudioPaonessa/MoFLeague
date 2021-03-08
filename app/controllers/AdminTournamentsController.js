var API_URL = "https://mof-league.com";

if (window.location.hostname == 'localhost') {
    API_URL = "";
}

app.controller('AdminTournamentsController', function($scope, $http, DTOptionsBuilder, DTColumnBuilder){
    $scope.dtOptions = DTOptionsBuilder.newOptions().withDOM('lfrtip');
    
    $scope.loading_tournaments = false;
    $scope.selected_tournament_id = -1;

    $scope.filterAccounts = {$: undefined};
    $scope.filterParticipants = {$: undefined};

    $scope.setFilterAccounts = function(){
        $scope.filterAccounts = {};
        $scope.filterAccounts['$'] = $scope.searchTextAccounts;
    }

    $scope.setFilterParticipants = function(){
        $scope.filterParticipants = {};
        $scope.filterParticipants['$'] = $scope.searchTextParticipants;
    }
    
    $scope.initTournaments = function() {
        $scope.loading_tournaments = true;

        $http.get(API_URL + '/api/tournament/Tournaments').then( function ( response ) {
            $scope.result = response.data.records;
        }, function ( response ) {
            // TODO: handle the error somehow
        }).finally(function() {
            $scope.loading_tournaments = false;
        });
    };

    $scope.initSets = function() {
        $http.get(API_URL + '/api/cards/Sets').then( function ( response ) {
            $scope.sets = response.data.records;
        }, function ( response ) {
            // TODO: handle the error somehow
        }).finally(function() {

        });
    }

    $scope.createTournament = function() {
        $http.post(API_URL + '/api/admin/CreateTournament.php', $scope.tournament).then( function ( response ) {
            $scope.initTournaments();
        }, function ( response ) {
            // TODO: handle the error somehow
        }).finally(function() {
            
        });
    }

    $scope.deleteSelectedTournament = function() {
        $http.get(API_URL + '/api/admin/DeleteTournament.php/' + $scope.selected_tournament_id).then( function ( response ) {
            $scope.initTournaments();
            $("#deleteConfirm").modal("hide");
        }, function ( response ) {
            // TODO: handle the error somehow
        }).finally(function() {
            
        });
    }

    $scope.manageParticipantsModal = function(row) {
        $scope.selected_tournament_id = row.tournament_id;
        $scope.selectedTournamentName = row.tournament_name;

        $scope.loadAllAccounts();
        $scope.loadCurrentParticipants();

        $("#manageParticipants").modal("show");
    }

    $scope.loadCurrentParticipants = function() {
        $http.get(API_URL + '/api/tournament/TournamentParticipants.php/' + $scope.selected_tournament_id).then( function ( response ) {
            $scope.participants = response.data.records;
        }, function ( response ) {
            // TODO: handle the error somehow
        }).finally(function() {
            
        });
    }

    $scope.loadAllAccounts = function() {
        $http.get(API_URL + '/api/tournament/TournamentAccounts.php/' + $scope.selected_tournament_id).then( function ( response ) {
            $scope.accounts = response.data.records;
        }, function ( response ) {
            // TODO: handle the error somehow
        }).finally(function() {
            
        });
    }

    $scope.addParticipant = function(accountId) {
        $http.get(API_URL + '/api/admin/AddParticipant.php/' + $scope.selected_tournament_id + '/' + accountId).then( function ( response ) {
            $scope.loadAllAccounts();
            $scope.loadCurrentParticipants();
        }, function ( response ) {
            // TODO: handle the error somehow
        }).finally(function() {
            
        });
    }

    $scope.removeParticipant = function(accountId) {
        $http.get(API_URL + '/api/admin/RemoveParticipant.php/' + $scope.selected_tournament_id + '/' + accountId).then( function ( response ) {
            $scope.loadAllAccounts();
            $scope.loadCurrentParticipants();
        }, function ( response ) {
            // TODO: handle the error somehow
        }).finally(function() {
            
        });
    }

    $scope.deleteModalTournament = function(row) {
        $scope.selected_tournament_id = row.tournament_id;
        $scope.delete_modal_text = "Do you really want to delete the tournament " + row.tournament_name + "?";

        $("#deleteConfirm").modal("show");
    }

    $scope.initTournaments();
    $scope.initSets();
    
});